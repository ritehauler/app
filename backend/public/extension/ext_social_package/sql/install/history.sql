
CREATE TABLE IF NOT EXISTS `sys_history` (
  `history_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `identifier` varchar(50) NOT NULL DEFAULT '',
  `plugin_identifier` varchar(50) DEFAULT NULL,
  `notification_type` enum('none','email','push','email_push') NOT NULL DEFAULT 'none',
  `notify_entity` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `notify_target_entity` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_user_viewable` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`history_id`),
  UNIQUE KEY `uk_identifier` (`identifier`,`plugin_identifier`),
  KEY `is_user_viewable` (`is_user_viewable`),
  KEY `notification_type` (`notification_type`),
  KEY `deleted_at` (`deleted_at`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

insert  into `sys_history`(`identifier`,`plugin_identifier`,`notification_type`,`notify_entity`,`notify_target_entity`,`is_user_viewable`,`created_at`) values
('like_add','{plugin_identifier}','none',0,0,0,'{wildcard_datetime}'),
('like_delete','{plugin_identifier}','none',0,0,0,'{wildcard_datetime}'),
('rate_add','{plugin_identifier}','none',0,0,0,'{wildcard_datetime}'),
('rate_update','{plugin_identifier}','none',0,0,0,'{wildcard_datetime}'),
('rate_delete','{plugin_identifier}','none',0,0,0,'{wildcard_datetime}'),
('comment_add','{plugin_identifier}','none',0,0,0,'{wildcard_datetime}'),
('comment_update','{plugin_identifier}','none',0,0,0,'{wildcard_datetime}'),
('comment_delete','{plugin_identifier}','none',0,0,0,'{wildcard_datetime}'),
('reply_add','{plugin_identifier}','none',0,0,0,'{wildcard_datetime}'),
('reply_update','{plugin_identifier}','none',0,0,0,'{wildcard_datetime}'),
('reply_delete','{plugin_identifier}','none',0,0,0,'{wildcard_datetime}');




CREATE TABLE IF NOT EXISTS `sys_entity_history` (
  `entity_history_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `history_id` int(10) unsigned NOT NULL DEFAULT '0',
  `entity_type_id` int(11) unsigned DEFAULT '0',
  `entity_id` bigint(11) unsigned DEFAULT NULL,
  `actor_entity_type_id` int(11) unsigned NOT NULL DEFAULT '0',
  `actor_entity_id` bigint(20) unsigned NOT NULL,
  `against_entity_type_id` int(50) NOT NULL DEFAULT '0',
  `against_entity_id` bigint(20) unsigned NOT NULL,
  `extension_ref_table` varchar(50) DEFAULT NULL,
  `extension_ref_id` bigint(20) unsigned DEFAULT NULL,
  `tracking_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `is_read` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_archive` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `request_params` longtext CHARACTER SET latin1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`entity_history_id`),
  KEY `history_id` (`history_id`),
  KEY `is_read` (`is_read`),
  KEY `deleted_at` (`deleted_at`),
  KEY `entity_type_id` (`entity_type_id`),
  KEY `entity_id` (`entity_id`),
  KEY `actor_entity_type_id` (`actor_entity_type_id`),
  KEY `actor_entity_id` (`actor_entity_id`),
  KEY `against_entity_type_id` (`against_entity_type_id`),
  KEY `against_entity_id` (`against_entity_id`),
  KEY `is_archive` (`is_archive`),
  KEY `extension_ref_table` (`extension_ref_table`),
  KEY `extension_ref_id` (`extension_ref_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `sys_history_notification` (
  `history_notification_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `history_identifier` varchar(50) NOT NULL,
  `plugin_identifier` varchar(50) DEFAULT NULL,
  `lang_identifier` varchar(3) NOT NULL DEFAULT 'en',
  `type` enum('push','email') NOT NULL DEFAULT 'push',
  `for` enum('to_entity','to_target_entity') NOT NULL DEFAULT 'to_entity',
  `title` varchar(255) NOT NULL DEFAULT '',
  `body` longtext,
  `key_code` varchar(10) NOT NULL,
  `hint` varchar(255) NOT NULL DEFAULT '',
  `wildcards` varchar(255) NOT NULL DEFAULT '',
  `replacers` varchar(255) NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`history_notification_id`),
  UNIQUE KEY `unique_key` (`history_identifier`,`plugin_identifier`,`lang_identifier`),
  KEY `type` (`type`),
  KEY `for` (`for`),
  KEY `history_identifier` (`history_identifier`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

