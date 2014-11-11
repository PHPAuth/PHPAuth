CREATE TABLE IF NOT EXISTS `groups` (
  `gid` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` varchar(128) NOT NULL,
  `group_desc` varchar(255) NOT NULL,
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`gid`),
  UNIQUE KEY `gid` (`gid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
--  table `groups`
--

INSERT INTO `groups` (`gid`, `group_name`, `group_desc`, `locked`) VALUES
(1, 'superadmin', 'This is the god among all. ', 1),
(2, 'admins', 'archangels are the right hand of him', 1),
(3, 'global_moderators', 'angels', 1)

-- --------------------------------------------------------

--
--  table `usergroups`
--

CREATE TABLE IF NOT EXISTS `usergroups` (
  `gid` mediumint(10) NOT NULL,
  `uid` mediumint(10) NOT NULL,
  `level` smallint(2) NOT NULL,
  KEY `gid` (`gid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `config` (`setting`, `value`) VALUES
('table_groups', 'groups'),
('table_usergroups', 'usergroups');