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
require_once dirname(__FILE__) . '/../helpers/FooterSettings.php';

use App\Core\Controller;

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
    protected $admin;
    protected $setting;
    protected $settingManager;
    protected $page;
    
    public function __construct() {
        // Clean any output buffer to prevent redirect issues
        if (ob_get_level()) {
            ob_clean();
        }

        parent::__construct();

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

        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true && $currentAction === 'dashboard') {
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

    public function footer() {
        $message = '';
        $error = '';
        $defaultFooter = heypFooterDefaultCanvasContent();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rawJson = $_POST['content_json'] ?? '';
            $decoded = json_decode($rawJson, true);

            if (!is_array($decoded)) {
                $error = 'JSON footer không hợp lệ. Vui lòng kiểm tra lại nội dung.';
            } else {
                $footer = heypFooterNormalizeCanvasContent($decoded);
                $json = json_encode($footer, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                if ($json === false) {
                    $error = 'Không thể lưu footer: ' . json_last_error_msg();
                } elseif ($this->setting->set('footer_content', $json)) {
                    $message = 'Đã lưu footer.';
                } else {
                    $error = 'Có lỗi xảy ra khi lưu footer.';
                }
            }
        }

        $footer = $this->getFooterCanvasContent();

        $this->view('admin/pages/canvas-editor', [
            'title' => 'Footer Canvas Editor',
            'page' => $footer,
            'page_json' => json_encode($footer, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT),
            'default_elements_json' => json_encode($defaultFooter['elements'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT),
            'message' => $message,
            'error' => $error,
            'editorTitle' => 'Footer Canvas Editor - HeypVietNam Admin',
            'editorEyebrow' => 'Footer canvas editor',
            'editorHeading' => 'Footer editor',
            'editorNote' => 'Kéo thả, resize, đổi layer và gắn link cho từng thành phần footer.',
            'formAction' => URL_ROOT . '/admin/footer',
            'activeEditor' => 'footer',
            'saveButtonLabel' => 'Save footer',
            'canvasDefaults' => [
                'width' => 1200,
                'height' => 432,
                'minHeight' => 160,
                'maxHeight' => 2400,
                'bottomPadding' => 8,
                'backgroundColor' => '#6a7260',
                'autoFitHeight' => true
            ]
        ]);
    }

    private function getFooterCanvasContent() {
        $raw = $this->setting->get('footer_content');
        $decoded = json_decode((string) $raw, true);

        return heypFooterNormalizeCanvasContent(is_array($decoded) ? $decoded : []);
    }

    private function defaultLandingPageContent() {
        return [
            'header' => [
                'logo' => 'public/img/logoHEYP.png'
            ],
            'sections' => [
                [
                    'id' => 'home',
                    'heading' => [
                        'level' => 1,
                        'text' => 'Sản phẩm xanh cho lối sống bền vững',
                        'anchorId' => 'home'
                    ],
                    'blocks' => [
                        [
                            'type' => 'text',
                            'content' => 'HEYP cung cấp các sản phẩm làm sạch trong gia đình từ xà phòng truyền thống, thân thiện môi trường.'
                        ],
                        [
                            'type' => 'image',
                            'src' => 'public/img/logoHEYP.png',
                            'alt' => 'Heyp Logo',
                            'caption' => ''
                        ]
                    ]
                ],
                [
                    'id' => 'about-heyp',
                    'heading' => [
                        'level' => 1,
                        'text' => 'Về HEYP',
                        'anchorId' => 'about-heyp'
                    ],
                    'blocks' => [
                        [
                            'type' => 'heading',
                            'level' => 2,
                            'content' => 'Thương hiệu địa phương tại Daklak'
                        ],
                        [
                            'type' => 'text',
                            'content' => 'HEYP hướng tới cung cấp các sản phẩm làm sạch, bảo vệ cho gia đình bạn, được làm từ nguyên liệu thiên nhiên, không phụ gia.'
                        ]
                    ]
                ]
            ]
        ];
    }

    private function normalizeLandingPageContent(array $page) {
        if (($page['layoutType'] ?? '') === 'canvas' || isset($page['elements'])) {
            $page = $this->convertCanvasToLandingSections($page);
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

            $normalizedHeading = [];
            if (!empty($heading)) {
                $headingLevel = isset($heading['level']) ? (int) $heading['level'] : 1;
                $normalizedHeading = [
                    'level' => in_array($headingLevel, [1, 2, 3], true) ? $headingLevel : 1,
                    'text' => (string) ($heading['text'] ?? ''),
                    'anchorId' => $sectionId
                ];
            }

            $blocks = [];
            foreach (($section['blocks'] ?? []) as $block) {
                if (!is_array($block)) {
                    continue;
                }

                $type = $block['type'] ?? '';
                if ($type === 'heading') {
                    $level = isset($block['level']) ? (int) $block['level'] : 2;
                    $content = trim((string) ($block['content'] ?? $block['text'] ?? ''));
                    if ($content === '') {
                        continue;
                    }

                    $blocks[] = [
                        'type' => 'heading',
                        'level' => in_array($level, [1, 2, 3], true) ? $level : 2,
                        'content' => $content
                    ];
                } elseif ($type === 'text') {
                    $content = trim((string) ($block['content'] ?? ''));
                    if ($content === '') {
                        continue;
                    }

                    $blocks[] = [
                        'type' => 'text',
                        'content' => $content
                    ];
                } elseif ($type === 'image') {
                    $src = trim((string) ($block['src'] ?? $block['url'] ?? $block['image_url'] ?? ''));
                    if ($src === '') {
                        continue;
                    }

                    $blocks[] = [
                        'type' => 'image',
                        'src' => $src,
                        'alt' => (string) ($block['alt'] ?? ''),
                        'caption' => (string) ($block['caption'] ?? '')
                    ];
                }
            }

            $sections[] = [
                'id' => $sectionId,
                'heading' => $normalizedHeading,
                'blocks' => $blocks
            ];
        }

        $page['sections'] = $sections;
        unset($page['layoutType'], $page['canvas'], $page['elements']);
        return $page;
    }

    private function convertCanvasToLandingSections(array $page) {
        $elements = isset($page['elements']) && is_array($page['elements']) ? $page['elements'] : [];

        usort($elements, function($a, $b) {
            $aY = is_array($a) && is_numeric($a['y'] ?? null) ? (int) $a['y'] : 0;
            $bY = is_array($b) && is_numeric($b['y'] ?? null) ? (int) $b['y'] : 0;
            if ($aY !== $bY) {
                return $aY <=> $bY;
            }

            $aX = is_array($a) && is_numeric($a['x'] ?? null) ? (int) $a['x'] : 0;
            $bX = is_array($b) && is_numeric($b['x'] ?? null) ? (int) $b['x'] : 0;
            return $aX <=> $bX;
        });

        $sections = [];
        $currentIndex = null;

        foreach ($elements as $element) {
            if (!is_array($element)) {
                continue;
            }

            $type = $element['type'] ?? 'text';
            if (!in_array($type, ['text', 'heading', 'image'], true)) {
                continue;
            }

            $content = trim((string) ($element['content'] ?? ''));
            $style = isset($element['style']) && is_array($element['style']) ? $element['style'] : [];
            $fontSize = is_numeric($style['fontSize'] ?? null) ? (int) $style['fontSize'] : 0;
            $headingLevel = is_numeric($element['headingLevel'] ?? $element['level'] ?? null)
                ? (int) ($element['headingLevel'] ?? $element['level'])
                : 0;
            $isH1 = $type !== 'image' && ($headingLevel === 1 || $fontSize >= 32);

            if ($isH1 && $content !== '') {
                $sectionId = $this->normalizeLandingAnchorId($element['id'] ?? $content, 'section-' . (count($sections) + 1));
                $sections[] = [
                    'id' => $sectionId,
                    'heading' => [
                        'level' => 1,
                        'text' => $content,
                        'anchorId' => $sectionId
                    ],
                    'blocks' => []
                ];
                $currentIndex = count($sections) - 1;
                continue;
            }

            if ($currentIndex === null) {
                $sections[] = [
                    'id' => 'home',
                    'heading' => [
                        'level' => 1,
                        'text' => 'Trang Chủ',
                        'anchorId' => 'home'
                    ],
                    'blocks' => []
                ];
                $currentIndex = 0;
            }

            if ($type === 'image') {
                $src = (string) ($element['src'] ?? $element['image_url'] ?? $element['url'] ?? '');
                if ($src !== '') {
                    $sections[$currentIndex]['blocks'][] = [
                        'type' => 'image',
                        'src' => $src,
                        'alt' => $content,
                        'caption' => ''
                    ];
                }
                continue;
            }

            if ($content === '') {
                continue;
            }

            $level = in_array($headingLevel, [2, 3], true) ? $headingLevel : ($fontSize >= 28 ? 2 : 0);
            $sections[$currentIndex]['blocks'][] = $level > 0
                ? [
                    'type' => 'heading',
                    'level' => $level,
                    'content' => $content
                ]
                : [
                    'type' => 'text',
                    'content' => $content
                ];
        }

        if (empty($sections)) {
            $sections = $this->defaultLandingPageContent()['sections'];
        }

        return [
            'header' => isset($page['header']) && is_array($page['header']) ? $page['header'] : ['logo' => 'public/img/logoHEYP.png'],
            'sections' => $sections
        ];
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
        $canvasHeight = 320;
        foreach ($elements as $element) {
            $canvasHeight = max($canvasHeight, (int) $element['y'] + (int) $element['height'] + 8);
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
        $headingLevel = $this->normalizeLandingNumber($element['headingLevel'] ?? $element['level'] ?? 0, 0, 3, 0);

        $normalized = [
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

        if ($type === 'text' && $headingLevel > 0) {
            $normalized['headingLevel'] = $headingLevel;
        }
        if ($type === 'text' && trim((string) ($element['navLabel'] ?? '')) !== '') {
            $normalized['navLabel'] = trim((string) $element['navLabel']);
        }

        return $normalized;
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
