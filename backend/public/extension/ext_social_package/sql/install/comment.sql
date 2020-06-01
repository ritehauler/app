
CREATE TABLE IF NOT EXISTS `ext_package_comment` (
  `package_comment_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) unsigned DEFAULT '0',
  `activity_type_id` tinyint(3) unsigned NOT NULL,
  `entity_type_extension_map_id` int(10) unsigned NOT NULL,
  `actor_entity_id` bigint(20) unsigned NOT NULL,
  `target_entity_id` bigint(20) unsigned NOT NULL,
  `data_entity_id` bigint(20) unsigned NOT NULL,
  `content` longtext,
  `json_data` longtext,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`package_comment_id`),
  KEY `entity_type_extension_map_id` (`entity_type_extension_map_id`),
  KEY `activity_type_id` (`activity_type_id`),
  KEY `actor_entity_id` (`actor_entity_id`),
  KEY `target_entity_id` (`target_entity_id`),
  KEY `data_entity_id` (`data_entity_id`),
  KEY `deleted_at` (`deleted_at`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


