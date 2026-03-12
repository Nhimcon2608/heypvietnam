<?php
namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller {
    private $categoryModel;
    private $productModel;
    private $settingModel;

    public function __construct() {
        $this->categoryModel = $this->model('Category');
        $this->productModel = $this->model('Product');
        $this->settingModel = $this->model('Setting');
    }

    public function index() {
        // Get featured products with out_of_stock column, sort by stock status first
        $db = new \App\Core\Database();
        $db->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.featured = 1 ORDER BY COALESCE(p.out_of_stock, 0) ASC, p.created_at DESC LIMIT 4");
        $featuredProducts = $db->resultSet();
        
        // Get categories
        $categories = $this->categoryModel->getAll();
        
        // Get home page settings
        $homeTitle = $this->settingModel->getSettingValue('home_title', 'Sản Phẩm Xanh Cho Cuộc Sống Bền Vững');
        $homeDescription = $this->settingModel->getSettingValue('home_description', 'HeypVietNam cung cấp các sản phẩm thân thiện với môi trường, giúp bạn xây dựng một lối sống bền vững, an lành và hạnh phúc.');
        $homeButtonText = $this->settingModel->getSettingValue('home_button_text', 'Khám Phá Ngay');
        $homeButtonUrl = $this->settingModel->getSettingValue('home_button_url', URL_ROOT . '/products');
        
        // Featured products section
        $featuredTitle = $this->settingModel->getSettingValue('featured_products_title', 'Sản Phẩm Nổi Bật');
        $featuredDescription = $this->settingModel->getSettingValue('featured_products_description', '');
        
        // About section on homepage
        $homeAboutTitle = $this->settingModel->getSettingValue('home_about_title', 'Về HeypVietNam');
        $homeAboutContent = $this->settingModel->getSettingValue('home_about_content', 'HeypVietNam là thương hiệu cung cấp các sản phẩm thân thiện với môi trường, được sản xuất từ nguyên liệu tự nhiên, không chứa hóa chất độc hại.');
        $homeAboutButtonText = $this->settingModel->getSettingValue('home_about_button_text', 'Tìm Hiểu Thêm');
        
        $data = [
            'title' => 'Trang Chủ',
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
            'home_title' => $homeTitle,
            'home_description' => $homeDescription,
            'home_button_text' => $homeButtonText,
            'home_button_url' => $homeButtonUrl,
            'featured_title' => $featuredTitle,
            'featured_description' => $featuredDescription,
            'home_about_title' => $homeAboutTitle,
            'home_about_content' => $homeAboutContent,
            'home_about_button_text' => $homeAboutButtonText
        ];

        $this->viewWithLayout('pages/home', $data);
    }
} 