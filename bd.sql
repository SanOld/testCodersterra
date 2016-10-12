-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               5.5.41-log - MySQL Community Server (GPL)
-- ОС Сервера:                   Win32
-- HeidiSQL Версия:              9.1.0.4867
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Дамп структуры для таблица test_traff.browser
CREATE TABLE IF NOT EXISTS `browser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы test_traff.browser: ~1 rows (приблизительно)
/*!40000 ALTER TABLE `browser` DISABLE KEYS */;
INSERT INTO `browser` (`id`, `name`) VALUES
	(1, 'Chrome');
/*!40000 ALTER TABLE `browser` ENABLE KEYS */;


-- Дамп структуры для таблица test_traff.city
CREATE TABLE IF NOT EXISTS `city` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы test_traff.city: ~1 rows (приблизительно)
/*!40000 ALTER TABLE `city` DISABLE KEYS */;
INSERT INTO `city` (`id`, `name`) VALUES
	(1, 'Харьков');
/*!40000 ALTER TABLE `city` ENABLE KEYS */;


-- Дамп структуры для таблица test_traff.cookie
CREATE TABLE IF NOT EXISTS `cookie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы test_traff.cookie: ~1 rows (приблизительно)
/*!40000 ALTER TABLE `cookie` DISABLE KEYS */;
INSERT INTO `cookie` (`id`, `name`) VALUES
	(1, '1467320031');
/*!40000 ALTER TABLE `cookie` ENABLE KEYS */;


-- Дамп структуры для таблица test_traff.hint
CREATE TABLE IF NOT EXISTS `hint` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы test_traff.hint: ~2 rows (приблизительно)
/*!40000 ALTER TABLE `hint` DISABLE KEYS */;
INSERT INTO `hint` (`id`, `name`) VALUES
	(1, '/contact'),
	(2, '/statistics'),
	(3, '/about'),
	(4, '/dashboard'),
	(5, '/login'),
	(6, '/visit');
/*!40000 ALTER TABLE `hint` ENABLE KEYS */;


-- Дамп структуры для таблица test_traff.ip
CREATE TABLE IF NOT EXISTS `ip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы test_traff.ip: ~1 rows (приблизительно)
/*!40000 ALTER TABLE `ip` DISABLE KEYS */;
INSERT INTO `ip` (`id`, `name`) VALUES
	(1, '78.111.16.86');
/*!40000 ALTER TABLE `ip` ENABLE KEYS */;


-- Дамп структуры для таблица test_traff.os
CREATE TABLE IF NOT EXISTS `os` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы test_traff.os: ~1 rows (приблизительно)
/*!40000 ALTER TABLE `os` DISABLE KEYS */;
INSERT INTO `os` (`id`, `name`) VALUES
	(1, 'Windows');
/*!40000 ALTER TABLE `os` ENABLE KEYS */;


-- Дамп структуры для таблица test_traff.ref
CREATE TABLE IF NOT EXISTS `ref` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы test_traff.ref: ~1 rows (приблизительно)
/*!40000 ALTER TABLE `ref` DISABLE KEYS */;
INSERT INTO `ref` (`id`, `name`) VALUES
	(1, 'http://test/contact'),
	(2, NULL),
	(3, 'http://test/statistics'),
	(4, 'http://test/about'),
	(5, 'http://test/dashboard'),
	(6, 'http://test/login');
/*!40000 ALTER TABLE `ref` ENABLE KEYS */;


-- Дамп структуры для таблица test_traff.spi_page
CREATE TABLE IF NOT EXISTS `spi_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_code` varchar(45) NOT NULL,
  `layout` varchar(45) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_without_login` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unq_code` (`page_code`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы test_traff.spi_page: ~5 rows (приблизительно)
/*!40000 ALTER TABLE `spi_page` DISABLE KEYS */;
INSERT INTO `spi_page` (`id`, `page_code`, `layout`, `name`, `is_without_login`) VALUES
	(1, 'dashboard', 'mainWithoutmenu', 'dashboard', 1),
	(2, 'contact', 'main', 'contact', 1),
	(3, 'visit', 'main', 'visit', 0),
	(4, 'login', 'main', 'login', 1),
	(5, 'about', 'main', 'about us', 1);
/*!40000 ALTER TABLE `spi_page` ENABLE KEYS */;


-- Дамп структуры для таблица test_traff.spi_page_position
CREATE TABLE IF NOT EXISTS `spi_page_position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `spi_page_position_page` (`page_id`),
  CONSTRAINT `spi_page_position_page` FOREIGN KEY (`page_id`) REFERENCES `spi_page` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Дамп данных таблицы test_traff.spi_page_position: ~0 rows (приблизительно)
/*!40000 ALTER TABLE `spi_page_position` DISABLE KEYS */;
/*!40000 ALTER TABLE `spi_page_position` ENABLE KEYS */;


-- Дамп структуры для таблица test_traff.spi_statistics
CREATE TABLE IF NOT EXISTS `spi_statistics` (
  `id` int(11) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Дамп данных таблицы test_traff.spi_statistics: ~0 rows (приблизительно)
/*!40000 ALTER TABLE `spi_statistics` DISABLE KEYS */;
/*!40000 ALTER TABLE `spi_statistics` ENABLE KEYS */;


-- Дамп структуры для таблица test_traff.spi_user
CREATE TABLE IF NOT EXISTS `spi_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL,
  `login` varchar(45) NOT NULL,
  `password` varchar(45) DEFAULT NULL,
  `sex` tinyint(1) NOT NULL,
  `first_name` varchar(45) NOT NULL,
  `last_name` varchar(45) NOT NULL,
  `email` varchar(45) NOT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `auth_token` varchar(32) DEFAULT NULL,
  `auth_token_created_at` datetime DEFAULT NULL,
  `recovery_token` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `spi_login_unq` (`login`),
  KEY `spi_email_unq` (`email`),
  KEY `spi_user_type_id` (`type_id`),
  CONSTRAINT `spi_user_type_id` FOREIGN KEY (`type_id`) REFERENCES `spi_user_type` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы test_traff.spi_user: ~1 rows (приблизительно)
/*!40000 ALTER TABLE `spi_user` DISABLE KEYS */;
INSERT INTO `spi_user` (`id`, `type_id`, `login`, `password`, `sex`, `first_name`, `last_name`, `email`, `phone`, `auth_token`, `auth_token_created_at`, `recovery_token`) VALUES
	(1, 1, 'admin', '098f6bcd4621d373cade4e832627b4f6', 0, 'Иван', 'Иванов', 'admin@mail.ru', '25-25-25', 'a0d3756f4bffe1e2d4bcf7034e7c08fe', '2016-07-01 03:30:59', NULL);
/*!40000 ALTER TABLE `spi_user` ENABLE KEYS */;


-- Дамп структуры для таблица test_traff.spi_user_type
CREATE TABLE IF NOT EXISTS `spi_user_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы test_traff.spi_user_type: ~2 rows (приблизительно)
/*!40000 ALTER TABLE `spi_user_type` DISABLE KEYS */;
INSERT INTO `spi_user_type` (`id`, `type`, `name`) VALUES
	(1, 'a', 'administrator'),
	(2, 'u', 'user');
/*!40000 ALTER TABLE `spi_user_type` ENABLE KEYS */;


-- Дамп структуры для таблица test_traff.spi_user_type_right
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

-- Дамп данных таблицы test_traff.spi_user_type_right: ~10 rows (приблизительно)
/*!40000 ALTER TABLE `spi_user_type_right` DISABLE KEYS */;
INSERT INTO `spi_user_type_right` (`id`, `type_id`, `page_id`, `can_show`, `can_view`, `can_edit`) VALUES
	(1, 1, 3, 1, 1, 1),
	(2, 1, 1, 1, 1, 1),
	(3, 1, 2, 1, 1, 1),
	(4, 1, 4, 1, 1, 1),
	(5, 1, 5, 1, 1, 1),
	(6, 2, 3, 1, 0, 0),
	(7, 2, 1, 1, 0, 0),
	(8, 2, 2, 1, 0, 0),
	(9, 2, 4, 1, 0, 0),
	(10, 2, 5, 1, 0, 0);
/*!40000 ALTER TABLE `spi_user_type_right` ENABLE KEYS */;


-- Дамп структуры для таблица test_traff.spi_visit
CREATE TABLE IF NOT EXISTS `spi_visit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `browser_id` int(11) NOT NULL DEFAULT '0',
  `os_id` int(11) NOT NULL DEFAULT '0',
  `city_id` int(11) NOT NULL DEFAULT '0',
  `ref_id` int(11) NOT NULL DEFAULT '0',
  `hint_id` int(11) NOT NULL DEFAULT '0',
  `ip_id` int(11) NOT NULL DEFAULT '0',
  `cookie_id` int(11) NOT NULL DEFAULT '0',
  `count` int(11) NOT NULL DEFAULT '0',
  `time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы test_traff.spi_visit: ~2 rows (приблизительно)
/*!40000 ALTER TABLE `spi_visit` DISABLE KEYS */;
INSERT INTO `spi_visit` (`id`, `browser_id`, `os_id`, `city_id`, `ref_id`, `hint_id`, `ip_id`, `cookie_id`, `count`, `time`) VALUES
	(1, 1, 1, 1, 1, 1, 1, 1, 8, 1467330317),
	(2, 1, 1, 1, 1, 2, 1, 1, 15, 1467330662),
	(3, 1, 1, 1, 2, 1, 1, 1, 1, 1467332917),
	(4, 1, 1, 1, 3, 3, 1, 1, 1, 1467333037),
	(5, 1, 1, 1, 4, 4, 1, 1, 1, 1467333046),
	(6, 1, 1, 1, 5, 5, 1, 1, 1, 1467333048),
	(7, 1, 1, 1, 6, 4, 1, 1, 1, 1467333059),
	(8, 1, 1, 1, 5, 6, 1, 1, 1, 1467333063),
	(9, 1, 1, 1, 5, 6, 1, 1, 1, 1467333075),
	(10, 1, 1, 1, 5, 6, 1, 1, 1, 1467333307),
	(11, 1, 1, 1, 5, 6, 1, 1, 1, 1467333436);
/*!40000 ALTER TABLE `spi_visit` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
