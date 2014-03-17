DROP TABLE IF EXISTS `#__pixpublish_articles`;

CREATE TABLE `#__pixpublish_articles` (
  `pixpublish_article_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `category_id` int(11) NOT NULL,
  `asset_id` int(11) NOT NULL,
  `access` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) unsigned NOT NULL DEFAULT '0',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`pixpublish_article_id`),
  KEY `idx_published` (`published`),
  KEY `idx_access` (`access`),
  KEY `idx_category_id` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
