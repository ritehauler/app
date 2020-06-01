
CREATE TABLE IF NOT EXISTS `{plugin_identifier}` (
  `social_like_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `entity_id` bigint(20) unsigned NOT NULL,
  `actor_entity_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`social_like_id`),
  KEY `entity_id` (`entity_id`),
  KEY `actor_entity_id` (`actor_entity_id`),
  KEY `deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;





