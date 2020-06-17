CREATE DATABASE blog501 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON blog501.* TO blog501@localhost IDENTIFIED BY 'blog501pass';
FLUSH PRIVILEGES;

USE blog501;

CREATE TABLE IF NOT EXISTS `settings` (
  `live` BOOLEAN NOT NULL DEFAULT false,
  `allow_crawler_indexing` BOOLEAN NOT NULL DEFAULT true,
  `title` VARCHAR(90) NOT NULL,
  `subheading` VARCHAR(90) NOT NULL,
  `description` TINYTEXT DEFAULT NULL,
  `color_primary` INT DEFAULT CONV('444444', 16, 10),
  `color_secondary` INT DEFAULT CONV('5F5F5F', 16, 10),
  `admin_email` VARCHAR(90) DEFAULT NULL,
  `time_date` TIMESTAMP NOT NULL,
  `time_epoch` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

INSERT INTO settings (title, subheading, admin_email) VALUES ('Another Blog', 'Poetry is code.', 'blog501@verb.ink');

-- SELECT HEX(color_primary) from settings;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fullname` VARCHAR(90) NOT NULL,
  `username` VARCHAR(90) NOT NULL,
  `email` VARCHAR(128) NOT NULL,
  `website` VARCHAR(128) DEFAULT NULL,
  `favnumber` TINYINT DEFAULT NULL,
  `pass` VARCHAR(255) DEFAULT NULL,
  `type` ENUM('contributor', 'writer', 'editor', 'admin') NOT NULL,
  `date_updated` TIMESTAMP NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

INSERT INTO users (name, username, email, type) VALUES ('Inky', 'inkyuser123', 'inkyuser@verb.ink', 'admin');

CREATE TABLE IF NOT EXISTS `strings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` INT UNSIGNED NOT NULL,
  `random_string` VARCHAR(255) DEFAULT NULL,
  `usable` ENUM('live', 'cookie_login', 'dead') NOT NULL,
  `date_expires` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
GRANT ALL PRIVILEGES ON webapp_db.* TO webapp_db_user@localhost IDENTIFIED BY 'webappdbpassword';
FLUSH PRIVILEGES;

CREATE TABLE IF NOT EXISTS `pieces` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` ENUM('post', 'page') NOT NULL,
  `status` ENUM('live', 'dead') NOT NULL,
  `pub_yn` BOOLEAN NOT NULL DEFAULT false,
  `title` VARCHAR(90) NOT NULL,
  `slug` VARCHAR(90) NOT NULL,
  `content` LONGTEXT DEFAULT NULL,
  `after` TINYTEXT DEFAULT NULL,
  `date_live` TIMESTAMP NULL,
  `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `date_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

INSERT INTO pieces (type, status, title, slug, content, after, date_live) VALUES ('post', 'live', 'Hello World!', 'hello-world', 'I am the first post! Ink is a verb. So, get inking!', '', NOW());
INSERT INTO pieces (type, status, title, slug, content, after, date_live) VALUES ('page', 'live', 'About', 'about', 'This is all about Blog 501. I am a demo to be updated.', '', NOW());

CREATE TABLE IF NOT EXISTS `publications` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `piece_id` INT UNSIGNED NOT NULL,
  `type` ENUM('page', 'post') NOT NULL,
  `pubstatus` ENUM('published', 'drafting') NOT NULL,
  `title` VARCHAR(90) NOT NULL,
  `slug` VARCHAR(90) NOT NULL,
  `content` LONGTEXT DEFAULT NULL,
  `after` TINYTEXT DEFAULT NULL,
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
  `date_live` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `date_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `social` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `url` VARCHAR(255) NOT NULL,
  `type` ENUM('gab', 'bitchute', 'instagram', 'youtube', 'minds', 'facebook', 'twitter', 'spotify', 'github', 'tumblr', 'homepage', 'blog', 'rss') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `links` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `url` VARCHAR(255) NOT NULL,
  `text` VARCHAR(90) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
