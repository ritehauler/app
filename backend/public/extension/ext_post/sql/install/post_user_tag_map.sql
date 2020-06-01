
CREATE TABLE IF NOT EXISTS `ext_post_user_tag_map` (
  `post_user_tag_map_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL,
  `actor_entity_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`post_user_tag_map_id`),
  KEY `post_id` (`post_id`),
  KEY `actor_entity_id` (`actor_entity_id`),
  KEY `deleted_at` (`deleted_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

