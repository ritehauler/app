<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ApiMethodField extends Base {
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table = 'api_method_field';
		$this->primaryKey = $this->__table . '_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'name', 'type', "request_type", 'method_uri','default_value','element_type','is_read_only','is_search', 'data_type', 'description', 'api_method_id','depend_table','depend_table_where','is_entity_auth','depend_table_title','depend_table_value', 'order', 'created_at', 'updated_at', 'deleted_at');
	}

	/**
	 * get Entity Attribute Lits
	 * @param integer entity_type_id
	 * @return NULL
	 */
	function getEntityAttributeList($entity_type_id,$list_order = false,$is_api = false) {
		// init data
		$data = array();

		// validate type
		$type = trim($entity_type_id);
		if($list_order)
			$sort_by = 'list_order';
			else
				$sort_by = 'sort_order';

        $query = "SELECT
                               sa.`default_value`,sa.`show_in_list` ,sa.`show_in_search`,sa.`entity_type_id` AS attribute_entity_type_id , sea.`attribute_id` AS attribute_id , sea.`attribute_set_id`  AS attribute_set_id , sea.`entity_attribute_id` AS entity_attribute_id , sea.`entity_type_id` AS entity_type_id , sa.`attribute_code` AS attribute_code ,  sdf.`identifier` AS data_type_identifier , sdf.`php_data_type` AS php_data_type , sa.`backend_table` AS backend_table, sa.`frontend_class` AS frontend_class, sa.`frontend_input` AS frontend_input, sa.`frontend_label` AS frontend_label, sa.`is_required` AS is_required, sas.`attribute_set_name` AS attribute_set_name, eet.`title` AS entity_title, GROUP_CONCAT(
                                            CONCAT(
                                                sao.`attribute_option_id`, ':', sao.`value`                                             )
                                        ) AS `attribute_options` , `model` ,sea.`view_at`, sea.`sort_order`,  sea.`is_read_only`,  sea.`frontend_label` AS entity_attr_frontend_label,
                                         sa.`use_entity_type` AS use_entity_type ,sa.`linked_entity_type_id` AS linked_entity_type_id, sa.`linked_attribute_id` AS linked_attribute_id,
                                         sa.`backend_table_option` AS backend_table_option, sa.`backend_table_value` AS backend_table_value, sea.`default_value` AS entity_attr_default_value,
                                         sea.`show_in_list` AS entity_attr_show_in_list, sea.`is_required` AS entity_attr_is_required,sa.data_type_id,sdf.php_data_type as data_type
                                         FROM (((((`sys_entity_attribute` `sea`
                                       LEFT JOIN `sys_attribute` `sa`
                                         ON ((`sa`.`attribute_id` = `sea`.`attribute_id`)))
                                      LEFT JOIN `sys_attribute_set` `sas`
                                        ON ((`sas`.`attribute_set_id` = `sea`.`attribute_set_id`)))
                                     LEFT JOIN `sys_entity_type` `eet`
                                       ON ((`eet`.`entity_type_id` = `sea`.`entity_type_id`)))
                                    LEFT JOIN `sys_attribute_option` `sao`
                                      ON ((`sao`.`attribute_id` = `sa`.`attribute_id`)))
                                   LEFT JOIN `sys_data_type` `sdf`
                                     ON ((sdf.data_type_id = `sa`.`data_type_id`)))
                                     WHERE sea.`entity_type_id`  = $type";

        if($is_api){
            $query .= " AND sea.api_column = 1";
        }

        $query .= " GROUP BY `sa`.`attribute_id` ORDER BY `sea`.`$sort_by`";
		// fetch
		$data = \DB::select($query);
 
		// return
		return $data;
	}
	
}