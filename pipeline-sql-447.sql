-- Setup for Bitbucket pipeline

CREATE DATABASE limeservice_statistics;
CREATE DATABASE limeservice_system;
CREATE USER 'statistics'@'127.0.0.1' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON *.* TO 'statistics'@'127.0.0.1';

CREATE TABLE `limeservice_system`.`installations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `domainname_id` int(11) NOT NULL DEFAULT '0',
  `subdomain` varchar(255) DEFAULT NULL,
  `adminuser` varchar(200) DEFAULT NULL,
  `adminemail` varchar(250) DEFAULT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `locked` int(1) NOT NULL DEFAULT '0',
  `super_locked` int(1) DEFAULT '0',
  `archived` datetime DEFAULT NULL,
  `deletionwarning1` datetime DEFAULT NULL,
  `deletionwarning2` datetime DEFAULT NULL,
  `reminderlimitresponses` int(11) NOT NULL DEFAULT '10',
  `remindersent` int(1) NOT NULL DEFAULT '0',
  `lastarchivefee` datetime DEFAULT NULL,
  `lastresponserenewal` datetime DEFAULT NULL,
  `statusemail` char(1) NOT NULL DEFAULT 'm',
  `laststatusemail` datetime DEFAULT NULL,
  `modified` datetime NOT NULL,
  `created` datetime NOT NULL,
  `subscription_created` datetime DEFAULT NULL,
  `subscription_paid` datetime DEFAULT NULL,
  `subscription_alias` varchar(25) DEFAULT NULL,
  `subscription_period` varchar(1) DEFAULT NULL,
  `ssl_included` tinyint(1) DEFAULT '0',
  `white_label` tinyint(1) DEFAULT '0',
  `advertising` tinyint(1) DEFAULT '0',
  `upload_storage_size` int(11) DEFAULT '100',
  `reminderlimitstorage` int(11) DEFAULT '10' COMMENT 'Same as storagereminder in #__limeservice_installations: Percentage of storage left when sending reminder email.',
  `storageremindersent` int(1) NOT NULL DEFAULT '0' COMMENT 'If true, a reminder has been send out about storage limit.',
  `locked_storage` int(1) NOT NULL DEFAULT '0',
  `hard_lock` tinyint(4) DEFAULT '0',
  `email_lock` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index_2` (`domainname_id`,`subdomain`)
) ENGINE=InnoDB AUTO_INCREMENT=21913 DEFAULT CHARSET=utf8;

CREATE TABLE `limeservice_system`.`balances` (
  `user_id` int(11) NOT NULL,
  `responses_avail` float NOT NULL DEFAULT '0',
  `responses_used` float NOT NULL DEFAULT '0',
  `responses_bought` float NOT NULL DEFAULT '0',
  `responses_complimentary` float NOT NULL DEFAULT '0',
  `responses_expired` float NOT NULL DEFAULT '0',
  `modified` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `storage_used` float DEFAULT '0',
  PRIMARY KEY (`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO limeservice_system.installations(user_id, modified, created) VALUES (12345, NOW(), NOW());

INSERT INTO balances(user_id) VALUES (12345);

FLUSH PRIVILEGES;
