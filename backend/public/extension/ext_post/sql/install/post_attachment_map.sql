
CREATE TABLE IF NOT EXISTS `ext_post_attachment_map` (
  `post_attachment_map_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) NOT NULL,
  `attachment_id` bigint(20) NOT NULL,
  `search_term` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`post_attachment_map_id`),
  UNIQUE KEY `unqiue` (`post_id`,`attachment_id`),
  KEY `deleted_at` (`deleted_at`),
  FULLTEXT KEY `search_term` (`search_term`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


insert  into `sys_history`(`identifier`,`plugin_identifier`,`notification_type`,`notify_entity`,`notify_target_entity`,`is_user_viewable`,`created_at`) values
('post_attachment_add','{plugin_identifier}','none',0,0,0,'{wildcard_datetime}'),
('post_attachment_update','{plugin_identifier}','none',0,0,0,'{wildcard_datetime}'),
('post_attachment_delete','{plugin_identifier}','none',0,0,0,'{wildcard_datetime}');