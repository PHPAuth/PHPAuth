-- Adminer 4.2.0 MySQL dump

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `attempts`;
CREATE TABLE `attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(39) NOT NULL,
  `count` int(11) NOT NULL,
  `expiredate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `config`;
CREATE TABLE `config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting` varchar(100) NOT NULL,
  `value` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;

INSERT INTO `config` (`id`, `setting`, `value`) VALUES
(1,	'site_name',	'PHPAuth'),
(2,	'site_url',	'https://github.com/PHPAuth/PHPAuth'),
(3,	'site_email',	'no-reply@phpauth.cuonic.com'),
(4,	'cookie_name',	'authID'),
(5,	'cookie_path',	'/'),
(6,	'cookie_domain',	NULL),
(7,	'cookie_secure',	'0'),
(8,	'cookie_http',	'0'),
(9,	'site_key',	'fghuior.)/!/jdUkd8s2!7HVHG7777ghg'),
(10,	'cookie_remember',	'+1 month'),
(11,	'cookie_forget',	'+30 minutes'),
(12,	'bcrypt_cost',	'10'),
(13,	'table_attempts',	'attempts'),
(14,	'table_requests',	'requests'),
(15,	'table_sessions',	'sessions'),
(16,	'table_users',	'users'),
(17,	'site_timezone',	'Europe/Paris'),
(18,	'site_activation_page',	'activate'),
(19,	'site_password_reset_page',	'reset'),
(20,	'smtp',	'0'),
(21,	'smtp_host',	'smtp.example.com'),
(22,	'smtp_auth',	'1'),
(23,	'smtp_username',	'email@example.com'),
(24,	'smtp_password',	'password'),
(25,	'smtp_port',	'25'),
(26,	'smtp_security',	NULL);

DROP TABLE IF EXISTS `requests`;
CREATE TABLE `requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `rkey` varchar(20) NOT NULL,
  `expire` datetime NOT NULL,
  `type` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `hash` varchar(40) NOT NULL,
  `expiredate` datetime NOT NULL,
  `ip` varchar(39) NOT NULL,
  `agent` varchar(200) NOT NULL,
  `cookie_crc` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(60) DEFAULT NULL,
  `isactive` tinyint(1) NOT NULL DEFAULT '0',
  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


-- 2015-05-08 20:15:43
