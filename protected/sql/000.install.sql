-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               5.5.48 - MySQL Community Server (GPL)
-- ОС Сервера:                   Win32
-- HeidiSQL Версия:              9.3.0.4984
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Дамп структуры для таблица test.spi_hint
CREATE TABLE IF NOT EXISTS `spi_hint` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `position_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `spi_hint_position_unq` (`position_id`),
  KEY `spi_hint_page` (`page_id`),
  KEY `spi_hint_position` (`position_id`),
  CONSTRAINT `spi_hint_page` FOREIGN KEY (`page_id`) REFERENCES `spi_page` (`id`) ON DELETE CASCADE,
  CONSTRAINT `spi_hint_position` FOREIGN KEY (`position_id`) REFERENCES `spi_page_position` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы test.spi_hint: ~5 rows (приблизительно)
/*!40000 ALTER TABLE `spi_hint` DISABLE KEYS */;
INSERT INTO `spi_hint` (`id`, `page_id`, `position_id`, `title`, `description`) VALUES
	(1, 3, 8, NULL, 'Choose page here'),
	(5, 3, 9, NULL, 'sdfsdf'),
	(6, 3, 13, 'test', 'test'),
	(7, 3, 11, NULL, 'desc'),
	(8, 3, 10, NULL, 'www');
/*!40000 ALTER TABLE `spi_hint` ENABLE KEYS */;


-- Дамп структуры для таблица test.spi_page
CREATE TABLE IF NOT EXISTS `spi_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_view` varchar(45) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_without_login` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы test.spi_page: ~3 rows (приблизительно)
/*!40000 ALTER TABLE `spi_page` DISABLE KEYS */;
INSERT INTO `spi_page` (`page_view`, `name`, `is_without_login`) VALUES
	('user', 'Users', 1),
	('user_type', 'User Roles', 1),
  ('resttest', 'Rest Test', 0),
  ('error404', 'Error 404', 1),
	('dashboard', 'dashboard', 1),
  ('login', 'Login', 1),
  ('contact', 'Contact', 1),
  ('about', 'About us', 1),
  ('admmin_dashboard', 'Admin Dashboard', 0);

/*!40000 ALTER TABLE `spi_page` ENABLE KEYS */;


-- Дамп структуры для таблица test.spi_page_position
CREATE TABLE IF NOT EXISTS `spi_page_position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `spi_page_position_page` (`page_id`),
  CONSTRAINT `spi_page_position_page` FOREIGN KEY (`page_id`) REFERENCES `spi_page` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы test.spi_page_position: ~14 rows (приблизительно)
/*!40000 ALTER TABLE `spi_page_position` DISABLE KEYS */;
INSERT INTO `spi_page_position` (`id`, `page_id`, `code`, `name`) VALUES
	(1, 1, 'is_active', 'Status'),
	(2, 1, 'type_id', 'Benutzer-Typ'),
	(3, 1, 'relation_id', 'Organisation'),
	(4, 1, 'is_finansist', 'Finanzielle Rechte'),
	(5, 1, 'login', 'Benutzername'),
	(6, 1, 'email', 'Email'),
	(7, 1, 'phone', 'Telefon'),
	(8, 3, 'page_id', 'Seite'),
	(9, 3, 'position_id', 'Position'),
	(10, 3, 'title', 'Hilfetext'),
	(11, 3, 'description', 'Description'),
	(12, 1, 'header', 'Header'),
	(13, 3, 'header', 'Header'),
	(14, 2, 'header', 'Header');
/*!40000 ALTER TABLE `spi_page_position` ENABLE KEYS */;


-- Дамп структуры для таблица test.spi_user
CREATE TABLE IF NOT EXISTS `spi_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(1) NOT NULL DEFAULT '',
  `type_id` int(11) NOT NULL,
  `relation_id` int(11) DEFAULT NULL,
  `login` varchar(45) NOT NULL,
  `password` varchar(45) NOT NULL,
  `gender` tinyint(1) NOT NULL,
  `title` varchar(45) DEFAULT NULL,
  `function` varchar(45) DEFAULT NULL,
  `first_name` varchar(45) NOT NULL,
  `last_name` varchar(45) NOT NULL,
  `email` varchar(45) NOT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `auth_token` varchar(32) DEFAULT NULL,
  `auth_token_created_at` datetime DEFAULT NULL,
  `recovery_token` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `spi_login_unq` (`login`),
  KEY `spi_email_unq` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы test.spi_user: ~1 rows (приблизительно)
/*!40000 ALTER TABLE `spi_user` DISABLE KEYS */;
INSERT INTO `spi_user` (`id`, `type`, `type_id`, `relation_id`, `login`, `password`, `gender`, `title`, `function`, `first_name`, `last_name`, `email`, `phone`, `is_active`, `auth_token`, `auth_token_created_at`, `recovery_token`) VALUES
	(1, 'a', 1, NULL, 'test', '098f6bcd4621d373cade4e832627b4f6', 1, 'Nice titile', NULL, 'Mister', 'Test', 'test@ma.rt', '1234567890', 1, 'bae4d11fce0e71521dcc74f802f11a2c', '2016-02-17 07:51:51', '427e6bd7ac6b908a27ca18f5a0400344');
/*!40000 ALTER TABLE `spi_user` ENABLE KEYS */;


-- Дамп структуры для таблица test.spi_user_type
CREATE TABLE IF NOT EXISTS `spi_user_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `default` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы test.spi_user_type: ~6 rows (приблизительно)
/*!40000 ALTER TABLE `spi_user_type` DISABLE KEYS */;
INSERT INTO `spi_user_type` (`id`, `type`, `name`, `default`) VALUES
	(1, 'a', 'Admin', 1),
	(2, 'g', 'Guest', 1),
	(3, 't', 'TA', 1),
	(4, 's', 'School', 1),
	(5, 'd', 'District', 1),
	(6, 'a', 'Senat', 1);
/*!40000 ALTER TABLE `spi_user_type` ENABLE KEYS */;


-- Дамп структуры для таблица test.spi_user_type_right
CREATE TABLE IF NOT EXISTS `spi_user_type_right` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `can_show` tinyint(1) NOT NULL DEFAULT '0',
  `can_view` tinyint(1) NOT NULL DEFAULT '0',
  `can_edit` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `spi_user_type_right_page` (`page_id`),
  KEY `spi_user_type_right_type` (`type_id`),
  CONSTRAINT `spi_user_type_right_page` FOREIGN KEY (`page_id`) REFERENCES `spi_page` (`id`) ON DELETE CASCADE,
  CONSTRAINT `spi_user_type_right_type` FOREIGN KEY (`type_id`) REFERENCES `spi_user_type` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы test.spi_user_type_right: ~10 rows (приблизительно)
/*!40000 ALTER TABLE `spi_user_type_right` DISABLE KEYS */;
SELECT @user_page_id:=id FROM `spi_page` WHERE `page_view`= 'user';
SELECT @user_type_page_id:=id FROM `spi_page` WHERE `page_view`= 'user_type';
SELECT @dashboard_page_id:=id FROM `spi_page` WHERE `page_view`= 'dashboard';
SELECT @login_page_id:=id FROM `spi_page` WHERE `page_view`= 'login';

SELECT @resttest_page_id:=id FROM `spi_page` WHERE `page_view`= 'resttest';
SELECT @error_page_id:=id FROM `spi_page` WHERE `page_view`= 'error404';
SELECT @admin_dashboard_page_id:=id FROM `spi_page` WHERE `page_view`= 'admin_dashboard';

INSERT INTO `spi_user_type_right` ( `type_id`, `page_id`, `can_show`, `can_view`, `can_edit`)
VALUES
(1, @resttest_page_id, 1, 1, 1),
(2, @resttest_page_id, 1, 1, 0),
(1, @error_page_id, 1, 1, 1),
(2, @error_page_id, 1, 1, 0),
(1, @admin_dashboard_page_id, 1, 1, 1),
(2, @admin_dashboard_page_id, 1, 1, 0),
(1, @user_page_id, 1, 1, 1),
(2, @user_page_id, 1, 1, 0),
(1, @user_type_page_id, 1, 1, 1),
(2, @user_type_page_id, 1, 1, 0),
(1, @dashboard_page_id, 1, 1, 1),
(2, @dashboard_page_id, 1, 1, 0),
(1, @login_page_id, 1, 1, 1),
(2, @login_page_id, 1, 1, 0);
/*!40000 ALTER TABLE `spi_user_type_right` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
