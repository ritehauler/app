
CREATE TABLE IF NOT EXISTS `ext_post_tag_map` (
  `post_tag_map_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL,
  `post_tag_id` bigint(20) unsigned NOT NULL,
  `label` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`post_tag_map_id`),
  KEY `deleted_at` (`deleted_at`),
  KEY `post_id` (`post_id`),
  KEY `post_tag_id` (`post_tag_id`),
  FULLTEXT KEY `label` (`label`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
