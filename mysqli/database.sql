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
      `date_updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
      `type` ENUM('post', 'page') NOT NULL,
      `status` ENUM('live', 'dead') NOT NULL,
      `pub_yn` BOOLEAN NOT NULL DEFAULT false,
      `title` VARCHAR(90) NOT NULL,
      `slug` VARCHAR(90) NOT NULL,
      `content` LONGTEXT DEFAULT NULL,
      `after` TINYTEXT DEFAULT NULL,
      `series` INT UNSIGNED DEFAULT 1
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
      `slug` VARCHAR(90) NOT NULL,
      `content` LONGTEXT DEFAULT NULL,
      `after` TINYTEXT DEFAULT NULL,
      `series` INT UNSIGNED DEFAULT 1
      `date_live` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `date_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
    
CREATE TABLE IF NOT EXISTS `publication_history` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `piece_id` INT UNSIGNED NOT NULL,
      `type` ENUM('page', 'post') NOT NULL,
      `title` VARCHAR(90) NOT NULL,
      `slug` VARCHAR(90) NOT NULL,
      `content` LONGTEXT DEFAULT NULL,
      `after` TINYTEXT DEFAULT NULL,
      `series` INT UNSIGNED DEFAULT 1
      `date_live` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `date_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
    
CREATE TABLE IF NOT EXISTS `series` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `name` VARCHAR(90) NOT NULL,
      `slug` VARCHAR(90) NOT NULL,
      `template` INT UNSIGNED DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
    INSERT INTO series (name, slug) VALUES ('Blog', 'blog');
    
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
    
