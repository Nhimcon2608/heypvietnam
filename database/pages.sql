SET NAMES utf8mb4;
SET time_zone = '+07:00';

CREATE DATABASE IF NOT EXISTS `heypvietnam`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `heypvietnam`;

CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `username` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admins_username_unique` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(255) NOT NULL,
  `value` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `pages` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `content` JSON NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pages_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `subscribers` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscribers_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `admins` (`name`, `email`, `username`, `password`)
VALUES (
  'HeypVietNam Admin',
  'admin@heypvietnam.com',
  'heypvietnam',
  '$2y$10$z/.XA75u7p.iCO1iGzpCi.hpabqqLuCsjc8GqEDjv23EDk43UpD2S'
)
ON DUPLICATE KEY UPDATE
  `name` = VALUES(`name`),
  `email` = VALUES(`email`),
  `password` = VALUES(`password`);

INSERT INTO `settings` (`key`, `value`)
VALUES
  ('site_name', 'HeypVietNam'),
  ('site_description', 'Sản phẩm xanh cho lối sống bền vững.'),
  ('contact_phone', '098 504 0609'),
  ('contact_email', 'Hienphuongcna@gmail.com'),
  ('social_shopee', 'https://shopee.vn/heypvietnam'),
  ('social_facebook', 'https://www.facebook.com/share/19kF6DwjHV/?mibextid=wwXIfr'),
  ('social_youtube', 'https://www.youtube.com/@hienphuongcna')
ON DUPLICATE KEY UPDATE
  `value` = VALUES(`value`);

INSERT INTO `pages` (`title`, `slug`, `content`)
VALUES (
  'Trang Chủ',
  'home',
  '{"header":{"logo":"public/img/logoHEYP.png"},"layoutType":"canvas","canvas":{"width":1200,"height":4250,"backgroundColor":"#ffffff"},"elements":[{"id":"hero-bg","type":"shape","x":0,"y":0,"width":1200,"height":640,"zIndex":1,"content":"","src":"","href":"","style":{"fontFamily":"Open Sans","fontSize":18,"fontWeight":"400","fontStyle":"normal","textAlign":"left","color":"#1f2937","backgroundColor":"#ffffff","borderRadius":0}},{"id":"quote-bg","type":"shape","x":0,"y":640,"width":1200,"height":250,"zIndex":2,"content":"","src":"","href":"","style":{"fontFamily":"Open Sans","fontSize":18,"fontWeight":"400","fontStyle":"normal","textAlign":"left","color":"#1f2937","backgroundColor":"#f8f9fa","borderRadius":0}},{"id":"image-intro-bg","type":"shape","x":0,"y":890,"width":1200,"height":620,"zIndex":3,"content":"","src":"","href":"","style":{"fontFamily":"Open Sans","fontSize":18,"fontWeight":"400","fontStyle":"normal","textAlign":"left","color":"#1f2937","backgroundColor":"#f8f9fa","borderRadius":0}},{"id":"story-bg","type":"shape","x":0,"y":1510,"width":1200,"height":760,"zIndex":4,"content":"","src":"","href":"","style":{"fontFamily":"Open Sans","fontSize":18,"fontWeight":"400","fontStyle":"normal","textAlign":"left","color":"#1f2937","backgroundColor":"#ffffff","borderRadius":0}},{"id":"gallery-bg","type":"shape","x":0,"y":2270,"width":1200,"height":1880,"zIndex":5,"content":"","src":"","href":"","style":{"fontFamily":"Open Sans","fontSize":18,"fontWeight":"400","fontStyle":"normal","textAlign":"left","color":"#1f2937","backgroundColor":"#f8f9fa","borderRadius":0}},{"id":"ve-heyp-tagline","type":"text","x":70,"y":88,"width":520,"height":34,"zIndex":6,"content":"VỀ HEYP - SẢN PHẨM XANH VIỆT NAM","src":"","href":"","style":{"fontFamily":"Open Sans","fontSize":14,"fontWeight":"500","fontStyle":"normal","textAlign":"left","color":"#6c757d","backgroundColor":"transparent","borderRadius":0}},{"id":"ve-heyp","type":"text","x":70,"y":132,"width":570,"height":190,"zIndex":7,"content":"Khởi Nguồn Từ Tình Yêu\\nDành Cho Thiên Nhiên\\nVà Cuộc Sống Bền Vững","src":"","href":"","style":{"fontFamily":"Georgia","fontSize":43,"fontWeight":"400","fontStyle":"normal","textAlign":"left","color":"#2c3e50","backgroundColor":"transparent","borderRadius":0},"headingLevel":1,"navLabel":"Về HEYP"},{"id":"ve-heyp-subtitle","type":"text","x":70,"y":328,"width":570,"height":70,"zIndex":8,"content":"Khởi Nguồn Từ Tình Yêu Dành Cho Thiên Nhiên Và Cuộc Sống Bền Vững","src":"","href":"","style":{"fontFamily":"Open Sans","fontSize":22,"fontWeight":"400","fontStyle":"normal","textAlign":"left","color":"#6c757d","backgroundColor":"transparent","borderRadius":0}},{"id":"ve-heyp-intro","type":"text","x":70,"y":405,"width":570,"height":150,"zIndex":9,"content":"HEYP ra đời từ niềm tin rằng con người cần sự kết nối với tự nhiên. Các sản phẩm thủ công thân thiện môi trường không những đủ khả năng đáp ứng nhu cầu của con người, mang lại cho chúng ta cuộc sống mạnh khỏe, hạnh phúc mà còn góp phần bảo vệ hành tinh.","src":"","href":"","style":{"fontFamily":"Open Sans","fontSize":20,"fontWeight":"400","fontStyle":"normal","textAlign":"left","color":"#6c757d","backgroundColor":"transparent","borderRadius":0}},{"id":"hero-logo-circle-bg","type":"shape","x":725,"y":80,"width":430,"height":430,"zIndex":10,"content":"","src":"","href":"","style":{"fontFamily":"Open Sans","fontSize":18,"fontWeight":"400","fontStyle":"normal","textAlign":"left","color":"#1f2937","backgroundColor":"#e9ecef","borderRadius":215}},{"id":"hero-logo","type":"image","x":725,"y":80,"width":430,"height":430,"zIndex":11,"content":"HEYP Logo","src":"public/img/logoHEYP.png","href":"","style":{"fontFamily":"Open Sans","fontSize":18,"fontWeight":"400","fontStyle":"normal","textAlign":"left","color":"#1f2937","backgroundColor":"transparent","borderRadius":215}},{"id":"lua-chon-xanh","type":"text","x":200,"y":718,"width":800,"height":96,"zIndex":12,"content":"\\"Mỗi sản phẩm xanh bạn chọn là một bước nhỏ hướng tới tương lai bền vững cho thế hệ mai sau.\\"","src":"","href":"","style":{"fontFamily":"Georgia","fontSize":30,"fontWeight":"400","fontStyle":"italic","textAlign":"center","color":"#495057","backgroundColor":"transparent","borderRadius":0}},{"id":"intro-image","type":"image","x":70,"y":965,"width":1060,"height":460,"zIndex":13,"content":"Sản phẩm xanh Heyp","src":"public/img/about/heyp-green-living.webp","href":"","style":{"fontFamily":"Open Sans","fontSize":18,"fontWeight":"400","fontStyle":"normal","textAlign":"left","color":"#1f2937","backgroundColor":"transparent","borderRadius":15}},{"id":"cau-chuyen-cua-heyp","type":"text","x":70,"y":1590,"width":500,"height":72,"zIndex":14,"content":"Câu Chuyện Của Heyp","src":"","href":"","style":{"fontFamily":"Georgia","fontSize":40,"fontWeight":"400","fontStyle":"normal","textAlign":"left","color":"#2c3e50","backgroundColor":"transparent","borderRadius":0},"headingLevel":1,"navLabel":"Câu Chuyện"},{"id":"story-divider","type":"shape","x":595,"y":1638,"width":1,"height":430,"zIndex":15,"content":"","src":"","href":"","style":{"fontFamily":"Open Sans","fontSize":18,"fontWeight":"400","fontStyle":"normal","textAlign":"left","color":"#1f2937","backgroundColor":"#e9ecef","borderRadius":0}},{"id":"story-p1","type":"text","x":70,"y":1688,"width":500,"height":112,"zIndex":16,"content":"Tất cả bắt đầu từ một câu hỏi đơn giản: \\"Làm thế nào để chúng ta có thể sống khỏe mạnh hơn mà vẫn bảo vệ được môi trường?\\"","src":"","href":"","style":{"fontFamily":"Open Sans","fontSize":19,"fontWeight":"400","fontStyle":"normal","textAlign":"left","color":"#6c757d","backgroundColor":"transparent","borderRadius":0}},{"id":"story-p2","type":"text","x":70,"y":1818,"width":500,"height":132,"zIndex":17,"content":"Heyp nhận ra rằng nhiều gia đình Việt Nam đang tìm kiếm những sản phẩm an toàn, thân thiện với môi trường nhưng lại gặp khó khăn trong việc tìm được những sản phẩm chất lượng với giá cả hợp lý.","src":"","href":"","style":{"fontFamily":"Open Sans","fontSize":19,"fontWeight":"400","fontStyle":"normal","textAlign":"left","color":"#6c757d","backgroundColor":"transparent","borderRadius":0}},{"id":"story-p3","type":"text","x":70,"y":1970,"width":500,"height":154,"zIndex":18,"content":"Heyp ra đời với sứ mệnh mang đến những sản phẩm xanh, sạch, an toàn cho sức khỏe và thân thiện với môi trường. Heyp tin rằng mỗi lựa chọn nhỏ của bạn đều góp phần tạo nên một tương lai bền vững cho thế hệ mai sau.","src":"","href":"","style":{"fontFamily":"Open Sans","fontSize":19,"fontWeight":"400","fontStyle":"normal","textAlign":"left","color":"#6c757d","backgroundColor":"transparent","borderRadius":0}},{"id":"story-button","type":"text","x":70,"y":2145,"width":220,"height":54,"zIndex":19,"content":"Tìm Hiểu Thêm","src":"","href":"#cuoc-song-xanh","style":{"fontFamily":"Open Sans","fontSize":16,"fontWeight":"600","fontStyle":"normal","textAlign":"center","color":"#ffffff","backgroundColor":"#7d8471","borderRadius":27}},{"id":"story-image","type":"image","x":665,"y":1580,"width":455,"height":560,"zIndex":20,"content":"Câu chuyện của chúng tôi","src":"public/img/about/heyp-green-story.webp","href":"","style":{"fontFamily":"Open Sans","fontSize":18,"fontWeight":"400","fontStyle":"normal","textAlign":"left","color":"#1f2937","backgroundColor":"transparent","borderRadius":20}},{"id":"hinh-anh-heyp","type":"text","x":70,"y":2350,"width":520,"height":70,"zIndex":21,"content":"Hình Ảnh HEYP","src":"","href":"","style":{"fontFamily":"Georgia","fontSize":40,"fontWeight":"400","fontStyle":"normal","textAlign":"left","color":"#2c3e50","backgroundColor":"transparent","borderRadius":0},"headingLevel":1,"navLabel":"Hình Ảnh"},{"id":"gallery-image-one","type":"image","x":70,"y":2440,"width":1060,"height":460,"zIndex":22,"content":"Sản phẩm xanh Heyp","src":"public/img/about/heyp-green-story.webp","href":"","style":{"fontFamily":"Open Sans","fontSize":18,"fontWeight":"400","fontStyle":"normal","textAlign":"left","color":"#1f2937","backgroundColor":"transparent","borderRadius":15}},{"id":"gallery-image-two","type":"image","x":70,"y":2940,"width":1060,"height":460,"zIndex":23,"content":"Cuộc sống xanh","src":"public/img/about/heyp-green-living.webp","href":"","style":{"fontFamily":"Open Sans","fontSize":18,"fontWeight":"400","fontStyle":"normal","textAlign":"left","color":"#1f2937","backgroundColor":"transparent","borderRadius":15}},{"id":"cuoc-song-xanh","type":"text","x":70,"y":3485,"width":520,"height":70,"zIndex":24,"content":"Cuộc Sống Xanh","src":"","href":"","style":{"fontFamily":"Georgia","fontSize":40,"fontWeight":"400","fontStyle":"normal","textAlign":"left","color":"#2c3e50","backgroundColor":"transparent","borderRadius":0},"headingLevel":1,"navLabel":"Cuộc Sống Xanh"},{"id":"green-life-video","type":"video","x":70,"y":3575,"width":1060,"height":596,"zIndex":25,"content":"Cuộc sống xanh - Heyp","src":"https://www.youtube.com/embed/1lKyby6WH-E?start=55","href":"","style":{"fontFamily":"Open Sans","fontSize":18,"fontWeight":"400","fontStyle":"normal","textAlign":"left","color":"#ffffff","backgroundColor":"#111827","borderRadius":15}}]}'
)
ON DUPLICATE KEY UPDATE
  `title` = VALUES(`title`),
  `content` = VALUES(`content`);
