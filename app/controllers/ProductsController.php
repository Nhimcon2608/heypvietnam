<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\ProductCustomField;

class ProductsController extends Controller {
    private $productModel;
    private $categoryModel;
    private $productImageModel;
    private $productVideoModel;
    private $productCustomFieldModel;

    public function __construct() {
        parent::__construct();
        $this->productModel = $this->model('Product');
        $this->categoryModel = $this->model('Category');
        $this->productImageModel = $this->model('ProductImage');
        $this->productVideoModel = $this->model('ProductVideo');
        $this->productCustomFieldModel = $this->model('ProductCustomField');
    }

    // Show all products
    public function index() {
        // Get all products with out_of_stock column, sort by stock status first (in stock first), then by created_at
        $db = new \App\Core\Database();
        $db->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY COALESCE(p.out_of_stock, 0) ASC, p.created_at DESC");
        $products = $db->resultSet();
        
        // Get all categories for filtering
        $categories = $this->categoryModel->getAll();
        
        $data = [
            'title' => 'Tất Cả Sản Phẩm',
            'products' => $products,
            'categories' => $categories
        ];

        $this->viewWithLayout('pages/products/index', $data);
    }

    // Show single product
    public function show($id) {
        // Log the function call and ID for debugging
        error_log("ProductsController::show($id) called");
        
        // Get product with out_of_stock column
        $db = new \App\Core\Database();
        $db->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = :id");
        $db->bind(':id', $id);
        $product = $db->single();
        
        if (!$product) {
            error_log("Product with ID $id not found");
            header('Location: ' . URL_ROOT . '/products');
            exit;
        }
        
        // Lấy danh sách các hình ảnh bổ sung của sản phẩm
        $additionalImages = $this->productImageModel->getByProductId($id);

        // Lấy video của sản phẩm
        $productVideos = $this->productVideoModel->getByProductId($id);

        // Debug to check if additionalImages are being retrieved
        if (empty($additionalImages)) {
            // Log this for debugging
            error_log("No additional images found for product ID: $id - This is normal if no images have been uploaded");
        } else {
            error_log("Found " . count($additionalImages) . " additional images for product ID: $id");
            // Log image details for debugging
            foreach ($additionalImages as $index => $image) {
                $imagePath = "public/img/products/" . $image['image_filename'];
                $exists = file_exists($imagePath) ? "EXISTS" : "MISSING";
                error_log("Image $index: {$image['image_filename']} - $exists (Display order: {$image['display_order']})");
            }
        }
        
        // Tăng lượt xem sản phẩm
        $this->productModel->incrementViews($id);
        
        // Lấy sản phẩm trong cùng danh mục với cột out_of_stock, sort by stock status first
        $db = new \App\Core\Database();
        $db->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.category_id = :category_id AND p.id != :product_id ORDER BY COALESCE(p.out_of_stock, 0) ASC, p.created_at DESC LIMIT 4");
        $db->bind(':category_id', $product['category_id']);
        $db->bind(':product_id', $id);
        $relatedProducts = $db->resultSet();
        
        // Lấy tất cả danh mục
        $categories = $this->categoryModel->getAll();
        
        // Lấy custom fields của sản phẩm
        $customFields = $this->productCustomFieldModel->getByProductId($id);
        
        $data = [
            'title' => $product['name'],
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'categories' => $categories,
            'additionalImages' => $additionalImages,
            'productVideos' => $productVideos,
            'customFields' => $customFields,
        ];
        
        // Log the data being passed to the view
        error_log("Rendering product detail view with " . (isset($additionalImages) ? count($additionalImages) : 0) . " additional images");
        
        $this->viewWithLayout('pages/products/show', $data);
    }

    // Show products by category
    public function category($id) {
        // Get category
        $category = $this->categoryModel->getById($id);
        
        // If category not found
        if (!$category) {
            die('Danh mục không tồn tại');
        }
        
        // Get products by category with out_of_stock column, sort by stock status first
        $db = new \App\Core\Database();
        $db->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.category_id = :category_id ORDER BY COALESCE(p.out_of_stock, 0) ASC, p.created_at DESC");
        $db->bind(':category_id', $id);
        $products = $db->resultSet();
        
        // Get all categories for sidebar
        $categories = $this->categoryModel->getAll();
        
        $data = [
            'title' => $category['name'],
            'category' => $category,
            'products' => $products,
            'categories' => $categories
        ];

        $this->viewWithLayout('pages/products/category', $data);
    }

    // Search products
    public function search() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            // Get search term
            $term = isset($_GET['term']) ? trim($_GET['term']) : '';
            
            if (empty($term)) {
                redirect('products');
            }
            
            // Search products with out_of_stock column, sort by stock status first
            $db = new \App\Core\Database();
            $db->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.name LIKE :term OR p.description LIKE :term ORDER BY COALESCE(p.out_of_stock, 0) ASC, p.created_at DESC");
            $db->bind(':term', '%' . $term . '%');
            $products = $db->resultSet();
            
            // Get all categories for sidebar
            $categories = $this->categoryModel->getAll();
            
            $data = [
                'title' => 'Kết quả tìm kiếm: ' . $term,
                'term' => $term,
                'products' => $products,
                'categories' => $categories
            ];

            $this->viewWithLayout('pages/products/search', $data);
        } else {
            redirect('products');
        }
    }
}