
CREATE TABLE IF NOT EXISTS `ext_activity_type` (
  `activity_type_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('like','emoticon','rate','favorite','comment') NOT NULL DEFAULT 'like',
  `title` varchar(100) NOT NULL,
  `identifier` varchar(50) NOT NULL,
  `icon_src` varchar(100) DEFAULT NULL,
  `description` text,
  `json_configuration` longtext,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`activity_type_id`),
  KEY `type` (`type`),
  KEY `identifier` (`identifier`),
  KEY `deleted_at` (`deleted_at`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

insert into `ext_activity_type` (`type`, `title`, `identifier`, `icon_src`, `description`, `json_configuration`, `created_at`) values('like','Like / Unlike','like',NULL,NULL,NULL,'{wildcard_datetime}');
insert into `ext_activity_type` (`type`, `title`, `identifier`, `icon_src`, `description`, `json_configuration`, `created_at`) values('emoticon','Thumbs Up','thumbs_up',NULL,NULL,NULL,'{wildcard_datetime}');
insert into `ext_activity_type` (`type`, `title`, `identifier`, `icon_src`, `description`, `json_configuration`, `created_at`) values('emoticon','Laugh','laugh',NULL,NULL,NULL,'{wildcard_datetime}');
insert into `ext_activity_type` (`type`, `title`, `identifier`, `icon_src`, `description`, `json_configuration`, `created_at`) values('emoticon','Cry','cry',NULL,NULL,NULL,'{wildcard_datetime}');
insert into `ext_activity_type` (`type`, `title`, `identifier`, `icon_src`, `description`, `json_configuration`, `created_at`) values('emoticon','Angry','angry',NULL,NULL,NULL,'{wildcard_datetime}');
insert into `ext_activity_type` (`type`, `title`, `identifier`, `icon_src`, `description`, `json_configuration`, `created_at`) values('emoticon','Thumbs Down','thumbs_down',NULL,NULL,NULL,'{wildcard_datetime}');
insert into `ext_activity_type` (`type`, `title`, `identifier`, `icon_src`, `description`, `json_configuration`, `created_at`) values('favorite','Favorite / Remove Favorite','favorite',NULL,NULL,NULL,'{wildcard_datetime}');
insert into `ext_activity_type` (`type`, `title`, `identifier`, `icon_src`, `description`, `json_configuration`, `created_at`) values('rate','Rate','rate',NULL,NULL,NULL,'{wildcard_datetime}');
insert into `ext_activity_type` (`type`, `title`, `identifier`, `icon_src`, `description`, `json_configuration`, `created_at`) values('comment','Comment / Reply','comment',NULL,NULL,NULL,'{wildcard_datetime}');



CREATE TABLE IF NOT EXISTS `ext_social_activity` (
  `social_activity_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `activity_type_id` tinyint(3) unsigned NOT NULL,
  `entity_type_extension_map_id` int(10) unsigned NOT NULL,
  `actor_entity_id` bigint(20) unsigned NOT NULL,
  `target_entity_id` bigint(20) unsigned NOT NULL,
  `data_entity_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`social_activity_id`),
  KEY `activity_type_id` (`activity_type_id`),
  KEY `entity_type_extension_map_id` (`entity_type_extension_map_id`),
  KEY `actor_entity_id` (`actor_entity_id`),
  KEY `target_entity_id` (`target_entity_id`),
  KEY `data_entity_id` (`data_entity_id`),
  KEY `deleted_at` (`deleted_at`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


INSERT INTO `sys_extension`(`title`,`identifier`,`type`,`schema_json`,`created_at`) values ('{plugin_name}','{plugin_identifier}','social','{config}','{wildcard_datetime}') ON DUPLICATE KEY UPDATE updated_at = '{wildcard_datetime}';

