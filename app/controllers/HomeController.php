<?php
namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller {
    private $pageModel;

    public function __construct() {
        $this->pageModel = $this->model('Page');
    }

    public function index() {
        $defaultPage = $this->defaultHomePageContent();
        $pageRow = $this->pageModel->getOrCreateBySlug('home', 'Trang Chủ', $defaultPage);

        $page = json_decode($pageRow['content'], true);
        if (!is_array($page)) {
            $page = $defaultPage;
            $pageRow = $this->pageModel->saveBySlug('home', $page, 'Trang Chủ');
        }

        $page = $this->normalizePageContent($page);

        $data = [
            'title' => $pageRow['title'] ?: 'Trang Chủ',
            'page' => $page,
            'page_json' => json_encode($page, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT),
            'example_json' => json_encode($defaultPage, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT),
            'landingNavItems' => $this->extractLandingNavItems($page)
        ];

        $this->viewWithLayout('pages/home', $data);
    }

    private function defaultHomePageContent() {
        return [
            'header' => [
                'logo' => 'public/img/logoHEYP.png'
            ],
            'sections' => [
                [
                    'id' => 'home',
                    'heading' => [
                        'level' => 1,
                        'text' => 'Sản Phẩm Xanh Cho Lối Sống Bền Vững',
                        'anchorId' => 'home'
                    ],
                    'blocks' => [
                        [
                            'type' => 'text',
                            'content' => 'SẢN PHẨM XANH CHO LỐI SỐNG BỀN VỮNG'
                        ],
                        [
                            'type' => 'text',
                            'content' => 'HEYP cung cấp các sản phẩm làm sạch trong gia đình từ xà phòng truyền thống, thân thiện môi trường, giúp bạn xây dựng lối sống bền vững, an lành và hạnh phúc.'
                        ],
                        [
                            'type' => 'image',
                            'src' => 'public/img/logoHEYP.png',
                            'alt' => 'Heyp Logo'
                        ]
                    ]
                ],
                [
                    'id' => 'product-categories',
                    'heading' => [
                        'level' => 1,
                        'text' => 'Danh Mục Sản Phẩm',
                        'anchorId' => 'product-categories'
                    ],
                    'blocks' => [
                        [
                            'type' => 'text',
                            'content' => 'Khám phá các nhóm sản phẩm xanh dành cho chăm sóc cơ thể, nhà bếp, phòng tắm, giặt giũ và chăm sóc nhà cửa.'
                        ]
                    ]
                ],
                [
                    'id' => 'featured-products',
                    'heading' => [
                        'level' => 1,
                        'text' => 'Sản Phẩm Nổi Bật',
                        'anchorId' => 'featured-products'
                    ],
                    'blocks' => [
                        [
                            'type' => 'text',
                            'content' => 'Những sản phẩm thân thiện môi trường được HEYP chọn lọc cho nhu cầu làm sạch hằng ngày.'
                        ],
                        [
                            'type' => 'image',
                            'src' => 'public/img/products/vn-11134210-7r98o-lqarr1ni06vm7c.webp',
                            'alt' => 'Sản phẩm HEYP'
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
                            'type' => 'text',
                            'content' => 'HEYP là thương hiệu địa phương tại Daklak, hướng tới cung cấp các sản phẩm làm sạch, bảo vệ cho gia đình bạn, được làm từ nguyên liệu thiên nhiên, không phụ gia.'
                        ],
                        [
                            'type' => 'text',
                            'content' => 'HEYP tự hào đi theo con đường ủng hộ bảo vệ môi trường bằng cách hạn chế tối đa bao bì nhựa trong đóng gói và vận chuyển.'
                        ],
                        [
                            'type' => 'image',
                            'src' => 'public/img/products/vn-11134210-7r98o-lqarr1ni06vm7c.webp',
                            'alt' => 'About Heyp'
                        ]
                    ]
                ]
            ]
        ];
    }

    private function normalizePageContent(array $page) {
        if (!isset($page['sections']) || !is_array($page['sections'])) {
            $page['sections'] = [];
        }

        $sections = [];
        foreach ($page['sections'] as $index => $section) {
            if (!is_array($section)) {
                continue;
            }

            $fallbackId = 'section-' . ($index + 1);
            $heading = isset($section['heading']) && is_array($section['heading']) ? $section['heading'] : [];
            $rawId = $section['id'] ?? $heading['anchorId'] ?? $heading['text'] ?? $fallbackId;
            $sectionId = $this->normalizeAnchorId($rawId, $fallbackId);

            $section['id'] = $sectionId;
            $section['heading'] = $heading;
            if (!empty($section['heading'])) {
                $level = isset($section['heading']['level']) ? (int) $section['heading']['level'] : 1;
                $section['heading']['level'] = in_array($level, [1, 2, 3], true) ? $level : 1;
                $section['heading']['anchorId'] = $sectionId;
                $section['heading']['text'] = $section['heading']['text'] ?? '';
            }

            $section['blocks'] = isset($section['blocks']) && is_array($section['blocks']) ? $section['blocks'] : [];
            $sections[] = $section;
        }

        $page['sections'] = $sections;
        return $page;
    }

    private function extractLandingNavItems(array $page) {
        $items = [];

        foreach ($page['sections'] ?? [] as $section) {
            if (!is_array($section)) {
                continue;
            }

            $sectionId = $section['id'] ?? '';
            $heading = isset($section['heading']) && is_array($section['heading']) ? $section['heading'] : [];
            if ((int) ($heading['level'] ?? 0) === 1 && !empty($heading['text']) && $sectionId !== '') {
                $items[] = [
                    'label' => $heading['text'],
                    'anchorId' => $sectionId
                ];
                continue;
            }

            foreach ($section['blocks'] ?? [] as $block) {
                if (
                    is_array($block) &&
                    ($block['type'] ?? '') === 'heading' &&
                    (int) ($block['level'] ?? 0) === 1 &&
                    !empty($block['content']) &&
                    $sectionId !== ''
                ) {
                    $items[] = [
                        'label' => $block['content'],
                        'anchorId' => $sectionId
                    ];
                    break;
                }
            }
        }

        return $items;
    }

    private function normalizeAnchorId($value, $fallback) {
        $id = strtolower(trim((string) $value));
        $id = preg_replace('/[^a-z0-9_-]+/', '-', $id);
        $id = trim($id, '-');

        return $id !== '' ? $id : $fallback;
    }
}
