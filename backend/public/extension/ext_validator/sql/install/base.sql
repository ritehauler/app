

CREATE TABLE IF NOT EXISTS `fb_entity_type_form_map` (
  `entity_type_form_map_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `target_entity_type_id` tinyint(3) unsigned NOT NULL,
  `actor_entity_type_id` tinyint(3) unsigned NOT NULL,
  `actor_entity_id` bigint(20) unsigned NOT NULL,
  `form_id` int(10) unsigned NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` longtext,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`entity_type_form_map_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `fb_field_type` (
  `field_type_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `identifier` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `input_type` enum('text','textarea','password','select','checkbox','radio','editor') NOT NULL DEFAULT 'text',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`field_type_id`),
  UNIQUE KEY `identifier` (`identifier`),
  KEY `input_type` (`input_type`),
  KEY `deleted_at` (`deleted_at`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


insert into `fb_field_type` (`identifier`, `title`, `input_type`, `created_at`) values('text','Text','text','{wildcard_datetime}');
insert into `fb_field_type` (`identifier`, `title`, `input_type`, `created_at`) values('password','Password','password','{wildcard_datetime}');
insert into `fb_field_type` (`identifier`, `title`, `input_type`, `created_at`) values('checkbox','Checkbox (Multi Select)','checkbox','{wildcard_datetime}');
insert into `fb_field_type` (`identifier`, `title`, `input_type`, `created_at`) values('radio','Radio Options','radio','{wildcard_datetime}');
insert into `fb_field_type` (`identifier`, `title`, `input_type`, `created_at`) values('select','Select Box','select','{wildcard_datetime}');
insert into `fb_field_type` (`identifier`, `title`, `input_type`, `created_at`) values('textarea','Big Text','textarea','{wildcard_datetime}');
insert into `fb_field_type` (`identifier`, `title`, `input_type`, `created_at`) values('editor','Editor','editor','{wildcard_datetime}');


CREATE TABLE IF NOT EXISTS `fb_form` (
  `form_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` longtext,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`form_id`),
  KEY `deleted_at` (`deleted_at`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE IF NOT EXISTS `fb_form_field_map` (
  `form_field_map_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `entity_type_form_map_id` int(10) unsigned NOT NULL,
  `field_type_id` tinyint(3) unsigned NOT NULL,
  `field_name` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text,
  `hint` text,
  `html5_input_type` varchar(15) DEFAULT NULL,
  `js_validation_type` enum('default','regex','custom') NOT NULL DEFAULT 'default',
  `js_validation_rule` longtext,
  `js_validation_event` enum('none','onclick','onblur','onchange','onfocus','onselect') NOT NULL DEFAULT 'none',
  `php_validation_type` enum('default','regex','custom') NOT NULL DEFAULT 'default',
  `php_validation_rule` longtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`form_field_map_id`),
  KEY `entity_type_form_map_id` (`entity_type_form_map_id`),
  KEY `field_type_id` (`field_type_id`),
  KEY `html5_input_type` (`html5_input_type`),
  KEY `js_validation_type` (`js_validation_type`),
  KEY `js_validation_event` (`js_validation_event`),
  KEY `php_validation_type` (`php_validation_type`),
  KEY `deleted_at` (`deleted_at`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


INSERT INTO `sys_extension`(`title`,`identifier`,`type`,`schema_json`,`is_required_assigning`,`created_at`) values ('{plugin_name}','{plugin_identifier}','social','{config}',0,'{wildcard_datetime}') ON DUPLICATE KEY UPDATE updated_at = '{wildcard_datetime}';

