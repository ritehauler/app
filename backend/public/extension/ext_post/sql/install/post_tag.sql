
CREATE TABLE IF NOT EXISTS `ext_post_tag` (
  `post_tag_id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity_type_extension_map_id` int(10) unsigned NOT NULL,
  `title` varchar(50) NOT NULL,
  `identifier` varchar(50) NOT NULL,
  `actor_entity_id` bigint(20) unsigned DEFAULT NULL COMMENT '// introduced by actor',
  `count_usage` bigint(20) unsigned DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`post_tag_id`),
  UNIQUE KEY `identifier` (`identifier`),
  KEY `actor_entity_id` (`actor_entity_id`),
  KEY `deleted_at` (`deleted_at`),
  KEY `count_usage` (`count_usage`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


insert  into `sys_history`(`identifier`,`plugin_identifier`,`notification_type`,`notify_entity`,`notify_target_entity`,`is_user_viewable`,`created_at`) values
('post_tag_add','{plugin_identifier}','none',0,0,0,'{wildcard_datetime}'),
('post_tag_update','{plugin_identifier}','none',0,0,0,'{wildcard_datetime}'),
('post_tag_delete','{plugin_identifier}','none',0,0,0,'{wildcard_datetime}');