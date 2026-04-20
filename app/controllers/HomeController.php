<?php
namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller {
    private $pageModel;

    public function __construct() {
        $this->pageModel = $this->model('Page');
    }

    public function index() {
        $slug = 'home';
        $title = 'Trang Chủ';
        $defaultPage = $this->defaultHomePageContent();
        $pageRow = $this->pageModel->getOrCreateBySlug($slug, $title, $defaultPage);

        $page = json_decode($pageRow['content'], true);
        $shouldSave = false;

        if (!is_array($page)) {
            $page = $defaultPage;
            $shouldSave = true;
        } elseif ($this->isLegacyCanvasContent($page)) {
            $page = $this->convertLegacyCanvasContent($page, $defaultPage);
            $shouldSave = true;
        }

        $normalizedPage = $this->normalizePageContent($page);
        if ($normalizedPage !== $page) {
            $page = $normalizedPage;
            $shouldSave = true;
        }

        if ($shouldSave) {
            $pageRow = $this->pageModel->saveBySlug($slug, $page, $pageRow['title'] ?: $title);
        }

        $data = [
            'title' => $pageRow['title'] ?: $title,
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
                            'type' => 'heading',
                            'level' => 2,
                            'content' => 'Giải pháp làm sạch thân thiện môi trường'
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
                    'id' => 'care-solutions',
                    'heading' => [
                        'level' => 1,
                        'text' => 'Giải Pháp Theo Nhu Cầu',
                        'anchorId' => 'care-solutions'
                    ],
                    'blocks' => [
                        [
                            'type' => 'heading',
                            'level' => 3,
                            'content' => 'Chăm sóc nhà cửa theo từng nhu cầu'
                        ],
                        [
                            'type' => 'text',
                            'content' => 'Khám phá các nhóm sản phẩm xanh dành cho chăm sóc cơ thể, nhà bếp, phòng tắm, giặt giũ và chăm sóc nhà cửa.'
                        ]
                    ]
                ],
                [
                    'id' => 'heyp-highlights',
                    'heading' => [
                        'level' => 1,
                        'text' => 'Điểm Nổi Bật Của HEYP',
                        'anchorId' => 'heyp-highlights'
                    ],
                    'blocks' => [
                        [
                            'type' => 'text',
                            'content' => 'Những sản phẩm thân thiện môi trường được HEYP chọn lọc cho nhu cầu làm sạch hằng ngày.'
                        ],
                        [
                            'type' => 'image',
                            'src' => 'public/img/logoHEYP.png',
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
                            'type' => 'heading',
                            'level' => 2,
                            'content' => 'Thương hiệu địa phương tại Daklak'
                        ],
                        [
                            'type' => 'text',
                            'content' => 'HEYP hướng tới cung cấp các sản phẩm làm sạch, bảo vệ cho gia đình bạn, được làm từ nguyên liệu thiên nhiên, không phụ gia.'
                        ],
                        [
                            'type' => 'text',
                            'content' => 'HEYP tự hào đi theo con đường ủng hộ bảo vệ môi trường bằng cách hạn chế tối đa bao bì nhựa trong đóng gói và vận chuyển.'
                        ],
                        [
                            'type' => 'image',
                            'src' => 'public/img/logoHEYP.png',
                            'alt' => 'About Heyp'
                        ]
                    ]
                ]
            ]
        ];
    }

    private function isLegacyCanvasContent(array $page) {
        return ($page['layoutType'] ?? '') === 'canvas' || isset($page['elements']);
    }

    private function convertLegacyCanvasContent(array $page, array $fallbackPage) {
        $elements = isset($page['elements']) && is_array($page['elements']) ? $page['elements'] : [];

        usort($elements, function($a, $b) {
            $aY = is_array($a) && is_numeric($a['y'] ?? null) ? (int) $a['y'] : 0;
            $bY = is_array($b) && is_numeric($b['y'] ?? null) ? (int) $b['y'] : 0;
            if ($aY !== $bY) {
                return $aY <=> $bY;
            }

            $aX = is_array($a) && is_numeric($a['x'] ?? null) ? (int) $a['x'] : 0;
            $bX = is_array($b) && is_numeric($b['x'] ?? null) ? (int) $b['x'] : 0;
            if ($aX !== $bX) {
                return $aX <=> $bX;
            }

            $aZ = is_array($a) && is_numeric($a['zIndex'] ?? null) ? (int) $a['zIndex'] : 0;
            $bZ = is_array($b) && is_numeric($b['zIndex'] ?? null) ? (int) $b['zIndex'] : 0;
            return $aZ <=> $bZ;
        });

        $sections = [];
        $currentSectionIndex = null;

        foreach ($elements as $index => $element) {
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
            $navLabel = trim((string) ($element['navLabel'] ?? ''));

            if ($type !== 'image' && $content === '') {
                continue;
            }

            $isH1 = $type !== 'image' && (
                $headingLevel === 1 ||
                $navLabel !== '' ||
                ($fontSize >= 32 && strlen($content) <= 220)
            );

            if ($isH1) {
                $label = $navLabel !== '' ? $navLabel : $content;
                $sectionId = $this->normalizeAnchorId($element['id'] ?? $label, 'section-' . (count($sections) + 1));

                $sections[] = [
                    'id' => $sectionId,
                    'heading' => [
                        'level' => 1,
                        'text' => $label,
                        'anchorId' => $sectionId
                    ],
                    'blocks' => []
                ];
                $currentSectionIndex = count($sections) - 1;
                continue;
            }

            if ($currentSectionIndex === null) {
                $sectionId = count($sections) === 0 ? 'home' : 'section-' . (count($sections) + 1);
                $sections[] = [
                    'id' => $sectionId,
                    'heading' => [
                        'level' => 1,
                        'text' => $content !== '' ? $content : 'Trang Chủ',
                        'anchorId' => $sectionId
                    ],
                    'blocks' => []
                ];
                $currentSectionIndex = count($sections) - 1;

                if ($type !== 'image') {
                    continue;
                }
            }

            if ($type === 'image') {
                $src = (string) ($element['src'] ?? $element['image_url'] ?? $element['url'] ?? '');
                if ($src === '') {
                    continue;
                }

                $sections[$currentSectionIndex]['blocks'][] = [
                    'type' => 'image',
                    'src' => $src,
                    'alt' => $content
                ];
                continue;
            }

            $level = in_array($headingLevel, [2, 3], true) ? $headingLevel : ($fontSize >= 28 ? 2 : 0);
            $sections[$currentSectionIndex]['blocks'][] = $level > 0
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
            $sections = $fallbackPage['sections'];
        }

        return [
            'header' => isset($page['header']) && is_array($page['header']) ? $page['header'] : $fallbackPage['header'],
            'sections' => $sections
        ];
    }

    private function normalizePageContent(array $page) {
        if (!isset($page['header']) || !is_array($page['header'])) {
            $page['header'] = ['logo' => 'public/img/logoHEYP.png'];
        }

        $rawSections = isset($page['sections']) && is_array($page['sections']) ? $page['sections'] : [];
        $sections = [];
        $usedIds = [];

        foreach ($rawSections as $index => $section) {
            if (!is_array($section)) {
                continue;
            }

            $fallbackId = 'section-' . ($index + 1);
            $heading = isset($section['heading']) && is_array($section['heading']) ? $section['heading'] : [];
            $rawId = $section['id'] ?? $heading['anchorId'] ?? $heading['text'] ?? $fallbackId;
            $sectionId = $this->uniqueAnchorId($rawId, $fallbackId, $usedIds);

            $normalizedHeading = [];
            if (!empty($heading)) {
                $level = isset($heading['level']) ? (int) $heading['level'] : 1;
                $normalizedHeading = [
                    'level' => in_array($level, [1, 2, 3], true) ? $level : 1,
                    'text' => (string) ($heading['text'] ?? ''),
                    'anchorId' => $sectionId
                ];

                if (isset($heading['style']) && is_array($heading['style'])) {
                    $normalizedHeading['style'] = $heading['style'];
                }
            }

            $blocks = [];
            foreach (($section['blocks'] ?? []) as $block) {
                foreach ($this->normalizeBlocks($block) as $normalizedBlock) {
                    $blocks[] = $normalizedBlock;
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

    private function normalizeBlocks($block) {
        if (!is_array($block)) {
            return [];
        }

        $type = $block['type'] ?? '';

        if ($type === 'columns') {
            $blocks = [];
            foreach (($block['columns'] ?? []) as $columnBlock) {
                foreach ($this->normalizeBlocks($columnBlock) as $normalizedBlock) {
                    $blocks[] = $normalizedBlock;
                }
            }
            return $blocks;
        }

        if ($type === 'heading') {
            $level = isset($block['level']) ? (int) $block['level'] : 2;
            $normalized = [
                'type' => 'heading',
                'level' => in_array($level, [1, 2, 3], true) ? $level : 2,
                'content' => (string) ($block['content'] ?? $block['text'] ?? '')
            ];

            if (isset($block['style']) && is_array($block['style'])) {
                $normalized['style'] = $block['style'];
            }

            return trim($normalized['content']) === '' ? [] : [$normalized];
        }

        if ($type === 'text') {
            $normalized = [
                'type' => 'text',
                'content' => (string) ($block['content'] ?? '')
            ];

            if (isset($block['style']) && is_array($block['style'])) {
                $normalized['style'] = $block['style'];
            }

            return trim($normalized['content']) === '' ? [] : [$normalized];
        }

        if ($type === 'image') {
            $src = (string) ($block['src'] ?? $block['url'] ?? $block['image_url'] ?? '');
            if ($src === '') {
                return [];
            }

            $normalized = [
                'type' => 'image',
                'src' => $src,
                'alt' => (string) ($block['alt'] ?? $block['content'] ?? ''),
                'caption' => (string) ($block['caption'] ?? '')
            ];

            if (isset($block['style']) && is_array($block['style'])) {
                $normalized['style'] = $block['style'];
            }

            return [$normalized];
        }

        return [];
    }

    private function extractLandingNavItems(array $page) {
        $items = [];

        foreach ($page['sections'] ?? [] as $section) {
            if (!is_array($section)) {
                continue;
            }

            $sectionId = $section['id'] ?? '';
            if ($sectionId === '') {
                continue;
            }

            $heading = isset($section['heading']) && is_array($section['heading']) ? $section['heading'] : [];
            if ((int) ($heading['level'] ?? 0) === 1 && trim((string) ($heading['text'] ?? '')) !== '') {
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
                    trim((string) ($block['content'] ?? '')) !== ''
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

    private function uniqueAnchorId($value, $fallback, array &$usedIds) {
        $base = $this->normalizeAnchorId($value, $fallback);
        $id = $base;
        $counter = 2;

        while (isset($usedIds[$id])) {
            $id = $base . '-' . $counter;
            $counter++;
        }

        $usedIds[$id] = true;
        return $id;
    }

    private function normalizeAnchorId($value, $fallback) {
        $id = strtolower(trim((string) $value));
        $id = preg_replace('/[^a-z0-9_-]+/', '-', $id);
        $id = trim($id, '-');

        return $id !== '' ? $id : $fallback;
    }
}
