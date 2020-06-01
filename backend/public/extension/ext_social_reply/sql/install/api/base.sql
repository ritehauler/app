

insert  into `api_method` (`plugin_identifier`,`type`,`name`,`uri`,`description`,`order`,`is_active`,`created_at`) values
('{plugin_identifier}','post','Extension : Social : {plugin_name} : Post','{api_base_route}','API call to post a Reply',1,1,'{wildcard_datetime}') ON DUPLICATE KEY UPDATE updated_at = '{wildcard_datetime}';

insert into `api_method_field` (`method_uri`, `request_type`, `type`, `data_type`, `name`, `description`, `element_type`, `is_read_only`, `is_search`, `element_validation_type`, `depend_table`, `depend_table_id`, `depend_table_where`, `depend_table_title`, `depend_table_value`, `is_entity_auth`, `default_value`, `is_active`, `order`, `created_at`) values('{api_base_route}','post','required','int','target_entity_type_id','Target Entity Type ID','option','0','1','string','sys_entity_type','0',NULL,NULL,'entity_type_id','0','','1','1','{wildcard_datetime}');

insert into `api_method_field` (`method_uri`, `request_type`, `type`, `data_type`, `name`, `description`, `element_type`, `is_read_only`, `is_search`, `element_validation_type`, `depend_table`, `depend_table_id`, `depend_table_where`, `depend_table_title`, `depend_table_value`, `is_entity_auth`, `default_value`, `is_active`, `order`, `created_at`) values('{api_base_route}','post','required','int','actor_entity_type_id','Actor Entity Type ID','option','0','1','string','sys_entity_type','0',NULL,NULL,'entity_type_id','0','','1','2','{wildcard_datetime}');

insert into `api_method_field` (`method_uri`, `request_type`, `type`, `data_type`, `name`, `description`, `element_type`, `is_read_only`, `is_search`, `element_validation_type`, `depend_table`, `depend_table_id`, `depend_table_where`, `depend_table_title`, `depend_table_value`, `is_entity_auth`, `default_value`, `is_active`, `order`, `created_at`) values('{api_base_route}','post','required','int','target_entity_id','Target Entity ID where activity is being performed','text','0','1','integer','','0',NULL,NULL,NULL,'0','','1','3','{wildcard_datetime}');

insert into `api_method_field` (`method_uri`, `request_type`, `type`, `data_type`, `name`, `description`, `element_type`, `is_read_only`, `is_search`, `element_validation_type`, `depend_table`, `depend_table_id`, `depend_table_where`, `depend_table_title`, `depend_table_value`, `is_entity_auth`, `default_value`, `is_active`, `order`, `created_at`) values('{api_base_route}','post','required','int','actor_entity_id','Actor Entity ID','text','0','1','integer','','0',NULL,NULL,NULL,'0','','1','4','{wildcard_datetime}');


insert into `api_method_field` (`method_uri`, `request_type`, `type`, `data_type`, `name`, `description`, `element_type`, `is_read_only`, `is_search`, `element_validation_type`, `depend_table`, `depend_table_id`, `depend_table_where`, `depend_table_title`, `depend_table_value`, `is_entity_auth`, `default_value`, `is_active`, `order`, `created_at`) values('{api_base_route}','post','required','text','reply','Reply','text','0','1','text','','0',NULL,NULL,NULL,'0','','1','5','{wildcard_datetime}');

insert into `api_method_field` (`method_uri`, `request_type`, `type`, `data_type`, `name`, `description`, `element_type`, `is_read_only`, `is_search`, `element_validation_type`, `depend_table`, `depend_table_id`, `depend_table_where`, `depend_table_title`, `depend_table_value`, `is_entity_auth`, `default_value`, `is_active`, `order`, `created_at`) values('{api_base_route}','post','required','text','json_data','JSON data (required without reply)','text','0','1','text','','0',NULL,NULL,NULL,'0','','1','6','{wildcard_datetime}');




insert  into `api_method`(`plugin_identifier`,`type`,`name`,`uri`,`description`,`order`,`is_active`,`created_at`) values
('{plugin_identifier}','get','Extension : Social : {plugin_name} : Listing','{api_base_route}/listing','API call to retrieve entity replys',1,1,'{wildcard_datetime}') ON DUPLICATE KEY UPDATE updated_at = '{wildcard_datetime}';


insert into `api_method_field` (`method_uri`, `request_type`, `type`, `data_type`, `name`, `description`, `element_type`, `is_read_only`, `is_search`, `element_validation_type`, `depend_table`, `depend_table_id`, `depend_table_where`, `depend_table_title`, `depend_table_value`, `is_entity_auth`, `default_value`, `is_active`, `order`, `created_at`) values('{api_base_route}/listing','get','required','int','target_entity_type_id','Target Target Entity Type ID','option','0','1','string','sys_entity_type','0',NULL,NULL,'entity_type_id','0','','1','1','{wildcard_datetime}');

insert into `api_method_field` (`method_uri`, `request_type`, `type`, `data_type`, `name`, `description`, `element_type`, `is_read_only`, `is_search`, `element_validation_type`, `depend_table`, `depend_table_id`, `depend_table_where`, `depend_table_title`, `depend_table_value`, `is_entity_auth`, `default_value`, `is_active`, `order`, `created_at`) values('{api_base_route}/listing','get','required','int','actor_entity_type_id','Actor Entity Type ID where activity is being performed','option','0','1','string','sys_entity_type','0',NULL,NULL,'entity_type_id','0','','1','2','{wildcard_datetime}');

insert into `api_method_field` (`method_uri`, `request_type`, `type`, `data_type`, `name`, `description`, `element_type`, `is_read_only`, `is_search`, `element_validation_type`, `depend_table`, `depend_table_id`, `depend_table_where`, `depend_table_title`, `depend_table_value`, `is_entity_auth`, `default_value`, `is_active`, `order`, `created_at`) values('{api_base_route}/listing','get','optional','text','target_entity_id','Target Entity ID (i.e:post,reply)','integer','0','1','string','','0',NULL,NULL,NULL,'0','','1','3','{wildcard_datetime}');

insert into `api_method_field` (`method_uri`, `request_type`, `type`, `data_type`, `name`, `description`, `element_type`, `is_read_only`, `is_search`, `element_validation_type`, `depend_table`, `depend_table_id`, `depend_table_where`, `depend_table_title`, `depend_table_value`, `is_entity_auth`, `default_value`, `is_active`, `order`, `created_at`) values('{api_base_route}/listing','get','optional','int','actor_entity_id','Actor Entity ID','text','0','1','integer','','0',NULL,NULL,NULL,'0','','1','4','{wildcard_datetime}');

insert into `api_method_field` (`method_uri`, `request_type`, `type`, `data_type`, `name`, `description`, `element_type`, `is_read_only`, `is_search`, `element_validation_type`, `depend_table`, `depend_table_id`, `depend_table_where`, `depend_table_title`, `depend_table_value`, `is_entity_auth`, `default_value`, `is_active`, `order`, `created_at`) values('{api_base_route}/listing','get','optional','text','order_by','Order by (def:created_at,entity_id)','text','0','1','string','','0',NULL,NULL,NULL,'0','','1','5','{wildcard_datetime}');

insert into `api_method_field` (`method_uri`, `request_type`, `type`, `data_type`, `name`, `description`, `element_type`, `is_read_only`, `is_search`, `element_validation_type`, `depend_table`, `depend_table_id`, `depend_table_where`, `depend_table_title`, `depend_table_value`, `is_entity_auth`, `default_value`, `is_active`, `order`, `created_at`) values('{api_base_route}/listing','get','optional','int','sorting','Sorting (def:desc,asc)','text','0','1','string','','0',NULL,NULL,NULL,'0','','1','6','{wildcard_datetime}');

insert into `api_method_field` (`method_uri`, `request_type`, `type`, `data_type`, `name`, `description`, `element_type`, `is_read_only`, `is_search`, `element_validation_type`, `depend_table`, `depend_table_id`, `depend_table_where`, `depend_table_title`, `depend_table_value`, `is_entity_auth`, `default_value`, `is_active`, `order`, `created_at`) values('{api_base_route}/listing','get','optional','int','offset','Offset (def:0)','integer','0','1','string','','0',NULL,NULL,NULL,'0','','1','7','{wildcard_datetime}');

insert into `api_method_field` (`method_uri`, `request_type`, `type`, `data_type`, `name`, `description`, `element_type`, `is_read_only`, `is_search`, `element_validation_type`, `depend_table`, `depend_table_id`, `depend_table_where`, `depend_table_title`, `depend_table_value`, `is_entity_auth`, `default_value`, `is_active`, `order`, `created_at`) values('{api_base_route}/listing','get','optional','int','limit','Limit (def:10)','integer','0','1','string','','0',NULL,NULL,NULL,'0','','1','8','{wildcard_datetime}');




insert  into `api_method` (`plugin_identifier`,`type`,`name`,`uri`,`description`,`order`,`is_active`,`created_at`) values
  ('{plugin_identifier}','post','Extension : Social : {plugin_name} : Update','{api_base_route}/update','API call to update a Reply',1,1,'{wildcard_datetime}') ON DUPLICATE KEY UPDATE updated_at = '{wildcard_datetime}';

insert into `api_method_field` (`method_uri`, `request_type`, `type`, `data_type`, `name`, `description`, `element_type`, `is_read_only`, `is_search`, `element_validation_type`, `depend_table`, `depend_table_id`, `depend_table_where`, `depend_table_title`, `depend_table_value`, `is_entity_auth`, `default_value`, `is_active`, `order`, `created_at`) values('{api_base_route}/update','post','required','int','target_entity_type_id','Target Entity Type ID','option','0','1','string','sys_entity_type','0',NULL,NULL,'entity_type_id','0','','1','1','{wildcard_datetime}');

insert into `api_method_field` (`method_uri`, `request_type`, `type`, `data_type`, `name`, `description`, `element_type`, `is_read_only`, `is_search`, `element_validation_type`, `depend_table`, `depend_table_id`, `depend_table_where`, `depend_table_title`, `depend_table_value`, `is_entity_auth`, `default_value`, `is_active`, `order`, `created_at`) values('{api_base_route}/update','post','required','int','actor_entity_type_id','Actor Entity Type ID','option','0','1','string','sys_entity_type','0',NULL,NULL,'entity_type_id','0','','1','2','{wildcard_datetime}');


insert into `api_method_field` (`method_uri`, `request_type`, `type`, `data_type`, `name`, `description`, `element_type`, `is_read_only`, `is_search`, `element_validation_type`, `depend_table`, `depend_table_id`, `depend_table_where`, `depend_table_title`, `depend_table_value`, `is_entity_auth`, `default_value`, `is_active`, `order`, `created_at`) values('{api_base_route}/update','post','required','int','actor_entity_id','Actor Entity ID','text','0','1','integer','','0',NULL,NULL,NULL,'0','','1','3','{wildcard_datetime}');

insert into `api_method_field` (`method_uri`, `request_type`, `type`, `data_type`, `name`, `description`, `element_type`, `is_read_only`, `is_search`, `element_validation_type`, `depend_table`, `depend_table_id`, `depend_table_where`, `depend_table_title`, `depend_table_value`, `is_entity_auth`, `default_value`, `is_active`, `order`, `created_at`) values('{api_base_route}/update','post','required','int','social_reply_id','Reply id which needs to be updated','text','0','1','integer','','0',NULL,NULL,NULL,'0','','1','4','{wildcard_datetime}');

insert into `api_method_field` (`method_uri`, `request_type`, `type`, `data_type`, `name`, `description`, `element_type`, `is_read_only`, `is_search`, `element_validation_type`, `depend_table`, `depend_table_id`, `depend_table_where`, `depend_table_title`, `depend_table_value`, `is_entity_auth`, `default_value`, `is_active`, `order`, `created_at`) values('{api_base_route}/update','post','required','text','reply','Reply','text','0','1','text','','0',NULL,NULL,NULL,'0','','1','5','{wildcard_datetime}');

insert into `api_method_field` (`method_uri`, `request_type`, `type`, `data_type`, `name`, `description`, `element_type`, `is_read_only`, `is_search`, `element_validation_type`, `depend_table`, `depend_table_id`, `depend_table_where`, `depend_table_title`, `depend_table_value`, `is_entity_auth`, `default_value`, `is_active`, `order`, `created_at`) values('{api_base_route}/update','post','required','text','json_data','JSON data (required without reply)','text','0','1','text','','0',NULL,NULL,NULL,'0','','1','6','{wildcard_datetime}');



insert  into `api_method` (`plugin_identifier`,`type`,`name`,`uri`,`description`,`order`,`is_active`,`created_at`) values
  ('{plugin_identifier}','post','Extension : Social : {plugin_name} : Delete','{api_base_route}/delete','API call to delete a Reply',1,1,'{wildcard_datetime}') ON DUPLICATE KEY UPDATE updated_at = '{wildcard_datetime}';

insert into `api_method_field` (`method_uri`, `request_type`, `type`, `data_type`, `name`, `description`, `element_type`, `is_read_only`, `is_search`, `element_validation_type`, `depend_table`, `depend_table_id`, `depend_table_where`, `depend_table_title`, `depend_table_value`, `is_entity_auth`, `default_value`, `is_active`, `order`, `created_at`) values('{api_base_route}/delete','post','required','int','target_entity_type_id','Target Entity Type ID','option','0','1','string','sys_entity_type','0',NULL,NULL,'entity_type_id','0','','1','1','{wildcard_datetime}');

insert into `api_method_field` (`method_uri`, `request_type`, `type`, `data_type`, `name`, `description`, `element_type`, `is_read_only`, `is_search`, `element_validation_type`, `depend_table`, `depend_table_id`, `depend_table_where`, `depend_table_title`, `depend_table_value`, `is_entity_auth`, `default_value`, `is_active`, `order`, `created_at`) values('{api_base_route}/delete','post','required','int','actor_entity_type_id','Actor Entity Type ID','option','0','1','string','sys_entity_type','0',NULL,NULL,'entity_type_id','0','','1','2','{wildcard_datetime}');


insert into `api_method_field` (`method_uri`, `request_type`, `type`, `data_type`, `name`, `description`, `element_type`, `is_read_only`, `is_search`, `element_validation_type`, `depend_table`, `depend_table_id`, `depend_table_where`, `depend_table_title`, `depend_table_value`, `is_entity_auth`, `default_value`, `is_active`, `order`, `created_at`) values('{api_base_route}/delete','post','required','int','actor_entity_id','Actor Entity ID','text','0','1','integer','','0',NULL,NULL,NULL,'0','','1','3','{wildcard_datetime}');

insert into `api_method_field` (`method_uri`, `request_type`, `type`, `data_type`, `name`, `description`, `element_type`, `is_read_only`, `is_search`, `element_validation_type`, `depend_table`, `depend_table_id`, `depend_table_where`, `depend_table_title`, `depend_table_value`, `is_entity_auth`, `default_value`, `is_active`, `order`, `created_at`) values('{api_base_route}/delete','post','required','int','social_reply_id','Reply id which needs to be updated','text','0','1','integer','','0',NULL,NULL,NULL,'0','','1','4','{wildcard_datetime}');
