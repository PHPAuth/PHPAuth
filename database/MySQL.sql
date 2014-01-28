SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE IF NOT EXISTS `activations` (
  `uid` int(11) NOT NULL,
  `activekey` varchar(20) NOT NULL,
  `expiredate` datetime NOT NULL,
  KEY `activekey` (`activekey`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `OTP` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `secret` varchar(56) NOT NULL,
  `backupkey` varchar(128) NOT NULL,
  `qr` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ;

CREATE TABLE IF NOT EXISTS `attempts` (
  `ip` varchar(39) NOT NULL,
  `count` int(11) NOT NULL,
  `expiredate` datetime NOT NULL,
  KEY `ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL DEFAULT 'UNKNOWN' COMMENT 'Username or UID',
  `action` varchar(100) NOT NULL,
  `info` varchar(1000) NOT NULL DEFAULT 'None provided',
  `ip` varchar(39) NOT NULL DEFAULT '0.0.0.0',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `resets` (
  `uid` int(11) NOT NULL,
  `resetkey` varchar(20) NOT NULL,
  `expiredate` datetime NOT NULL,
  KEY `uid` (`uid`),
  KEY `resetkey` (`resetkey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `hash` varchar(40) NOT NULL,
  `expiredate` datetime NOT NULL,
  `ip` varchar(39) NOT NULL,
  `agent` varchar(200) NOT NULL,
  `cookie_crc` varchar(40) NOT NULL,
  `lang` char(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `hash` (`hash`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `password` varchar(128) NOT NULL,
  `otp` int(11) NOT NULL DEFAULT '0',
  `email` varchar(100) NOT NULL,
  `salt` varchar(20) NOT NULL,
  `lang` char(2) NOT NULL DEFAULT 'en',
  `isactive` tinyint(1) NOT NULL DEFAULT '0',
  `level` int(11) NOT NULL DEFAULT '1',
  `pin` int(4) NOT NULL COMMENT 'User''s PIN code for modifications',
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `tracking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `DNT` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `continent` varchar(128) NOT NULL,
  `region` varchar(128) NOT NULL,
  `country` varchar(56) NOT NULL,
  `landingpage` text NOT NULL,
  `landinghost` text NOT NULL,
  `referrer` text NOT NULL,
  `requestmethod` varchar(8) NOT NULL,
  `os` varchar(255) NOT NULL,
  `host` varchar(255) NOT NULL,
  `browser` varchar(255) NOT NULL,
  `cookies` text NOT NULL,
  `datetime` varchar(24) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=48 ;

CREATE TABLE IF NOT EXISTS `usergroups` (
  `id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `desc` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
