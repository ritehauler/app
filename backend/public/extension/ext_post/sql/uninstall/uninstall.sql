DROP TABLE IF EXISTS `{plugin_identifier}`;
DROP TABLE IF EXISTS `ext_package_like`;
DROP TABLE IF EXISTS `ext_package_rate`;
DROP TABLE IF EXISTS `ext_package_comment`;

DELETE FROM `api_method_field` WHERE `method_uri` IN (
	SELECT uri FROM `api_method`
	WHERE `plugin_identifier`= '{plugin_identifier}'
);

DELETE FROM `api_method` WHERE `plugin_identifier`= '{plugin_identifier}';

DELETE FROM `sys_entity_history` WHERE history_id IN (SELECT `history_id` FROM `sys_history` WHERE `plugin_identifier` = '{plugin_identifier}');

DELETE FROM `sys_history_notification` WHERE `history_identifier` IN (SELECT `identifier` FROM `sys_history` WHERE `plugin_identifier` = '{plugin_identifier}');

DELETE FROM `sys_history` WHERE `plugin_identifier` = '{plugin_identifier}';

DELETE FROM `sys_entity_type_extension_map` WHERE `extension_id` IN (
	SELECT extension_id FROM `sys_extension`
	WHERE `identifier` = '{plugin_identifier}'
);


UPDATE `sys_extension` SET `deleted_at` = '{wildcard_datetime}' WHERE `identifier` = '{plugin_identifier}';