<?php
namespace App\Controllers;

// Increase upload limits
ini_set('upload_max_filesize', '50M');
ini_set('post_max_size', '60M');
ini_set('max_execution_time', '300');
ini_set('max_input_time', '300');
ini_set('memory_limit', '256M');

// Load config to ensure DEBUG constant is available
require_once dirname(__FILE__) . '/../../config.php';

use App\Core\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Admin;
use App\Models\ProductImage;
use App\Models\ProductVideo;
use App\Models\ProductCustomField;
use App\Helpers\ImageHelper;

/**
 * Class SettingManager - Quản lý cài đặt trực tiếp trong controller
 */
class SettingManager {
    private $db;
    
    public function __construct() {
        $this->db = new \App\Core\Database();
    }
    
    // Lấy giá trị cài đặt theo key
    public function get($key) {
        $this->db->query("SELECT value FROM settings WHERE `key` = :key");
        $this->db->bind(':key', $key);
        
        $result = $this->db->single();
        return $result ? $result['value'] : null;
    }
    
    // Cập nhật hoặc tạo mới cài đặt
    public function set($key, $value) {
        // Kiểm tra xem key đã tồn tại chưa
        $this->db->query("SELECT COUNT(*) as count FROM settings WHERE `key` = :key");
        $this->db->bind(':key', $key);
        $result = $this->db->single();
        
        if ($result['count'] > 0) {
            // Cập nhật
            $this->db->query("UPDATE settings SET value = :value WHERE `key` = :key");
            $this->db->bind(':key', $key);
            $this->db->bind(':value', $value);
            return $this->db->execute();
        } else {
            // Tạo mới
            $this->db->query("INSERT INTO settings (`key`, value) VALUES (:key, :value)");
            $this->db->bind(':key', $key);
            $this->db->bind(':value', $value);
            return $this->db->execute();
        }
    }
    
    // Cập nhật nhiều cài đặt cùng lúc
    public function updateMultiple($settings) {
        $success = true;
        
        foreach ($settings as $key => $value) {
            $result = $this->set($key, $value);
            if (!$result) {
                $success = false;
            }
        }
        
        return $success;
    }
}

class AdminController extends Controller {
    
    protected $product;
    protected $category;
    protected $admin;
    protected $setting;
    protected $productImage;
    protected $productVideo;
    protected $productCustomField;
    protected $settingManager;
    protected $page;
    
    public function __construct() {
        // Clean any output buffer to prevent redirect issues
        if (ob_get_level()) {
            ob_clean();
        }

        parent::__construct();

        // Tải models
        $this->category = $this->model('Category');
        $this->product = $this->model('Product');
        $this->productImage = $this->model('ProductImage'); // Thêm model ProductImage
        $this->productVideo = $this->model('ProductVideo'); // Thêm model ProductVideo
        $this->productCustomField = $this->model('ProductCustomField'); // Thêm model ProductCustomField
        $this->page = $this->model('Page');
        $this->admin = $this->model('Admin'); // Load Admin model
        
        // Khởi tạo SettingManager một lần
        $this->settingManager = new SettingManager();
        $this->setting = $this->settingManager;
        
        // Check if we're in the login page
        $currentAction = $this->getCurrentAction();
        
        // Only check for login and redirect if not already on the login page
        if ($currentAction !== 'index' && $currentAction !== 'login' && (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true)) {
            redirect('admin');
            exit;
        }

        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true && in_array($currentAction, ['dashboard', 'products', 'categories', 'settings'], true)) {
            redirect('admin/pages');
            exit;
        }
    }
    
    // Get the current action/method name
    private function getCurrentAction() {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
        $segments = array_values(array_filter(explode('/', trim((string) $path, '/')), 'strlen'));
        $adminIndex = array_search('admin', $segments, true);

        if ($adminIndex === false) {
            return 'index';
        }

        return $segments[$adminIndex + 1] ?? 'index';
    }
    
    // Login page
    public function index() {
        // If already logged in, go directly to the landing page editor
        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            redirect('admin/pages');
            exit;
        }
        
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = isset($_POST['username']) ? $_POST['username'] : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            
            if (empty($username) || empty($password)) {
                $error = 'Tên đăng nhập và mật khẩu không được để trống';
            } else {
                $admin = $this->admin->getByUsername($username);
                
                if ($admin && password_verify($password, $admin['password'])) {
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_name'] = $admin['name'];
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['admin_logged_in'] = true;

                    redirect('admin/pages');
                    exit;
                } else {
                    $error = 'Tên đăng nhập hoặc mật khẩu không đúng';
                }
            }
        }
        
        $this->view('admin/login', [
            'title' => 'Đăng nhập quản trị',
            'error' => $error
        ]);
    }
    
    // Dashboard
    public function dashboard() {
        redirect('admin/pages');
        exit;
    }

    // Landing page block editor
    public function pages() {
        $slug = 'home';
        $title = 'Trang Chủ';
        $defaultPage = $this->defaultLandingPageContent();
        $message = '';
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rawJson = $_POST['content_json'] ?? '';
            $decoded = json_decode($rawJson, true);

            if (!is_array($decoded)) {
                $error = 'JSON không hợp lệ. Vui lòng kiểm tra lại nội dung trang.';
            } else {
                $decoded = $this->normalizeLandingPageContent($decoded);
                $this->page->saveBySlug($slug, $decoded, $title);
                $message = 'Đã lưu landing page.';
            }
        }

        $pageRow = $this->page->getOrCreateBySlug($slug, $title, $defaultPage);
        $page = json_decode($pageRow['content'], true);
        if (!is_array($page)) {
            $page = $defaultPage;
            $this->page->saveBySlug($slug, $page, $title);
        }

        $page = $this->normalizeLandingPageContent($page);

        $this->view('admin/pages/editor', [
            'title' => 'Landing Page Builder',
            'page' => $page,
            'page_json' => json_encode($page, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT),
            'message' => $message,
            'error' => $error
        ]);
    }

    private function defaultLandingPageContent() {
        return [
            'header' => [
                'logo' => 'public/img/logoHEYP.png'
            ],
            'layoutType' => 'canvas',
            'canvas' => [
                'width' => 1200,
                'height' => 760,
                'backgroundColor' => '#ffffff'
            ],
            'elements' => [
                [
                    'id' => 'hero-title',
                    'type' => 'text',
                    'x' => 90,
                    'y' => 92,
                    'width' => 650,
                    'height' => 130,
                    'zIndex' => 1,
                    'content' => 'Sản phẩm xanh cho lối sống bền vững',
                    'style' => [
                        'fontFamily' => 'Montserrat',
                        'fontSize' => 46,
                        'fontWeight' => '700',
                        'color' => '#243528',
                        'backgroundColor' => 'transparent',
                        'borderRadius' => 0
                    ]
                ],
                [
                    'id' => 'hero-copy',
                    'type' => 'text',
                    'x' => 92,
                    'y' => 250,
                    'width' => 520,
                    'height' => 120,
                    'zIndex' => 2,
                    'content' => 'HEYP cung cấp các sản phẩm làm sạch trong gia đình từ xà phòng truyền thống, thân thiện môi trường.',
                    'style' => [
                        'fontFamily' => 'Open Sans',
                        'fontSize' => 24,
                        'fontWeight' => '400',
                        'color' => '#1f2937',
                        'backgroundColor' => 'transparent',
                        'borderRadius' => 0
                    ]
                ],
                [
                    'id' => 'hero-logo',
                    'type' => 'image',
                    'x' => 760,
                    'y' => 130,
                    'width' => 300,
                    'height' => 300,
                    'zIndex' => 3,
                    'src' => 'public/img/logoHEYP.png',
                    'content' => 'Heyp Logo',
                    'style' => [
                        'fontFamily' => 'Open Sans',
                        'fontSize' => 18,
                        'fontWeight' => '400',
                        'color' => '#1f2937',
                        'backgroundColor' => 'transparent',
                        'borderRadius' => 8
                    ],
                ]
            ]
        ];
    }

    private function normalizeLandingPageContent(array $page) {
        if (($page['layoutType'] ?? '') === 'canvas' || isset($page['elements'])) {
            return $this->normalizeCanvasPageContent($page);
        }

        if (!isset($page['header']) || !is_array($page['header'])) {
            $page['header'] = ['logo' => 'public/img/logoHEYP.png'];
        }

        if (!isset($page['sections']) || !is_array($page['sections'])) {
            $page['sections'] = [];
        }

        $sections = [];
        foreach ($page['sections'] as $index => $section) {
            if (!is_array($section)) {
                continue;
            }

            $heading = isset($section['heading']) && is_array($section['heading']) ? $section['heading'] : [];
            $fallbackId = 'section-' . ($index + 1);
            $rawId = $section['id'] ?? $heading['anchorId'] ?? $heading['text'] ?? $fallbackId;
            $sectionId = $this->normalizeLandingAnchorId($rawId, $fallbackId);

            if (!empty($heading)) {
                $headingLevel = isset($heading['level']) ? (int) $heading['level'] : 1;
                $heading['level'] = in_array($headingLevel, [1, 2, 3], true) ? $headingLevel : 1;
                $heading['text'] = (string) ($heading['text'] ?? '');
                $heading['anchorId'] = $sectionId;
                $heading['style'] = $this->normalizeLandingBlockStyle($heading['style'] ?? [], 'heading');
            }

            $blocks = [];
            foreach (($section['blocks'] ?? []) as $block) {
                if (!is_array($block)) {
                    continue;
                }

                $type = $block['type'] ?? '';
                if ($type === 'heading') {
                    $level = isset($block['level']) ? (int) $block['level'] : 2;
                    $blocks[] = [
                        'type' => 'heading',
                        'level' => in_array($level, [1, 2, 3], true) ? $level : 2,
                        'content' => (string) ($block['content'] ?? $block['text'] ?? ''),
                        'style' => $this->normalizeLandingBlockStyle($block['style'] ?? [], 'heading')
                    ];
                } elseif ($type === 'text') {
                    $blocks[] = [
                        'type' => 'text',
                        'content' => (string) ($block['content'] ?? ''),
                        'style' => $this->normalizeLandingBlockStyle($block['style'] ?? [], 'text')
                    ];
                } elseif ($type === 'image') {
                    $blocks[] = [
                        'type' => 'image',
                        'src' => (string) ($block['src'] ?? $block['url'] ?? $block['image_url'] ?? ''),
                        'alt' => (string) ($block['alt'] ?? ''),
                        'caption' => (string) ($block['caption'] ?? ''),
                        'style' => $this->normalizeLandingBlockStyle($block['style'] ?? [], 'image')
                    ];
                } elseif ($type === 'columns') {
                    $columns = [];
                    $rawColumns = isset($block['columns']) && is_array($block['columns']) ? $block['columns'] : [];
                    $columns[] = $this->normalizeLandingColumnBlock($rawColumns[0] ?? null, 'text');
                    $columns[] = $this->normalizeLandingColumnBlock($rawColumns[1] ?? null, 'image');

                    $blocks[] = [
                        'type' => 'columns',
                        'columns' => $columns,
                        'style' => $this->normalizeLandingBlockStyle($block['style'] ?? [], 'columns')
                    ];
                }
            }

            $sections[] = [
                'id' => $sectionId,
                'heading' => $heading,
                'blocks' => $blocks
            ];
        }

        $page['sections'] = $sections;
        return $page;
    }

    private function normalizeCanvasPageContent(array $page) {
        $header = isset($page['header']) && is_array($page['header'])
            ? $page['header']
            : ['logo' => 'public/img/logoHEYP.png'];

        $canvas = isset($page['canvas']) && is_array($page['canvas']) ? $page['canvas'] : [];
        $elements = [];

        foreach (($page['elements'] ?? []) as $index => $element) {
            if (!is_array($element)) {
                continue;
            }

            $elements[] = $this->normalizeCanvasElement($element, $index + 1);
        }

        if (empty($elements)) {
            foreach ($this->defaultLandingPageContent()['elements'] as $index => $element) {
                $elements[] = $this->normalizeCanvasElement($element, $index + 1);
            }
        }

        usort($elements, function($a, $b) {
            return $a['zIndex'] <=> $b['zIndex'];
        });

        foreach ($elements as $index => &$element) {
            $element['zIndex'] = $index + 1;
        }
        unset($element);

        $canvasWidth = $this->normalizeLandingNumber($canvas['width'] ?? 1200, 320, 2400, 1200);
        $canvasHeight = $this->normalizeLandingNumber($canvas['height'] ?? 760, 320, 10000, 760);
        foreach ($elements as $element) {
            $canvasHeight = max($canvasHeight, (int) $element['y'] + (int) $element['height'] + 180);
        }
        $canvasHeight = $this->normalizeLandingNumber($canvasHeight, 320, 10000, 760);

        return [
            'header' => $header,
            'layoutType' => 'canvas',
            'canvas' => [
                'width' => $canvasWidth,
                'height' => $canvasHeight,
                'backgroundColor' => $this->normalizeCanvasColor($canvas['backgroundColor'] ?? '#ffffff', '#ffffff')
            ],
            'elements' => $elements
        ];
    }

    private function normalizeCanvasElement(array $element, $fallbackIndex) {
        $type = $this->normalizeLandingChoice($element['type'] ?? 'text', ['text', 'image', 'video', 'shape'], 'text');
        $defaultWidth = $type === 'text' ? 320 : ($type === 'video' ? 480 : 240);
        $defaultHeight = $type === 'text' ? 100 : ($type === 'video' ? 270 : 180);

        return [
            'id' => $this->normalizeCanvasElementId($element['id'] ?? '', $fallbackIndex),
            'type' => $type,
            'x' => $this->normalizeLandingNumber($element['x'] ?? 120, 0, 10000, 120),
            'y' => $this->normalizeLandingNumber($element['y'] ?? 120, 0, 10000, 120),
            'width' => $this->normalizeLandingNumber($element['width'] ?? $defaultWidth, 20, 2400, $defaultWidth),
            'height' => $this->normalizeLandingNumber($element['height'] ?? $defaultHeight, 20, 3200, $defaultHeight),
            'zIndex' => $this->normalizeLandingNumber($element['zIndex'] ?? $fallbackIndex, 1, 9999, $fallbackIndex),
            'content' => (string) ($element['content'] ?? ''),
            'src' => (string) ($element['src'] ?? $element['image_url'] ?? $element['url'] ?? ''),
            'style' => $this->normalizeCanvasElementStyle($element['style'] ?? [], $type)
        ];
    }

    private function normalizeCanvasElementStyle($style, $type) {
        $style = is_array($style) ? $style : [];
        $defaultBackground = $type === 'shape' ? '#d9f99d' : ($type === 'video' ? '#111827' : 'transparent');

        return [
            'fontFamily' => $this->normalizeLandingChoice(
                $style['fontFamily'] ?? 'Open Sans',
                ['Open Sans', 'Montserrat', 'Georgia', 'Arial', 'Times New Roman'],
                'Open Sans'
            ),
            'fontSize' => $this->normalizeLandingNumber($style['fontSize'] ?? ($type === 'text' ? 24 : 18), 8, 160, $type === 'text' ? 24 : 18),
            'fontWeight' => $this->normalizeLandingChoice((string) ($style['fontWeight'] ?? '400'), ['400', '500', '600', '700'], '400'),
            'color' => $this->normalizeCanvasColor($style['color'] ?? '#1f2937', '#1f2937'),
            'backgroundColor' => $this->normalizeCanvasColor($style['backgroundColor'] ?? $defaultBackground, $defaultBackground),
            'borderRadius' => $this->normalizeLandingNumber($style['borderRadius'] ?? (($type === 'image' || $type === 'video') ? 8 : 0), 0, 240, ($type === 'image' || $type === 'video') ? 8 : 0)
        ];
    }

    private function normalizeCanvasElementId($id, $fallbackIndex) {
        $id = preg_replace('/[^a-zA-Z0-9_-]+/', '-', trim((string) $id));
        $id = trim($id, '-');

        return $id !== '' ? $id : 'el-' . $fallbackIndex . '-' . uniqid();
    }

    private function normalizeCanvasColor($value, $fallback) {
        $value = trim((string) $value);

        if ($value === 'transparent') {
            return 'transparent';
        }

        return $this->normalizeLandingColor($value, $fallback);
    }

    private function normalizeLandingColumnBlock($block, $fallbackType) {
        if (!is_array($block) || ($block['type'] ?? '') === 'columns') {
            return $this->defaultLandingColumnBlock($fallbackType);
        }

        $type = $block['type'] ?? '';
        if ($type === 'heading') {
            $level = isset($block['level']) ? (int) $block['level'] : 2;

            return [
                'type' => 'heading',
                'level' => in_array($level, [1, 2, 3], true) ? $level : 2,
                'content' => (string) ($block['content'] ?? $block['text'] ?? ''),
                'style' => $this->normalizeLandingBlockStyle($block['style'] ?? ['width' => 100], 'heading')
            ];
        }

        if ($type === 'image') {
            return [
                'type' => 'image',
                'src' => (string) ($block['src'] ?? $block['url'] ?? $block['image_url'] ?? ''),
                'alt' => (string) ($block['alt'] ?? ''),
                'caption' => (string) ($block['caption'] ?? ''),
                'style' => $this->normalizeLandingBlockStyle($block['style'] ?? ['width' => 100], 'image')
            ];
        }

        return [
            'type' => 'text',
            'content' => (string) ($block['content'] ?? ''),
            'style' => $this->normalizeLandingBlockStyle($block['style'] ?? ['width' => 100], 'text')
        ];
    }

    private function defaultLandingColumnBlock($type) {
        if ($type === 'image') {
            return [
                'type' => 'image',
                'src' => '',
                'alt' => '',
                'caption' => '',
                'style' => $this->normalizeLandingBlockStyle(['width' => 100], 'image')
            ];
        }

        return [
            'type' => 'text',
            'content' => '',
            'style' => $this->normalizeLandingBlockStyle(['width' => 100], 'text')
        ];
    }

    private function normalizeLandingBlockStyle($style, $type) {
        $style = is_array($style) ? $style : [];

        $normalized = [
            'align' => $this->normalizeLandingChoice($style['align'] ?? 'left', ['left', 'center', 'right'], 'left'),
            'width' => $this->normalizeLandingNumber($style['width'] ?? 100, 10, 100, 100),
            'marginTop' => $this->normalizeLandingNumber($style['marginTop'] ?? 0, 0, 160, 0),
            'marginBottom' => $this->normalizeLandingNumber($style['marginBottom'] ?? 16, 0, 160, 16),
            'backgroundColor' => $this->normalizeLandingColor($style['backgroundColor'] ?? '')
        ];

        if ($type === 'heading' || $type === 'text') {
            $normalized['fontFamily'] = $this->normalizeLandingChoice(
                $style['fontFamily'] ?? 'Open Sans',
                ['Open Sans', 'Montserrat', 'Georgia', 'Arial', 'Times New Roman'],
                'Open Sans'
            );
            $normalized['fontSize'] = $this->normalizeLandingNumber($style['fontSize'] ?? ($type === 'heading' ? 40 : 18), 10, 96, $type === 'heading' ? 40 : 18);
            $normalized['fontWeight'] = $this->normalizeLandingChoice((string) ($style['fontWeight'] ?? '400'), ['400', '500', '600', '700'], '400');
            $normalized['fontStyle'] = $this->normalizeLandingChoice($style['fontStyle'] ?? 'normal', ['normal', 'italic'], 'normal');
            $normalized['color'] = $this->normalizeLandingColor($style['color'] ?? '#243528', '#243528');
            $normalized['textAlign'] = $this->normalizeLandingChoice($style['textAlign'] ?? $normalized['align'], ['left', 'center', 'right'], $normalized['align']);
        }

        if ($type === 'image') {
            $normalized['borderRadius'] = $this->normalizeLandingNumber($style['borderRadius'] ?? 8, 0, 40, 8);
        }

        if ($type === 'columns') {
            $normalized['columnGap'] = $this->normalizeLandingNumber($style['columnGap'] ?? 24, 0, 80, 24);
            $normalized['firstColumnWidth'] = $this->normalizeLandingNumber($style['firstColumnWidth'] ?? 50, 20, 80, 50);
        }

        return $normalized;
    }

    private function normalizeLandingChoice($value, array $allowed, $fallback) {
        $value = (string) $value;
        return in_array($value, $allowed, true) ? $value : $fallback;
    }

    private function normalizeLandingNumber($value, $min, $max, $fallback) {
        if (!is_numeric($value)) {
            return $fallback;
        }

        $value = (int) $value;
        return max($min, min($max, $value));
    }

    private function normalizeLandingColor($value, $fallback = '') {
        $value = trim((string) $value);

        if ($value === '') {
            return $fallback;
        }

        return preg_match('/^#[0-9a-fA-F]{6}$/', $value) ? $value : $fallback;
    }

    private function normalizeLandingAnchorId($value, $fallback) {
        $id = strtolower(trim((string) $value));
        $id = preg_replace('/[^a-z0-9_-]+/', '-', $id);
        $id = trim($id, '-');

        return $id !== '' ? $id : $fallback;
    }
    
    // Category management
    public function categories() {
        $categories = $this->category->getAll();
        
        $this->view('admin/categories/index', [
            'title' => 'Quản lý danh mục',
            'categories' => $categories
        ]);
    }
    
    public function addCategory() {
        $error = '';
        $category = [
            'name' => '',
            'description' => '',
            'image' => ''
        ];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $category['name'] = isset($_POST['name']) ? $_POST['name'] : '';
            $category['description'] = isset($_POST['description']) ? $_POST['description'] : '';
            
            if (empty($category['name'])) {
                $error = 'Tên danh mục không được để trống';
            } else {
                // Handle image upload
                $image = '';
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = 'public/img/categories/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    $fileName = time() . '_' . basename($_FILES['image']['name']);
                    $uploadFile = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                        $image = $fileName;
                    }
                }
                
                $category['image'] = $image;
                
                $result = $this->category->create($category);
                if ($result) {
                    $_SESSION['success'] = 'Thêm danh mục thành công';
                    header('Location: ' . URL_ROOT . '/admin/categories');
                    exit;
                } else {
                    $error = 'Có lỗi xảy ra khi thêm danh mục';
                }
            }
        }
        
        $this->view('admin/categories/add', [
            'title' => 'Thêm danh mục',
            'category' => $category,
            'error' => $error
        ]);
    }

    // Handle edit routing for categories
    public function edit() {
        // Get ID from URL parameters
        $args = func_get_args();
        $id = isset($args[0]) ? $args[0] : null;

        // Check if we're editing a category based on the URL structure
        // URL: /admin/categories/edit/9
        if ($id !== null) {
            $this->editCategory($id);
        } else {
            header('Location: ' . URL_ROOT . '/admin/categories');
            exit;
        }
    }

    public function editCategory($id) {
        $category = $this->category->getById($id);
        
        if (!$category) {
            header('Location: ' . URL_ROOT . '/admin/categories');
            exit;
        }
        
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $category['name'] = isset($_POST['name']) ? $_POST['name'] : '';
            $category['description'] = isset($_POST['description']) ? $_POST['description'] : '';
            
            if (empty($category['name'])) {
                $error = 'Tên danh mục không được để trống';
            } else {
                // Handle image upload
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = 'public/img/categories/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    $fileName = time() . '_' . basename($_FILES['image']['name']);
                    $uploadFile = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                        // Remove old image if exists
                        if (!empty($category['image'])) {
                            $oldFile = $uploadDir . $category['image'];
                            if (file_exists($oldFile)) {
                                unlink($oldFile);
                            }
                        }
                        
                        $category['image'] = $fileName;
                    }
                }
                
                $result = $this->category->update($id, $category);
                if ($result) {
                    $_SESSION['success'] = 'Cập nhật danh mục thành công';
                    header('Location: ' . URL_ROOT . '/admin/categories');
                    exit;
                } else {
                    $error = 'Có lỗi xảy ra khi cập nhật danh mục';
                }
            }
        }
        
        $this->view('admin/categories/edit', [
            'title' => 'Cập nhật danh mục',
            'category' => $category,
            'error' => $error
        ]);
    }
    
    public function deleteCategory() {
        // Get ID from URL parameters
        $args = func_get_args();
        $id = isset($args[0]) ? $args[0] : null;

        // Check if this is an AJAX request
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        
        if ($id === null) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'ID danh mục không hợp lệ']);
                exit;
            }
            header('Location: ' . URL_ROOT . '/admin/categories');
            exit;
        }
        
        $category = $this->category->getById($id);
        
        if (!$category) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy danh mục']);
                exit;
            }
            header('Location: ' . URL_ROOT . '/admin/categories');
            exit;
        }
        
        try {
            // Check if category has products
            $db = new \App\Core\Database();
            $db->query("SELECT COUNT(*) as count FROM products WHERE category_id = :id");
            $db->bind(':id', $id);
            $result = $db->single();
            
            if ($result['count'] > 0) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Không thể xóa danh mục vì còn có sản phẩm thuộc danh mục này']);
                    exit;
                }
                $_SESSION['error'] = 'Không thể xóa danh mục vì còn có sản phẩm thuộc danh mục này';
                header('Location: ' . URL_ROOT . '/admin/categories');
                exit;
            }
            
            // Remove category image if exists
            if (!empty($category['image'])) {
                $file = APP_ROOT . '/public/img/categories/' . $category['image'];
                if (file_exists($file)) {
                    unlink($file);
                }
            }
            
            // Delete category
            if ($this->category->delete($id)) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => 'Xóa danh mục thành công']);
                    exit;
                }
                $_SESSION['success'] = 'Xóa danh mục thành công';
            } else {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi xóa danh mục']);
                    exit;
                }
                $_SESSION['error'] = 'Có lỗi xảy ra khi xóa danh mục';
            }
        } catch (Exception $e) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
                exit;
            }
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        
        header('Location: ' . URL_ROOT . '/admin/categories');
        exit;
    }
    
    // Product management
    public function products() {
        // Lấy danh mục đã chọn từ query parameter nếu có
        $selectedCategory = isset($_GET['category_id']) ? $_GET['category_id'] : null;

        // Pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = ADMIN_PRODUCTS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        // Lấy tất cả danh mục
        $categories = $this->category->getAll();

        // Lấy sản phẩm theo danh mục nếu có (với cột out_of_stock và pagination)
        $db = new \App\Core\Database();

        // Count total products for pagination
        if ($selectedCategory && $selectedCategory != 'all') {
            $db->query("SELECT COUNT(*) as total FROM products WHERE category_id = :category_id");
            $db->bind(':category_id', $selectedCategory);
        } else {
            $db->query("SELECT COUNT(*) as total FROM products");
        }
        $totalResult = $db->single();
        $totalProducts = $totalResult['total'];
        $totalPages = ceil($totalProducts / $limit);

        // Get products with pagination, sort by stock status first
        if ($selectedCategory && $selectedCategory != 'all') {
            $db->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.category_id = :category_id ORDER BY COALESCE(p.out_of_stock, 0) ASC, p.created_at DESC LIMIT :limit OFFSET :offset");
            $db->bind(':category_id', $selectedCategory);
            $db->bind(':limit', $limit, \PDO::PARAM_INT);
            $db->bind(':offset', $offset, \PDO::PARAM_INT);
        } else {
            $db->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY COALESCE(p.out_of_stock, 0) ASC, p.created_at DESC LIMIT :limit OFFSET :offset");
            $db->bind(':limit', $limit, \PDO::PARAM_INT);
            $db->bind(':offset', $offset, \PDO::PARAM_INT);
        }
        $products = $db->resultSet();

        $this->view('admin/products/index', [
            'title' => 'Quản lý sản phẩm',
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts
        ]);
    }
    
    public function addProduct() {
        error_log("AdminController::addProduct() called");
        error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
        error_log("POST data: " . print_r($_POST, true));
        error_log("FILES data: " . print_r($_FILES, true));

        $error = '';
        $success = '';
        $product = [
            'name' => '',
            'description' => '',
            'price' => '',
            'category_id' => '',
            'image' => '',
            'featured' => 0,
            'shopee_link' => ''
        ];
        
        $sizes = [];
        $size_prices = [];
        $categories = $this->category->getAll();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product['name'] = isset($_POST['name']) ? $_POST['name'] : '';
            $product['description'] = isset($_POST['description']) ? $_POST['description'] : '';
            $product['price'] = isset($_POST['price']) ? $_POST['price'] : '';
            $product['category_id'] = isset($_POST['category_id']) ? $_POST['category_id'] : '';
            $product['featured'] = isset($_POST['featured']) ? 1 : 0;
            $product['shopee_link'] = isset($_POST['shopee_link']) ? $_POST['shopee_link'] : '';
            
            // Product details (will be stored in custom fields)
            $productDetails = [
                'origin' => isset($_POST['origin']) ? $_POST['origin'] : 'Việt Nam',
                'warranty_type' => isset($_POST['warranty_type']) ? $_POST['warranty_type'] : 'Bảo hành nhà sản xuất',
                'manufacturer_name' => isset($_POST['manufacturer_name']) ? $_POST['manufacturer_name'] : 'HEYP SOAP',
                'manufacturer_address' => isset($_POST['manufacturer_address']) ? $_POST['manufacturer_address'] : '70 Buôn Sút M\'gưr xã Cư Suê, huyện Cư M\'',
                'shipping_from' => isset($_POST['shipping_from']) ? $_POST['shipping_from'] : 'Đắk Lắk',
                'material' => isset($_POST['material']) ? $_POST['material'] : 'Thô',
                'usage_instructions' => isset($_POST['usage_instructions']) ? $_POST['usage_instructions'] : ''
            ];

            $sizes = isset($_POST['size']) ? $_POST['size'] : [];
            $size_prices = isset($_POST['size_price']) ? $_POST['size_price'] : [];
            $display_orders = isset($_POST['display_order']) ? $_POST['display_order'] : [];
            
            if (empty($product['name'])) {
                $error = 'Tên sản phẩm không được để trống';
            } elseif (empty($product['price']) || !is_numeric($product['price'])) {
                $error = 'Giá sản phẩm không hợp lệ';
            } elseif (empty($product['category_id'])) {
                $error = 'Vui lòng chọn danh mục sản phẩm';
            } elseif (empty($product['shopee_link'])) {
                $error = 'Link Shopee không được để trống';
            } elseif (!filter_var($product['shopee_link'], FILTER_VALIDATE_URL)) {
                $error = 'Link Shopee không hợp lệ';
            } else {
                try {
                    // Xử lý hình ảnh chính (không bắt buộc)
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        $imageInfo = $this->product->processImageUpload($_FILES['image']);
                        if ($imageInfo) {
                            $product['image'] = $imageInfo['filename'];
                            $product['image_data'] = $imageInfo['data'];
                            $product['image_mime_type'] = $imageInfo['mime_type'];
                        } else {
                            throw new \Exception('Không thể xử lý hình ảnh chính');
                        }
                    } else {
                        // Nếu không có hình ảnh, sử dụng hình mặc định
                        $product['image'] = 'default-product.jpg';
                    }


                    // Thêm sản phẩm (không dùng transaction để tránh timeout)
                    $product_id = $this->product->create($product);
                    if (!$product_id) {
                        throw new \Exception('Không thể thêm sản phẩm');
                    }
                    
                    // Lưu product details vào custom fields
                    $displayOrder = 1;
                    foreach ($productDetails as $key => $value) {
                        if (!empty($value)) {
                            $fieldTitle = '';
                            switch ($key) {
                                case 'origin':
                                    $fieldTitle = 'Xuất xứ';
                                    break;
                                case 'warranty_type':
                                    $fieldTitle = 'Loại bảo hành';
                                    break;
                                case 'manufacturer_name':
                                    $fieldTitle = 'Tên nhà sản xuất';
                                    break;
                                case 'manufacturer_address':
                                    $fieldTitle = 'Địa chỉ nhà sản xuất';
                                    break;
                                case 'shipping_from':
                                    $fieldTitle = 'Gửi từ';
                                    break;
                                case 'material':
                                    $fieldTitle = 'Chất liệu';
                                    break;
                                case 'usage_instructions':
                                    $fieldTitle = 'Hướng dẫn sử dụng';
                                    break;
                            }

                            if (!empty($fieldTitle)) {
                                $this->productCustomField->create([
                                    'product_id' => $product_id,
                                    'field_title' => $fieldTitle,
                                    'field_content' => $value,
                                    'display_order' => $displayOrder++
                                ]);
                            }
                        }
                    }

                    // Xử lý custom fields từ form
                    if (isset($_POST['custom_field_title']) && isset($_POST['custom_field_content'])) {
                        $customTitles = $_POST['custom_field_title'];
                        $customContents = $_POST['custom_field_content'];

                        for ($i = 0; $i < count($customTitles); $i++) {
                            // Trim whitespace và kiểm tra
                            $title = isset($customTitles[$i]) ? trim($customTitles[$i]) : '';
                            $content = isset($customContents[$i]) ? trim($customContents[$i]) : '';

                            if (!empty($title) && !empty($content)) {
                                $this->productCustomField->create([
                                    'product_id' => $product_id,
                                    'field_title' => $title,
                                    'field_content' => $content,
                                    'display_order' => $displayOrder++
                                ]);
                            }
                        }
                    }

                    // Thêm kích thước với ảnh (nếu có)
                    if (!empty($sizes) && count($sizes) > 0) {
                        for ($i = 0; $i < count($sizes); $i++) {
                            if (!empty($sizes[$i]) && is_numeric($size_prices[$i])) {
                                $display_order = isset($display_orders[$i]) ? intval($display_orders[$i]) : $i;

                                // Xử lý upload ảnh cho size
                                $size_image = null;
                                if (isset($_FILES['size_images']) && isset($_FILES['size_images']['name'][$i]) && $_FILES['size_images']['error'][$i] === UPLOAD_ERR_OK) {
                                    $size_upload_dir = APP_ROOT . '/public/img/products/sizes/';

                                    // Tạo thư mục nếu chưa tồn tại
                                    if (!is_dir($size_upload_dir)) {
                                        mkdir($size_upload_dir, 0777, true);
                                    }

                                    $size_file_name = time() . '_size_' . $i . '_' . basename($_FILES['size_images']['name'][$i]);
                                    $size_upload_file = $size_upload_dir . $size_file_name;

                                    if (move_uploaded_file($_FILES['size_images']['tmp_name'][$i], $size_upload_file)) {
                                        $size_image = $size_file_name;
                                    }
                                }

                                // Sử dụng helper method để thêm product size
                                $this->addProductSize($product_id, $sizes[$i], $size_prices[$i], $display_order, $size_image);
                            }
                        }
                    }
                    
                    // Xử lý thêm hình ảnh bổ sung
                    if (isset($_FILES['additional_images']) && count($_FILES['additional_images']['name']) > 0) {
                        // Tải lên nhiều hình ảnh
                        $total = count($_FILES['additional_images']['name']);

                        for ($i = 0; $i < $total; $i++) {
                            // Kiểm tra lỗi
                            if ($_FILES['additional_images']['error'][$i] === UPLOAD_ERR_OK) {
                                // Create temporary file array for processing
                                $tempFile = [
                                    'name' => $_FILES['additional_images']['name'][$i],
                                    'type' => $_FILES['additional_images']['type'][$i],
                                    'tmp_name' => $_FILES['additional_images']['tmp_name'][$i],
                                    'error' => $_FILES['additional_images']['error'][$i],
                                    'size' => $_FILES['additional_images']['size'][$i]
                                ];

                                $imageInfo = $this->productImage->processImageUpload($tempFile);
                                if ($imageInfo) {
                                    // Lưu vào database (vị trí display_order = i+1)
                                    $this->productImage->add($product_id, $imageInfo['filename'], $i + 1, $imageInfo['data'], $imageInfo['mime_type']);
                                }
                            }
                        }
                    }

                    // Xử lý upload video
                    if (isset($_FILES['video'])) {
                        if ($_FILES['video']['error'] === UPLOAD_ERR_OK) {
                            try {
                                $videoUploadDir = APP_ROOT . '/public/videos/products/';
                                if (!is_dir($videoUploadDir)) {
                                    mkdir($videoUploadDir, 0777, true);
                                }

                                $videoFileName = time() . '_' . basename($_FILES['video']['name']);
                                $videoUploadFile = $videoUploadDir . $videoFileName;
                                $videoType = $this->productVideo->getVideoType($_FILES['video']['name']);

                                if ($this->productVideo->isAllowedVideoType($_FILES['video']['name'])) {
                                    if (move_uploaded_file($_FILES['video']['tmp_name'], $videoUploadFile)) {
                                        $result = $this->productVideo->add($product_id, $videoFileName, $videoType, 0);
                                        if (!$result) {
                                            throw new \Exception('Không thể lưu thông tin video vào database');
                                        }
                                    } else {
                                        throw new \Exception('Không thể upload file video');
                                    }
                                } else {
                                    throw new \Exception('Định dạng video không được hỗ trợ: ' . $videoType);
                                }
                            } catch (\Exception $e) {
                                error_log("Video upload error: " . $e->getMessage());
                                // Don't throw exception here, just log it so product can still be created
                            }
                        } elseif ($_FILES['video']['error'] !== UPLOAD_ERR_NO_FILE) {
                            $errorMsg = '';
                            switch ($_FILES['video']['error']) {
                                case UPLOAD_ERR_INI_SIZE:
                                case UPLOAD_ERR_FORM_SIZE:
                                    $errorMsg = 'File video quá lớn';
                                    break;
                                case UPLOAD_ERR_PARTIAL:
                                    $errorMsg = 'File video chỉ được upload một phần';
                                    break;
                                default:
                                    $errorMsg = 'Lỗi upload video: ' . $_FILES['video']['error'];
                            }
                            error_log('Video upload error: ' . $errorMsg);
                        }
                    }

                    $success = 'Thêm sản phẩm thành công!';
                    // Reset form
                    $product = [
                        'name' => '',
                        'description' => '',
                        'price' => '',
                        'category_id' => '',
                        'image' => '',
                        'featured' => 0,
                        'shopee_link' => ''
                    ];
                    $sizes = [];
                    $size_prices = [];
                } catch(\Exception $e) {
                    $error = 'Lỗi: ' . $e->getMessage();
                    error_log("Add product error: " . $e->getMessage());
                }
            }
        }
        
        $this->view('admin/products/addproduct', [
            'title' => 'Thêm sản phẩm',
            'product' => $product,
            'categories' => $categories,
            'error' => $error,
            'success' => $success,
            'sizes' => $sizes,
            'size_prices' => $size_prices
        ]);
    }
    
    public function editProduct() {
        error_log("=== EDIT PRODUCT METHOD CALLED ===");
        error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
        // Get ID from URL parameters
        $args = func_get_args();
        $id = isset($args[0]) ? $args[0] : null;
        error_log("Product ID: " . $id);

        if ($id === null) {
            header('Location: ' . URL_ROOT . '/admin/products');
            exit;
        }
        $product = $this->product->getById($id);

        if (!$product) {
            header('Location: ' . URL_ROOT . '/admin/products');
            exit;
        }

        // Lấy sizes của sản phẩm
        $sizes = $this->product->getSizesByProductId($id);
        
        // Lấy custom fields của sản phẩm
        $customFields = $this->productCustomField->getByProductId($id);
        if (!$customFields) {
            $customFields = [];
        }

        $categories = $this->category->getAll();
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log("=== PROCESSING POST REQUEST FOR EDIT PRODUCT ===");
            error_log("POST data: " . print_r($_POST, true));
            error_log("FILES data: " . print_r($_FILES, true));

            $product['name'] = isset($_POST['name']) ? $_POST['name'] : '';
            $product['description'] = isset($_POST['description']) ? $_POST['description'] : '';
            $product['price'] = isset($_POST['price']) ? $_POST['price'] : '';
            $product['category_id'] = isset($_POST['category_id']) ? $_POST['category_id'] : '';
            $product['featured'] = isset($_POST['featured']) ? 1 : 0;
            $product['shopee_link'] = isset($_POST['shopee_link']) ? $_POST['shopee_link'] : '';
            
            // Product details
            $product['origin'] = isset($_POST['origin']) ? $_POST['origin'] : 'Việt Nam';
            $product['warranty_type'] = isset($_POST['warranty_type']) ? $_POST['warranty_type'] : 'Bảo hành nhà sản xuất';
            $product['manufacturer_name'] = isset($_POST['manufacturer_name']) ? $_POST['manufacturer_name'] : 'HEYP SOAP';
            $product['manufacturer_address'] = isset($_POST['manufacturer_address']) ? $_POST['manufacturer_address'] : '70 Buôn Sút M\'gưr xã Cư Suê, huyện Cư M\'';
            $product['shipping_from'] = isset($_POST['shipping_from']) ? $_POST['shipping_from'] : 'Đắk Lắk';
            $product['material'] = isset($_POST['material']) ? $_POST['material'] : 'Thô';
            $product['usage_instructions'] = isset($_POST['usage_instructions']) ? $_POST['usage_instructions'] : '';

            // Get size data
            $size_ids = isset($_POST['size_id']) ? $_POST['size_id'] : [];
            $size_names = isset($_POST['size']) ? $_POST['size'] : [];
            $size_prices = isset($_POST['size_price']) ? $_POST['size_price'] : [];
            $display_orders = isset($_POST['display_order']) ? $_POST['display_order'] : [];

            if (empty($product['name'])) {
                $error = 'Tên sản phẩm không được để trống';
            } elseif (empty($product['price']) || !is_numeric($product['price'])) {
                $error = 'Giá sản phẩm không hợp lệ';
            } elseif (empty($product['category_id'])) {
                $error = 'Vui lòng chọn danh mục sản phẩm';
            } elseif (empty($product['shopee_link'])) {
                $error = 'Link Shopee không được để trống';
            } elseif (!filter_var($product['shopee_link'], FILTER_VALIDATE_URL)) {
                $error = 'Link Shopee không hợp lệ';
            } else {
                // Handle image upload
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    error_log("Image upload detected: " . $_FILES['image']['name']);

                    try {
                        $imageInfo = $this->product->processImageUpload($_FILES['image']);
                        if ($imageInfo) {
                            $product['image'] = $imageInfo['filename'];
                            $product['image_data'] = $imageInfo['data'];
                            $product['image_mime_type'] = $imageInfo['mime_type'];
                            error_log("Image processed successfully: " . $imageInfo['filename']);
                        } else {
                            error_log("Failed to process image");
                        }
                    } catch (\Exception $e) {
                        error_log("Image processing error: " . $e->getMessage());
                    }
                } elseif (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                    error_log("Image upload error: " . $_FILES['image']['error']);
                }

                // Handle additional images upload
                if (isset($_FILES['additional_images']) && is_array($_FILES['additional_images']['name'])) {
                    // Filter out empty file names
                    $validFiles = array_filter($_FILES['additional_images']['name'], function($name) {
                        return !empty($name);
                    });

                    if (count($validFiles) > 0) {
                        error_log("Additional images upload detected: " . count($validFiles) . " files");
                        error_log("Files data: " . print_r($_FILES['additional_images'], true));

                        // Get current max display_order for this product
                        $existingImages = $this->productImage->getByProductId($id);
                        $maxDisplayOrder = 0;
                        if (!empty($existingImages)) {
                            foreach ($existingImages as $img) {
                                if ($img['display_order'] > $maxDisplayOrder) {
                                    $maxDisplayOrder = $img['display_order'];
                                }
                            }
                        }
                        error_log("Current max display_order: " . $maxDisplayOrder);

                        $uploadDir = 'public/img/products/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }

                        $total = count($_FILES['additional_images']['name']);
                        $processedCount = 0;

                        for ($i = 0; $i < $total; $i++) {
                            // Skip empty files
                            if (empty($_FILES['additional_images']['name'][$i])) {
                                continue;
                            }

                            if ($_FILES['additional_images']['error'][$i] === UPLOAD_ERR_OK) {
                                error_log("Processing additional image: " . $_FILES['additional_images']['name'][$i]);

                                try {
                                    // Create temporary file array for processing
                                    $tempFile = [
                                        'name' => $_FILES['additional_images']['name'][$i],
                                        'type' => $_FILES['additional_images']['type'][$i],
                                        'tmp_name' => $_FILES['additional_images']['tmp_name'][$i],
                                        'error' => $_FILES['additional_images']['error'][$i],
                                        'size' => $_FILES['additional_images']['size'][$i]
                                    ];

                                    $imageInfo = $this->productImage->processImageUpload($tempFile);
                                    if ($imageInfo) {
                                        // Use incremental display_order starting from max + 1
                                        $newDisplayOrder = $maxDisplayOrder + $processedCount + 1;
                                        $result = $this->productImage->add($id, $imageInfo['filename'], $newDisplayOrder, $imageInfo['data'], $imageInfo['mime_type']);
                                        error_log("Additional image processed successfully: " . $imageInfo['filename'] . " - Display order: " . $newDisplayOrder . " - DB result: " . ($result ? 'success' : 'failed'));
                                        $processedCount++;
                                    } else {
                                        error_log("Failed to process additional image");
                                    }
                                } catch (\Exception $e) {
                                    error_log("Additional image processing error: " . $e->getMessage());
                                }
                            } else {
                                error_log("Additional image upload error for file " . $i . ": " . $_FILES['additional_images']['error'][$i]);
                            }
                        }
                    } else {
                        error_log("No valid additional images to upload");
                    }
                } else {
                    error_log("No additional_images in FILES or not an array");
                }

                // Handle video upload
                if (isset($_FILES['video'])) {
                    if ($_FILES['video']['error'] === UPLOAD_ERR_OK) {
                        try {
                            error_log("Video upload detected: " . $_FILES['video']['name']);

                            $videoUploadDir = APP_ROOT . '/public/videos/products/';
                            if (!is_dir($videoUploadDir)) {
                                mkdir($videoUploadDir, 0777, true);
                                error_log("Created video upload directory: " . $videoUploadDir);
                            }

                            $videoFileName = time() . '_' . basename($_FILES['video']['name']);
                            $videoUploadFile = $videoUploadDir . $videoFileName;
                            $videoType = $this->productVideo->getVideoType($_FILES['video']['name']);

                            error_log("Attempting to upload video to: " . $videoUploadFile);

                            if ($this->productVideo->isAllowedVideoType($_FILES['video']['name'])) {
                                if (move_uploaded_file($_FILES['video']['tmp_name'], $videoUploadFile)) {
                                    error_log("Video uploaded successfully: " . $videoFileName);

                                    // Remove old video if exists
                                    $existingVideo = $this->productVideo->getMainVideo($id);
                                    if ($existingVideo) {
                                        $oldVideoFile = $videoUploadDir . $existingVideo['video_filename'];
                                        if (file_exists($oldVideoFile)) {
                                            unlink($oldVideoFile);
                                            error_log("Removed old video: " . $oldVideoFile);
                                        }
                                        // Delete old video record
                                        $this->productVideo->delete($existingVideo['id']);
                                    }

                                    // Add new video
                                    $result = $this->productVideo->add($id, $videoFileName, $videoType, 0);
                                    if (!$result) {
                                        throw new \Exception('Không thể lưu thông tin video vào database');
                                    }
                                } else {
                                    throw new \Exception('Không thể upload file video');
                                }
                            } else {
                                throw new \Exception('Định dạng video không được hỗ trợ: ' . $videoType);
                            }
                        } catch (\Exception $e) {
                            error_log("Video upload error: " . $e->getMessage());
                            $error = 'Lỗi upload video: ' . $e->getMessage();
                        }
                    } elseif ($_FILES['video']['error'] !== UPLOAD_ERR_NO_FILE) {
                        $errorMsg = '';
                        switch ($_FILES['video']['error']) {
                            case UPLOAD_ERR_INI_SIZE:
                            case UPLOAD_ERR_FORM_SIZE:
                                $errorMsg = 'File video quá lớn';
                                break;
                            case UPLOAD_ERR_PARTIAL:
                                $errorMsg = 'File video chỉ được upload một phần';
                                break;
                            default:
                                $errorMsg = 'Lỗi upload video: ' . $_FILES['video']['error'];
                        }
                        error_log('Video upload error: ' . $errorMsg);
                        $error = $errorMsg;
                    }
                }

                try {
                    // Cập nhật thông tin sản phẩm
                    error_log("=== UPDATING PRODUCT ===");
                    error_log("Product ID: " . $id);
                    error_log("Product data: " . print_r($product, true));

                    $result = $this->product->update($id, $product);
                    error_log("Update result: " . ($result ? 'success' : 'failed'));

                    if (!$result) {
                        throw new \Exception('Không thể cập nhật sản phẩm');
                    }

                    // Xử lý sizes với ảnh
                    $size_images = isset($_FILES['size_images']) ? $_FILES['size_images'] : null;
                    $existing_size_images = isset($_POST['existing_size_images']) ? $_POST['existing_size_images'] : [];
                    $delete_size_images = isset($_POST['delete_size_images']) ? $_POST['delete_size_images'] : [];

                    // Debug log
                    file_put_contents('/tmp/debug_size_images.log', "Delete size images: " . print_r($delete_size_images, true) . "\n", FILE_APPEND);
                    file_put_contents('/tmp/debug_size_images.log', "Existing size images: " . print_r($existing_size_images, true) . "\n", FILE_APPEND);

                    $this->updateProductSizes($id, $size_ids, $size_names, $size_prices, $display_orders, $size_images, $existing_size_images, $delete_size_images);
                    
                    // Xử lý custom fields
                    error_log("Processing custom fields for product ID: " . $id);
                    error_log("POST data custom fields: " . print_r([
                        'custom_field_title' => isset($_POST['custom_field_title']) ? $_POST['custom_field_title'] : 'NOT SET',
                        'custom_field_content' => isset($_POST['custom_field_content']) ? $_POST['custom_field_content'] : 'NOT SET'
                    ], true));

                    // Luôn xóa tất cả custom fields cũ trước
                    $deleteResult = $this->productCustomField->deleteByProductId($id);
                    error_log("Delete old custom fields result: " . ($deleteResult ? 'success' : 'failed'));

                    if (isset($_POST['custom_field_title']) && isset($_POST['custom_field_content'])) {
                        error_log("Custom field data found in POST");

                        $customTitles = $_POST['custom_field_title'];
                        $customContents = $_POST['custom_field_content'];

                        error_log("Custom titles: " . print_r($customTitles, true));
                        error_log("Custom contents: " . print_r($customContents, true));

                        for ($i = 0; $i < count($customTitles); $i++) {
                            // Trim whitespace và kiểm tra
                            $title = isset($customTitles[$i]) ? trim($customTitles[$i]) : '';
                            $content = isset($customContents[$i]) ? trim($customContents[$i]) : '';

                            if (!empty($title) && !empty($content)) {
                                error_log("Creating custom field " . ($i + 1) . ": " . $title);

                                $createResult = $this->productCustomField->create([
                                    'product_id' => $id,
                                    'field_title' => $title,
                                    'field_content' => $content,
                                    'display_order' => $i + 1
                                ]);

                                error_log("Custom field create result: " . ($createResult ? 'success' : 'failed'));
                            } else {
                                error_log("Skipping empty custom field at index " . $i . " - Title: '$title', Content: '$content'");
                            }
                        }
                    } else {
                        error_log("No custom field data found in POST");
                    }

                    // Redirect về chính trang edit với thông báo thành công
                    header('Location: ' . URL_ROOT . '/admin/products/edit/' . $id . '?success=' . urlencode('Sản phẩm đã được cập nhật thành công'));
                    exit;
                } catch (\Exception $e) {
                    $error = 'Có lỗi xảy ra khi cập nhật sản phẩm: ' . $e->getMessage();
                }
            }
        }
        
        $this->view('admin/products/editproduct', [
            'title' => 'Cập nhật sản phẩm',
            'product' => $product,
            'categories' => $categories,
            'sizes' => $sizes,
            'customFields' => $customFields,
            'error' => $error,
            'action' => 'edit'
        ]);
    }
    
    public function deleteProduct() {
        // Get ID from URL parameters
        $args = func_get_args();
        $id = isset($args[0]) ? $args[0] : null;

        if ($id === null) {
            header('Location: ' . URL_ROOT . '/admin/products');
            exit;
        }
        $product = $this->product->getById($id);

        if ($product) {
            try {
                // Bắt đầu giao dịch để đảm bảo tính toàn vẹn dữ liệu
                $this->product->beginTransaction();

                // 1. Xóa tất cả sizes liên quan đến sản phẩm
                $this->product->deleteSizes($id);

                // 2. Xóa custom fields của sản phẩm
                $this->productCustomField->deleteByProductId($id);

                // 3. Xóa các ảnh bổ sung của sản phẩm
                $additionalImages = $this->productImage->getByProductId($id);

                foreach ($additionalImages as $img) {
                    $imagePath = 'public/img/products/' . $img['image_filename'];
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
                $this->productImage->deleteByProductId($id);

                // 4. Xóa video của sản phẩm
                $productVideos = $this->productVideo->getByProductId($id);
                foreach ($productVideos as $video) {
                    $videoPath = APP_ROOT . '/public/videos/products/' . $video['video_filename'];
                    if (file_exists($videoPath)) {
                        unlink($videoPath);
                    }
                }
                $this->productVideo->deleteByProductId($id);

                // 5. Xóa ảnh chính của sản phẩm
                if (!empty($product['image'])) {
                    $mainImagePath = 'public/img/products/' . $product['image'];
                    if (file_exists($mainImagePath)) {
                        unlink($mainImagePath);
                    }
                }

                // 6. Xóa sản phẩm
                if ($this->product->delete($id)) {
                    // Xác nhận giao dịch
                    $this->product->commit();
                    $successMessage = 'Sản phẩm đã được xóa thành công';
                    header('Location: ' . URL_ROOT . '/admin/products?success=' . urlencode($successMessage));
                } else {
                    // Rollback nếu không xóa được sản phẩm
                    $this->product->rollBack();
                    $errorMessage = 'Không thể xóa sản phẩm';
                    header('Location: ' . URL_ROOT . '/admin/products?error=' . urlencode($errorMessage));
                }
            } catch (\Exception $e) {
                // Rollback trong trường hợp có lỗi
                $this->product->rollBack();
                $errorMessage = 'Đã xảy ra lỗi: ' . $e->getMessage();
                header('Location: ' . URL_ROOT . '/admin/products?error=' . urlencode($errorMessage));
            }
        } else {
            $errorMessage = 'Không tìm thấy sản phẩm';
            header('Location: ' . URL_ROOT . '/admin/products?error=' . urlencode($errorMessage));
        }

        exit;
    }

    public function toggleStock() {
        // Ensure no output before this point
        ob_clean();
        header('Content-Type: application/json');

        // Check authentication for AJAX requests
        if (!isset($_SESSION['admin_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        // Log for debugging
        error_log("toggleStock input: " . json_encode($input));

        if (!isset($input['product_id']) || !isset($input['out_of_stock'])) {
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
            exit;
        }

        $product_id = (int)$input['product_id'];
        $out_of_stock = (int)$input['out_of_stock'];

        error_log("toggleStock: product_id=$product_id, out_of_stock=$out_of_stock");

        try {
            $db = new \App\Core\Database();
            $db->query("UPDATE products SET out_of_stock = :out_of_stock WHERE id = :id");
            $db->bind(':out_of_stock', $out_of_stock);
            $db->bind(':id', $product_id);

            if ($db->execute()) {
                echo json_encode(['success' => true, 'message' => 'Trạng thái hàng đã được cập nhật']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể cập nhật trạng thái hàng']);
            }
        } catch (Exception $e) {
            error_log("toggleStock error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Lỗi database: ' . $e->getMessage()]);
        }
        exit;
    }

    public function deleteMultiple() {
        // Ensure no output before this point
        if (ob_get_level()) {
            ob_clean();
        }
        header('Content-Type: application/json');

        // Check authentication for AJAX requests
        if (!isset($_SESSION['admin_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        try {
            // Get JSON input
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            if (!$data || !isset($data['productIds']) || !is_array($data['productIds'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid input data']);
                exit;
            }

            $productIds = $data['productIds'];

            if (empty($productIds)) {
                echo json_encode(['success' => false, 'message' => 'No products selected']);
                exit;
            }

            $deletedCount = 0;
            $errors = [];

            // Begin transaction
            $this->product->beginTransaction();

            foreach ($productIds as $id) {
                try {
                    // Validate product ID
                    if (!is_numeric($id) || $id <= 0) {
                        $errors[] = "Invalid product ID: $id";
                        continue;
                    }

                    // Get product info
                    $productInfo = $this->product->getById($id);
                    if (!$productInfo) {
                        $errors[] = "Product not found: $id";
                        continue;
                    }

                    // 1. Delete product sizes
                    $this->product->deleteSizes($id);

                    // 2. Delete custom fields
                    $this->productCustomField->deleteByProductId($id);

                    // 3. Delete additional images and files
                    $additionalImages = $this->productImage->getByProductId($id);
                    foreach ($additionalImages as $img) {
                        $imagePath = 'public/img/products/' . $img['image_filename'];
                        if (file_exists($imagePath)) {
                            unlink($imagePath);
                        }
                    }
                    $this->productImage->deleteByProductId($id);

                    // 4. Delete videos and files
                    $productVideos = $this->productVideo->getByProductId($id);
                    foreach ($productVideos as $video) {
                        $videoPath = getPublicPath('videos/products/' . $video['video_filename']);
                        if (file_exists($videoPath)) {
                            unlink($videoPath);
                        }
                    }
                    $this->productVideo->deleteByProductId($id);

                    // 5. Delete main product image
                    if (!empty($productInfo['image'])) {
                        $mainImagePath = 'public/img/products/' . $productInfo['image'];
                        if (file_exists($mainImagePath)) {
                            unlink($mainImagePath);
                        }
                    }

                    // 6. Delete the product itself
                    if ($this->product->delete($id)) {
                        $deletedCount++;
                    } else {
                        $errors[] = "Failed to delete product: {$productInfo['name']} (ID: $id)";
                    }

                } catch (Exception $e) {
                    $errors[] = "Error deleting product ID $id: " . $e->getMessage();
                }
            }

            // Commit transaction if any products were deleted
            if ($deletedCount > 0) {
                $this->product->commit();
            } else {
                $this->product->rollBack();
            }

            // Prepare response
            $response = [
                'success' => $deletedCount > 0,
                'deletedCount' => $deletedCount,
                'totalRequested' => count($productIds)
            ];

            if (!empty($errors)) {
                $response['errors'] = $errors;
                $response['message'] = "Deleted $deletedCount out of " . count($productIds) . " products. Some errors occurred.";
            } else {
                $response['message'] = "Successfully deleted $deletedCount product(s).";
            }

            echo json_encode($response);

        } catch (Exception $e) {
            // Rollback on error
            if (isset($this->product)) {
                $this->product->rollBack();
            }

            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    // Settings
    public function settings() {
        $error = '';
        $success = '';
        $activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'account';

        // Kiểm tra session admin_id
        if (!isset($_SESSION['admin_id'])) {
            redirect('admin');
            exit;
        }

        // Lấy thông tin admin
        $admin = $this->admin->getById($_SESSION['admin_id']);
        
        // Lấy các cài đặt website
        $siteSettings = [
            'site_name' => $this->setting->get('site_name') ?: 'HeypVietNam',
            'site_description' => $this->setting->get('site_description') ?: 'Trang web giới thiệu về cà phê Việt Nam',
            'site_keywords' => $this->setting->get('site_keywords') ?: 'cà phê, việt nam, coffee, heyp vietnam',
            'contact_email' => $this->setting->get('contact_email') ?: '',
            'contact_phone' => $this->setting->get('contact_phone') ?: '',
            'contact_address' => $this->setting->get('contact_address') ?: '',
            'social_facebook' => $this->setting->get('social_facebook') ?: '',
            'social_instagram' => $this->setting->get('social_instagram') ?: '',
            'social_youtube' => $this->setting->get('social_youtube') ?: '',
            'about_content' => $this->setting->get('about_content') ?: ''
        ];
        
        // Xử lý đổi mật khẩu
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tab']) && $_POST['tab'] === 'password') {
            $current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
            $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
            $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
            
            if (empty($current_password)) {
                $error = 'Vui lòng nhập mật khẩu hiện tại';
            } elseif (empty($new_password)) {
                $error = 'Vui lòng nhập mật khẩu mới';
            } elseif ($new_password !== $confirm_password) {
                $error = 'Mật khẩu mới không khớp';
            } else {
                if (password_verify($current_password, $admin['password'])) {
                    $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
                    $result = $this->admin->updatePassword($_SESSION['admin_id'], $hashedPassword);
                    
                    if ($result) {
                        $success = 'Mật khẩu đã được cập nhật thành công';
                    } else {
                        $error = 'Có lỗi xảy ra khi cập nhật mật khẩu';
                    }
                } else {
                    $error = 'Mật khẩu hiện tại không đúng';
                }
            }
            $activeTab = 'password';
        }
        
        // Xử lý cập nhật thông tin tài khoản
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tab']) && $_POST['tab'] === 'account') {
            $admin_name = isset($_POST['admin_name']) ? trim($_POST['admin_name']) : '';
            $admin_email = isset($_POST['admin_email']) ? trim($_POST['admin_email']) : '';
            $admin_username = isset($_POST['admin_username']) ? trim($_POST['admin_username']) : '';
            
            if (empty($admin_name)) {
                $error = 'Vui lòng nhập tên hiển thị';
            } elseif (empty($admin_username)) {
                $error = 'Vui lòng nhập tên đăng nhập';
            } else {
                // Kiểm tra xem tên đăng nhập mới có trùng với tài khoản khác không
                if ($admin_username !== $admin['username']) {
                    $existingAdmin = $this->admin->getByUsername($admin_username);
                    if ($existingAdmin && $existingAdmin['id'] != $_SESSION['admin_id']) {
                        $error = 'Tên đăng nhập đã tồn tại';
                        $activeTab = 'account';
                        
                        $this->view('admin/settings', [
                            'title' => 'Cài đặt',
                            'error' => $error,
                            'success' => $success,
                            'activeTab' => $activeTab,
                            'admin' => $admin,
                            'siteSettings' => $siteSettings
                        ]);
                        return;
                    }
                }
                
                // Cập nhật thông tin tài khoản
                $result = $this->admin->updateInfo($_SESSION['admin_id'], [
                    'name' => $admin_name,
                    'email' => $admin_email,
                    'username' => $admin_username
                ]);
                
                if ($result) {
                    $_SESSION['admin_name'] = $admin_name;
                    $_SESSION['admin_username'] = $admin_username;
                    $success = 'Thông tin tài khoản đã được cập nhật thành công';
                    // Cập nhật thông tin admin hiện tại
                    $admin = $this->admin->getById($_SESSION['admin_id']);
                } else {
                    $error = 'Có lỗi xảy ra khi cập nhật thông tin tài khoản';
                }
                $activeTab = 'account';
            }
        }
        
        // Xử lý cập nhật cài đặt trang web
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tab']) && $_POST['tab'] === 'site') {
            $settings = [
                'site_name' => isset($_POST['site_name']) ? trim($_POST['site_name']) : '',
                'site_description' => isset($_POST['site_description']) ? trim($_POST['site_description']) : '',
                'site_keywords' => isset($_POST['site_keywords']) ? trim($_POST['site_keywords']) : '',
                'contact_email' => isset($_POST['contact_email']) ? trim($_POST['contact_email']) : '',
                'contact_phone' => isset($_POST['contact_phone']) ? trim($_POST['contact_phone']) : '',
                'contact_address' => isset($_POST['contact_address']) ? trim($_POST['contact_address']) : '',
                'social_facebook' => isset($_POST['social_facebook']) ? trim($_POST['social_facebook']) : '',
                'social_instagram' => isset($_POST['social_instagram']) ? trim($_POST['social_instagram']) : '',
                'social_youtube' => isset($_POST['social_youtube']) ? trim($_POST['social_youtube']) : '',
                'about_content' => isset($_POST['about_content']) ? trim($_POST['about_content']) : ''
            ];
            
            // Cập nhật cài đặt trang web
            $result = $this->setting->updateMultiple($settings);
            
            if ($result) {
                $success = 'Cài đặt trang web đã được cập nhật thành công';
                // Cập nhật lại các giá trị cài đặt
                $siteSettings = $settings;
            } else {
                $error = 'Có lỗi xảy ra khi cập nhật cài đặt trang web';
            }
            $activeTab = 'site';
        }
        
        $this->view('admin/settings', [
            'title' => 'Cài đặt',
            'error' => $error,
            'success' => $success,
            'activeTab' => $activeTab,
            'admin' => $admin,
            'siteSettings' => $siteSettings
        ]);
    }

    // Helper method to update product sizes
    private function updateProductSizes($product_id, $size_ids, $size_names, $size_prices, $display_orders, $size_images = null, $existing_size_images = [], $delete_size_images = [], $db = null) {
        if ($db === null) {
            $db = new \App\Core\Database();
        }
        // Lấy danh sách sizes hiện tại
        $existing_sizes = $this->product->getSizesByProductId($product_id);
        $existing_size_ids = array_column($existing_sizes, 'id');

        $processed_size_ids = [];

        // Xử lý từng size
        for ($i = 0; $i < count($size_names); $i++) {
            if (empty($size_names[$i])) continue;

            $size_id = isset($size_ids[$i]) ? intval($size_ids[$i]) : 0;
            $size_name = $size_names[$i];
            $size_price = (is_numeric($size_prices[$i])) ? $size_prices[$i] : 0;
            $display_order = isset($display_orders[$i]) ? intval($display_orders[$i]) : $i;

            // Xử lý ảnh size
            $size_image = null;

            // Kiểm tra xem ảnh có được đánh dấu để xóa không
            $should_delete_image = in_array($size_id, $delete_size_images);

            // Debug log
            if ($should_delete_image) {
                file_put_contents('/tmp/debug_size_images.log', "Size ID {$size_id} marked for image deletion\n", FILE_APPEND);
            }

            if ($size_images && isset($size_images['name'][$i]) && $size_images['error'][$i] === UPLOAD_ERR_OK) {
                // Upload ảnh mới
                $upload_dir = getPublicPath('img/products/sizes/');
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $file_name = time() . '_size_' . $i . '_' . basename($size_images['name'][$i]);
                $upload_file = $upload_dir . $file_name;

                if (move_uploaded_file($size_images['tmp_name'][$i], $upload_file)) {
                    $size_image = $file_name;

                    // Xóa ảnh cũ nếu có
                    if (isset($existing_size_images[$i]) && !empty($existing_size_images[$i])) {
                        $old_file = $upload_dir . $existing_size_images[$i];
                        if (file_exists($old_file)) {
                            unlink($old_file);
                        }
                    }
                }
            } else if (!$should_delete_image && isset($existing_size_images[$i]) && !empty($existing_size_images[$i])) {
                // Giữ ảnh cũ (chỉ khi không được đánh dấu để xóa)
                $size_image = $existing_size_images[$i];
            } else if ($should_delete_image && isset($existing_size_images[$i]) && !empty($existing_size_images[$i])) {
                // Xóa ảnh cũ khỏi server
                $upload_dir = getPublicPath('img/products/sizes/');
                $old_file = $upload_dir . $existing_size_images[$i];
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
                // $size_image sẽ là null (không có ảnh)
            }

            if ($size_id > 0 && in_array($size_id, $existing_size_ids)) {
                // Cập nhật size hiện có
                $this->updateProductSize($size_id, $size_name, $size_price, $display_order, $size_image, $db);
                $processed_size_ids[] = $size_id;
            } else {
                // Thêm size mới
                $new_size_id = $this->addProductSize($product_id, $size_name, $size_price, $display_order, $size_image, $db);
                if ($new_size_id) {
                    $processed_size_ids[] = $new_size_id;
                }
            }
        }

        // Xóa các sizes không còn tồn tại
        $sizes_to_delete = array_diff($existing_size_ids, $processed_size_ids);
        foreach ($sizes_to_delete as $size_id) {
            $this->deleteProductSize($size_id, $db);
        }
    }

    // Helper method to update a product size
    private function updateProductSize($size_id, $size_name, $price, $display_order, $image = null, $db = null) {
        if ($db === null) {
            $db = new \App\Core\Database();
        }

        if ($image !== null) {
            $db->query("UPDATE product_sizes SET size_name = :size_name, size_value = :size_value, price = :price, display_order = :display_order, image = :image WHERE id = :id");
            $db->bind(':image', $image);
        } else {
            $db->query("UPDATE product_sizes SET size_name = :size_name, size_value = :size_value, price = :price, display_order = :display_order WHERE id = :id");
        }

        $db->bind(':size_name', $size_name);
        $db->bind(':size_value', $size_name); // Sử dụng size_name làm size_value
        $db->bind(':price', $price);
        $db->bind(':display_order', $display_order);
        $db->bind(':id', $size_id);
        return $db->execute();
    }

    // Helper method to add a product size
    private function addProductSize($product_id, $size_name, $price, $display_order, $image = null, $db = null) {
        if ($db === null) {
            $db = new \App\Core\Database();
        }
        $db->query("INSERT INTO product_sizes (product_id, size_name, size_value, price, display_order, image) VALUES (:product_id, :size_name, :size_value, :price, :display_order, :image)");
        $db->bind(':product_id', $product_id);
        $db->bind(':size_name', $size_name);
        $db->bind(':size_value', $size_name); // Sử dụng size_name làm size_value
        $db->bind(':price', $price);
        $db->bind(':display_order', $display_order);
        $db->bind(':image', $image);

        if ($db->execute()) {
            // Lấy ID của size vừa tạo
            $db->query("SELECT LAST_INSERT_ID() as id");
            $result = $db->single();
            return $result['id'];
        }
        return false;
    }

    // Helper method to delete a product size
    private function deleteProductSize($size_id, $db = null) {
        if ($db === null) {
            $db = new \App\Core\Database();
        }
        $db->query("DELETE FROM product_sizes WHERE id = :id");
        $db->bind(':id', $size_id);
        return $db->execute();
    }

    // Logout
    public function logout() {
        // Xóa tất cả session admin
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_name']);
        unset($_SESSION['admin_username']);
        unset($_SESSION['admin_logged_in']);

        // Hủy session hoàn toàn nếu không có dữ liệu khác
        if (empty($_SESSION)) {
            session_destroy();
        }

        redirect('admin');
        exit;
    }
}
