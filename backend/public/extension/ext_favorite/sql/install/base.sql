
CREATE TABLE IF NOT EXISTS `{plugin_identifier}` (
  `favorite_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `entity_type_extension_map_id` INT(10) unsigned NOT NULL,
  `actor_entity_id` bigint(20) unsigned NOT NULL,
  `target_entity_id` bigint(20) unsigned NOT NULL,
  `data_entity_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`favorite_id`),
  KEY `entity_type_extension_map_id` (`entity_type_extension_map_id`),
  KEY `actor_entity_id` (`actor_entity_id`),
  KEY `target_entity_id` (`target_entity_id`),
  KEY `data_entity_id` (`data_entity_id`),
  KEY `deleted_at` (`deleted_at`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



INSERT INTO `sys_extension`(`title`,`identifier`,`type`,`schema_json`,`created_at`) values ('{plugin_name}','{plugin_identifier}','general','{config}','{wildcard_datetime}') ON DUPLICATE KEY UPDATE updated_at = '{wildcard_datetime}';

