insert into `api_method` (`type`, `name`, `uri`, `method`, `schema`, `description`, `plugin_identifier`, `order`, `type_id`, `is_active`, `is_token_required`, `created_at`) values('get','Extension : {plugin_name} : Field Types','{api_base_route}/field_type/listing','',NULL,'API call to get field types','{plugin_identifier}','1','0','1','0','{wildcard_datetime}') ON DUPLICATE KEY UPDATE updated_at = '{wildcard_datetime}';

