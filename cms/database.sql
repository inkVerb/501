CREATE DATABASE blog_db;
GRANT ALL PRIVILEGES ON blog_db.* TO blog_db_user@localhost IDENTIFIED BY 'blogdbpassword';
FLUSH PRIVILEGES;
USE blog_db;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fullname` VARCHAR(90) NOT NULL,
  `username` VARCHAR(90) NOT NULL,
  `email` VARCHAR(128) NOT NULL,
  `website` VARCHAR(128) DEFAULT NULL,
  `favnumber` TINYINT DEFAULT NULL,
  `pass` VARCHAR(255) DEFAULT NULL,
  `type` ENUM('member', 'contributor', 'writer', 'editor', 'admin') NOT NULL,
  `date_updated` TIMESTAMP NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `strings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` INT UNSIGNED NOT NULL,
  `random_string` VARCHAR(255) DEFAULT NULL,
  `usable` ENUM('live', 'dead') NOT NULL,
  `date_expires` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4

CREATE TABLE IF NOT EXISTS `pieces` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` ENUM('post', 'page', 'template') NOT NULL,
  `status` ENUM('live', 'dead') NOT NULL,
  `pub_yn` BOOLEAN NOT NULL DEFAULT false,
  `title` VARCHAR(90) NOT NULL,
  `subtitle` VARCHAR(90) DEFAULT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `content` LONGTEXT DEFAULT NULL,
  `after` TEXT DEFAULT NULL,
  `excerpt` TEXT DEFAULT NULL,
  `series` INT UNSIGNED DEFAULT 1,
  `tags` TEXT DEFAULT NULL,
  `links` TEXT DEFAULT NULL,
  `feat_img` INT UNSIGNED NOT NULL DEFAULT 0,
  `feat_aud` INT UNSIGNED NOT NULL DEFAULT 0,
  `feat_vid` INT UNSIGNED NOT NULL DEFAULT 0,
  `feat_doc` INT UNSIGNED NOT NULL DEFAULT 0,
  `date_live` TIMESTAMP NULL,
  `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `date_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `publications` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `piece_id` INT UNSIGNED NOT NULL,
  `type` ENUM('page', 'post') NOT NULL,
  `status` ENUM('live', 'dead') NOT NULL,
  `pubstatus` ENUM('published', 'redrafting') NOT NULL,
  `title` VARCHAR(90) NOT NULL,
  `subtitle` VARCHAR(90) DEFAULT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `content` LONGTEXT DEFAULT NULL,
  `after` TEXT DEFAULT NULL,
  `excerpt` TEXT DEFAULT NULL,
  `series` INT UNSIGNED DEFAULT 1,
  `aggregated` INT UNSIGNED DEFAULT 0,
  `duration` VARCHAR(12) DEFAULT 0,
  `guid` TEXT NOT NULL DEFAULT 0,
  `tags` TEXT DEFAULT NULL,
  `links` TEXT DEFAULT NULL,
  `feat_img` TEXT NOT NULL DEFAULT 0,
  `feat_aud` TEXT NOT NULL DEFAULT 0,
  `feat_vid` TEXT NOT NULL DEFAULT 0,
  `feat_doc` TEXT NOT NULL DEFAULT 0,
  `feat_img_mime` TEXT NOT NULL DEFAULT 0,
  `feat_aud_mime` TEXT NOT NULL DEFAULT 0,
  `feat_vid_mime` TEXT NOT NULL DEFAULT 0,
  `feat_doc_mime` TEXT NOT NULL DEFAULT 0,
  `feat_img_length` TEXT NOT NULL DEFAULT 0,
  `feat_aud_length` TEXT NOT NULL DEFAULT 0,
  `feat_vid_length` TEXT NOT NULL DEFAULT 0,
  `feat_doc_length` TEXT NOT NULL DEFAULT 0,
  `date_live` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `date_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `publication_history` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `piece_id` INT UNSIGNED NOT NULL,
  `type` ENUM('page', 'post') NOT NULL,
  `title` VARCHAR(90) NOT NULL,
  `subtitle` VARCHAR(90) DEFAULT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `content` LONGTEXT DEFAULT NULL,
  `after` TEXT DEFAULT NULL,
  `excerpt` TEXT DEFAULT NULL,
  `series` INT UNSIGNED DEFAULT 1,
  `tags` TEXT DEFAULT NULL,
  `links` TEXT DEFAULT NULL,
  `feat_img` TEXT NOT NULL DEFAULT 0,
  `feat_aud` TEXT NOT NULL DEFAULT 0,
  `feat_vid` TEXT NOT NULL DEFAULT 0,
  `feat_doc` TEXT NOT NULL DEFAULT 0,
  `date_live` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `date_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `series` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(90) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `template` INT UNSIGNED DEFAULT NULL,
  `series_lang` VARCHAR(8) NOT NULL DEFAULT 'en',
  `series_link` TEXT DEFAULT NULL,
  `series_author` VARCHAR(90) DEFAULT NULL,
  `series_descr` VARCHAR(255) DEFAULT NULL,
  `series_summary` VARCHAR(255) DEFAULT NULL,
  `series_owner` VARCHAR(255) DEFAULT NULL,
  `series_email` VARCHAR(255) DEFAULT NULL,
  `series_copy` VARCHAR(90) DEFAULT NULL,
  `series_keywords` TEXT DEFAULT NULL,
  `series_explicit` VARCHAR(5) NOT NULL DEFAULT 'false',
  `series_cat1` VARCHAR(255) DEFAULT NULL,
  `series_cat2` VARCHAR(255) DEFAULT NULL,
  `series_cat3` VARCHAR(255) DEFAULT NULL,
  `series_cat4` VARCHAR(255) DEFAULT NULL,
  `series_cat5` VARCHAR(255) DEFAULT NULL
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
INSERT INTO series (name, slug) VALUES ('Blog', 'blog');

CREATE TABLE IF NOT EXISTS `aggregation` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(90) NOT NULL,
  `source` TEXT DEFAULT NULL,
  `series` INT UNSIGNED DEFAULT 1,
  `description` TINYTEXT DEFAULT NULL,
  `update_interval` TINYTEXT DEFAULT '15',
  `status` ENUM('active', 'dormant', 'problematic', 'deleting') NOT NULL,
  `on_delete` ENUM('convert', 'erase') NOT NULL,
  `last_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `date_added` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `media_library` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `size` BIGINT UNSIGNED DEFAULT 1,
  `mime_type` VARCHAR(128) NOT NULL,
  `basic_type` VARCHAR(12) NOT NULL,
  `location` VARCHAR(255) NOT NULL,
  `file_base` VARCHAR(255) NOT NULL,
  `file_extension` VARCHAR(52) NOT NULL,
  `title_text` VARCHAR(255) DEFAULT NULL,
  `alt_text` VARCHAR(255) DEFAULT NULL,
  `duration` VARCHAR(12) DEFAULT NULL,
  `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `date_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `media_images` (
  `m_id` INT UNSIGNED NOT NULL,
  `orientation` VARCHAR(4) NOT NULL,
  `width` VARCHAR(4) NOT NULL,
  `height` VARCHAR(4) NOT NULL,
  `xs` VARCHAR(9) NOT NULL,
  `sm` VARCHAR(9) NOT NULL,
  `md` VARCHAR(9) NOT NULL,
  `lg` VARCHAR(9) NOT NULL,
  `xl` VARCHAR(9) NOT NULL,
  PRIMARY KEY (`m_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `blog_settings` (
  `web_base` VARCHAR(2048) NOT NULL, -- May be redundant from in.conf.php, but coult be useful for future development
  `public` BOOLEAN NOT NULL DEFAULT true,
  `title` VARCHAR(90) DEFAULT '501 Blog',
  `tagline` VARCHAR(120) DEFAULT 'Where code stacks',
  `description` LONGTEXT DEFAULT 'Long, poetic explanations of blog contents are useful in search engines, podcasts, and other places on the interwebs.',
  `keywords` LONGTEXT DEFAULT NULL,
  `summary_words` INT UNSIGNED DEFAULT 50,
  `piece_items` INT UNSIGNED DEFAULT 10,
  `feed_items` INT UNSIGNED DEFAULT 20,
  `default_series` INT UNSIGNED DEFAULT 1,
  `crawler_index` ENUM('index', 'noindex') DEFAULT 'index',
  `blog_lang` VARCHAR(8) NOT NULL DEFAULT 'en',
  `blog_link` TEXT DEFAULT NULL,
  `blog_author` VARCHAR(90) DEFAULT NULL,
  `blog_descr` VARCHAR(255) DEFAULT NULL,
  `blog_summary` VARCHAR(255) DEFAULT NULL,
  `blog_owner` VARCHAR(255) DEFAULT NULL,
  `blog_email` VARCHAR(255) DEFAULT NULL,
  `blog_copy` VARCHAR(90) DEFAULT NULL,
  `blog_keywords` TEXT DEFAULT NULL,
  `blog_explicit` VARCHAR(5) NOT NULL DEFAULT 'false',
  `blog_cat1` VARCHAR(255) DEFAULT NULL,
  `blog_cat2` VARCHAR(255) DEFAULT NULL,
  `blog_cat3` VARCHAR(255) DEFAULT NULL,
  `blog_cat4` VARCHAR(255) DEFAULT NULL,
  `blog_cat5` VARCHAR(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
