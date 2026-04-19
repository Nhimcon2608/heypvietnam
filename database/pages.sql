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
  '{
    "header": {
      "logo": "public/img/logoHEYP.png"
    },
    "sections": [
      {
        "id": "home",
        "heading": {
          "level": 1,
          "text": "Sản Phẩm Xanh Cho Lối Sống Bền Vững",
          "anchorId": "home"
        },
        "blocks": [
          {
            "type": "heading",
            "level": 2,
            "content": "Giải pháp làm sạch thân thiện môi trường"
          },
          {
            "type": "text",
            "content": "HEYP cung cấp các sản phẩm làm sạch trong gia đình từ xà phòng truyền thống, thân thiện môi trường, giúp bạn xây dựng lối sống bền vững, an lành và hạnh phúc."
          },
          {
            "type": "image",
            "src": "public/img/logoHEYP.png",
            "alt": "Heyp Logo",
            "caption": ""
          }
        ]
      },
      {
        "id": "product-categories",
        "heading": {
          "level": 1,
          "text": "Danh Mục Sản Phẩm",
          "anchorId": "product-categories"
        },
        "blocks": [
          {
            "type": "heading",
            "level": 3,
            "content": "Chăm sóc nhà cửa theo từng nhu cầu"
          },
          {
            "type": "text",
            "content": "Khám phá các nhóm sản phẩm xanh dành cho chăm sóc cơ thể, nhà bếp, phòng tắm, giặt giũ và chăm sóc nhà cửa."
          }
        ]
      },
      {
        "id": "featured-products",
        "heading": {
          "level": 1,
          "text": "Sản Phẩm Nổi Bật",
          "anchorId": "featured-products"
        },
        "blocks": [
          {
            "type": "text",
            "content": "Những sản phẩm thân thiện môi trường được HEYP chọn lọc cho nhu cầu làm sạch hằng ngày."
          },
          {
            "type": "image",
            "src": "public/img/products/vn-11134210-7r98o-lqarr1ni06vm7c.webp",
            "alt": "Sản phẩm HEYP",
            "caption": ""
          }
        ]
      },
      {
        "id": "about-heyp",
        "heading": {
          "level": 1,
          "text": "Về HEYP",
          "anchorId": "about-heyp"
        },
        "blocks": [
          {
            "type": "heading",
            "level": 2,
            "content": "Thương hiệu địa phương tại Daklak"
          },
          {
            "type": "text",
            "content": "HEYP hướng tới cung cấp các sản phẩm làm sạch, bảo vệ cho gia đình bạn, được làm từ nguyên liệu thiên nhiên, không phụ gia."
          },
          {
            "type": "text",
            "content": "HEYP tự hào đi theo con đường ủng hộ bảo vệ môi trường bằng cách hạn chế tối đa bao bì nhựa trong đóng gói và vận chuyển."
          },
          {
            "type": "image",
            "src": "public/img/products/vn-11134210-7r98o-lqarr1ni06vm7c.webp",
            "alt": "About Heyp",
            "caption": ""
          }
        ]
      }
    ]
  }'
)
ON DUPLICATE KEY UPDATE
  `title` = VALUES(`title`);
