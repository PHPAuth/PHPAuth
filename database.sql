-- Adminer 4.2.0 MySQL dump

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `config`;
CREATE TABLE `config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting` varchar(100) NOT NULL,
  `value` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;

INSERT INTO `config` (`id`, `setting`, `value`) VALUES
(1,	    'site_name',	'PHPAuth'),
(2,	    'site_url',	'https://github.com/PHPAuth/PHPAuth'),
(3,	    'site_email',	'no-reply@phpauth.cuonic.com'),
(4,	    'site_key',	'fghuior.)/!/jdUkd8s2!7HVHG7777ghg'),
(5,	    'site_timezone',	'Europe/Paris'),
(6,	    'site_activation_page',	'activate'),
(7,	    'site_password_reset_page',	'reset'),
(8,	    'cookie_name',	'authID'),
(9,	    'cookie_path',	'/'),
(10,	'cookie_domain',	NULL),
(11,	'cookie_secure',	'0'),
(12,	'cookie_http',	'0'),
(13,	'cookie_remember',	'+1 month'),
(14,	'cookie_forget',	'+30 minutes'),
(15,	'bcrypt_cost',	'10'),
(16,	'table_attempts',	'attempts'),
(17,	'table_requests',	'requests'),
(18,	'table_sessions',	'sessions'),
(19,	'table_users',	'users'),
(20,	'smtp',	'0'),
(21,	'smtp_host',	'smtp.example.com'),
(22,	'smtp_auth',	'1'),
(23,	'smtp_username',	'email@example.com'),
(24,	'smtp_password',	'password'),
(25,	'smtp_port',	'25'),
(26,	'smtp_security',	NULL),
(27,    'verify_password_min_length', '3'),
(28,    'verify_password_max_length', '150'),
(29,    'verify_password_strong_requirements',  '1'),
(30,    'verify_email_min_length',  '5'),
(31,    'verify_email_max_length',  '100'),
(32,    'verify_email_use_banlist', '1'),
(33,    'attack_mitigation_time', '+30 minutes'),
(34,    'attempts_before_verify', '5'),
(35,    'attempts_before_ban', '30');

DROP TABLE IF EXISTS `attempts`;
CREATE TABLE `attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(39) NOT NULL,
  `expiredate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
