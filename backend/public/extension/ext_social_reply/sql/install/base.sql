
CREATE TABLE IF NOT EXISTS `{plugin_identifier}` (
  `social_reply_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `entity_type_extension_map_id` int(10) unsigned NOT NULL,
  `actor_entity_id` bigint(20) unsigned NOT NULL,
  `target_entity_id` bigint(20) unsigned NOT NULL,
  `data_entity_id` bigint(20) unsigned NOT NULL,
  `reply` longtext,
  `json_data` longtext,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`social_reply_id`),
  KEY `entity_type_extension_map_id` (`entity_type_extension_map_id`),
  KEY `actor_entity_id` (`actor_entity_id`),
  KEY `target_entity_id` (`target_entity_id`),
  KEY `data_entity_id` (`data_entity_id`),
  KEY `deleted_at` (`deleted_at`),
  FULLTEXT KEY `reply` (`reply`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



INSERT INTO `sys_extension`(`title`,`identifier`,`type`,`schema_json`,`created_at`) values ('{plugin_name}','{plugin_identifier}','social','{config}','{wildcard_datetime}') ON DUPLICATE KEY UPDATE updated_at = '{wildcard_datetime}';

