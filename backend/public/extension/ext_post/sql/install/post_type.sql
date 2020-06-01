
CREATE TABLE IF NOT EXISTS `ext_post_type` (
  `post_type_id` tinyint(4) unsigned NOT NULL AUTO_INCREMENT,
  `identifier` varchar(50) NOT NULL,
  `title` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`post_type_id`),
  UNIQUE KEY `identifier` (`identifier`),
  KEY `title` (`title`),
  KEY `deleted_at` (`deleted_at`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


insert into `ext_post_type` (`identifier`, `title`, `created_at`) values('thought','Thought','{wildcard_datetime}');
insert into `ext_post_type` (`identifier`, `title`, `created_at`) values('idea','Idea','{wildcard_datetime}');
insert into `ext_post_type` (`identifier`, `title`, `created_at`) values('poll','Poll','{wildcard_datetime}');
insert into `ext_post_type` (`identifier`, `title`, `created_at`) values('task','Task','{wildcard_datetime}');
insert into `ext_post_type` (`identifier`, `title`, `created_at`) values('form','Open Form','{wildcard_datetime}');
insert into `ext_post_type` (`identifier`, `title`, `created_at`) values('link','Link','{wildcard_datetime}');
insert into `ext_post_type` (`identifier`, `title`, `created_at`) values('checkin','Checkin','{wildcard_datetime}');
insert into `ext_post_type` (`identifier`, `title`, `created_at`) values('newsfeed','newsfeed','{wildcard_datetime}');
insert into `ext_post_type` (`identifier`, `title`, `created_at`) values('event','Event','{wildcard_datetime}');
