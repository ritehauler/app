<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\SoftDeletes as SoftDeletes;

// init models
//use App\Http\Models\Conf;

class SYSEntityAttribute extends Base
{

    use SoftDeletes;
    public $table = 'sys_entity_attribute';
    public $timestamps = TRUE;
    public $primaryKey;
    protected $dates = ['deleted_at'];

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->primaryKey = 'entity_attribute_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'entity_type_id', 'attribute_set_id', 'attribute_id', 'sort_order', 'placeholder', 'frontend_label', 'frontend_input', 'default_value', 'classes', 'validation', 'is_unique', 'is_read_only', 'is_required', 'show_in_list', 'show_in_search', 'view_at', 'searchable_type','list_order','api_column','is_default_option', 'created_at', 'updated_at', 'deleted_at');
    }

    /**
     * get Entity Attribute Lits
     *
     * @param integer entity_type_id
     * @return NULL
     */
    function getEntityAttributeList($entity_type_id)
    {
        // init data
        $data = array();

        // validate type
        $type = trim($entity_type_id);

        // fetch
        $q = "SELECT
                                 sea.`attribute_id` AS attribute_id , sea.`attribute_set_id`  AS attribute_set_id , sea.`entity_attribute_id` AS entity_attribute_id , sea.`entity_type_id` AS entity_type_id , sa.`attribute_code` AS attribute_code ,  sdf.`identifier` AS identifier , sa.`backend_table` AS backend_table, sa.`frontend_class` AS frontend_class, sa.`frontend_input` AS frontend_input, sa.`frontend_label` AS frontend_label, sa.`is_required` AS is_required, sas.`attribute_set_name` AS attribute_set_name, eet.`title` AS entity_title, GROUP_CONCAT(
                                            CONCAT(
                                                sao.`attribute_option_id`, ':', sao.`value`
                                            )
                                        ) AS `options` , `model` FROM (((((`sys_entity_attribute` `sea`
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
                                     WHERE sea.`entity_type_id`  = " . $type . "
                                GROUP BY `sa`.`attribute_id`";
        // $data = $this->cacheQuery($q, 60);

        $data = \DB::select($q);

        // return
        return $data;
    }


    /**
     * get Entity Attribute Lits
     *
     * @param integer entity_type_id
     * @return NULL
     */
    function getEntityAttributeValidationList($entity_type_id, $params = '')
    {
        // init data
        $data = array();

        $show_in_list = isset($params['show_in_list']) ? ' AND sa.show_in_list = 1 ' : '';
        $show_in_search = isset($params['show_in_search']) ? ' AND sa.show_in_search = 1 ' : '';

        // validate type
        $type = trim($entity_type_id);
        // fetch
        $q = "SELECT
                                      sdf.flat_table_type, sa.use_entity_type, sa.entity_type_id AS linked_entity_type_id, sa.`data_type_id`,
                                      sa.`entity_type_id`, sa.`attribute_id`, sa.`attribute_code` AS attribute_code, sdf.`identifier`, CONCAT(
                                        IF(
                                          sea.`is_required` = 1, 'required|', ''
                                        ), sdf.`validation_type`, IF(
                                          GROUP_CONCAT(sao.`attribute_option_id`) IS NOT NULL, CONCAT('|in:', GROUP_CONCAT(sao.`value`)), ''
                                        ), IF(
                                          sea.`is_unique` = 1, CONCAT(
                                            '|unique:', sdf.`model`, ',', 'value,NULL,', 'entity_type_id,', 'entity_type_id,', '" . $type . ",deleted_at,NULL'
                                          ), ''
                                        )
                                      ) AS validation ,sea.`view_at` , sea.`is_read_only`, sea.`placeholder`,  sea.`default_value`,sea.`show_in_list`, sea.`show_in_search`, sea.`validation` AS js_validation_tags , sdf.php_data_type , sdf.type as attribute_data_type , searchable_type,
                                      sa.`frontend_label` AS frontend_label,  sea.`frontend_label` AS entity_frontend_label
                                FROM
                                  `sys_entity_attribute` `sea`
                                  LEFT JOIN `sys_attribute` `sa`
                                    ON `sa`.`attribute_id` = `sea`.`attribute_id`
                                  LEFT JOIN `sys_attribute_set` `sas`
                                    ON `sas`.`attribute_set_id` = `sea`.`attribute_set_id`
                                  LEFT JOIN `sys_entity_type` `eet`
                                    ON `eet`.`entity_type_id` = `sea`.`entity_type_id`
                                  LEFT JOIN `sys_attribute_option` `sao`
                                    ON `sao`.`attribute_id` = `sa`.`attribute_id`
                                  LEFT JOIN `sys_data_type` `sdf`
                                    ON sdf.data_type_id = `sa`.`data_type_id`
                                     WHERE sea.`entity_type_id`  = " . $type . $show_in_search . $show_in_list . "
                                GROUP BY `sea`.`sort_order`";
        //$data = $this->cacheQuery($q, 60);

        $data = \DB::select($q);

        // return
        return $data;
    }

    /**
     * get Entity Attribute Lits
     *
     * @param integer entity_type_id
     * @return NULL
     */
    function getEntityAttributeValidationListForUpdate($entity_type_id, $entity_id, $params = '')
    {
        // init data
        $data = array();

        $show_in_list = isset($params['show_in_list']) ? ' AND sa.show_in_list = 1 ' : '';
        $show_in_search = isset($params['show_in_search']) ? ' AND sa.show_in_search = 1 ' : '';

        // validate type
        $type = trim($entity_type_id);
        // fetch
        $q = "SELECT
                                      sdf.flat_table_type, sa.use_entity_type, sa.entity_type_id AS linked_entity_type_id, sa.`data_type_id`,
                                      sa.`entity_type_id`, sa.`attribute_id`, sa.`attribute_code` AS attribute_code, sdf.`identifier`,
                                      CONCAT(
                                        IF(
                                          sea.`is_required` = 1, 'required|', ''
                                        ), sdf.`validation_type`, IF(
                                          GROUP_CONCAT(sao.`attribute_option_id`) IS NOT NULL, CONCAT('|in:', GROUP_CONCAT(sao.`value`)), ''
                                        ) , IF( sea.`is_unique` = 1 ,CONCAT('|unique:' , sdf.`model` ,',','value,NULL,', REPLACE(sdf.`model`,'sys_',''),'_id,', REPLACE(sdf.`model`,'sys_','' ),'_id,'," . $entity_id . " ),  '' )
                                      ) AS validation ,sea.`view_at` , sea.`is_read_only`,sea.`default_value`, sea.`placeholder`, sea.`show_in_list`, sea.`show_in_search`, sea.`validation` AS js_validation_tags , searchable_type,
                                       sa.`frontend_label` AS frontend_label,  sea.`frontend_label` AS entity_frontend_label
                                FROM
                                  `sys_entity_attribute` `sea`
                                  LEFT JOIN `sys_attribute` `sa`
                                    ON `sa`.`attribute_id` = `sea`.`attribute_id`
                                  LEFT JOIN `sys_attribute_set` `sas`
                                    ON `sas`.`attribute_set_id` = `sea`.`attribute_set_id`
                                  LEFT JOIN `sys_entity_type` `eet`
                                    ON `eet`.`entity_type_id` = `sea`.`entity_type_id`
                                  LEFT JOIN `sys_attribute_option` `sao`
                                    ON `sao`.`attribute_id` = `sa`.`attribute_id`
                                  LEFT JOIN `sys_data_type` `sdf`
                                    ON sdf.data_type_id = `sa`.`data_type_id`
                                     WHERE sea.`entity_type_id`  = " . $type . $show_in_search . $show_in_list . "
                                GROUP BY `sea`.`sort_order`";
        // $data = $this->cacheQuery($q, 60);

        $data = \DB::select($q);

        // return
        return $data;
    }

    /**
     * get Entity Attribute Lits
     *
     * @param integer entity_type_id
     * @return NULL
     */
    function getEntityAttributeFields($entity_type_id)
    {
        // init data
        $data = array();

        // validate type
        $type = trim($entity_type_id);

        $q = "SELECT  sa.`attribute_id`, " . $type . " AS  entity_type_id, sa.`attribute_code`, sdf.`model`, sea.`attribute_set_id` , sdf.`php_data_type`, sea.default_value
                                    FROM
                                      sys_attribute sa
                                      LEFT JOIN sys_entity_attribute sea
                                        ON sea.`attribute_id` = sa.`attribute_id`
                                      LEFT JOIN `sys_entity_type` `eet`
                                        ON `eet`.`entity_type_id` = `sea`.`entity_type_id`
                                      LEFT JOIN `sys_data_type` sdf
                                        ON sdf.`data_type_id` = sa.`data_type_id`
                                    WHERE  sea.`entity_type_id` = " . $type . "
                                    UNION
                                    SELECT
                                      '0' AS attribute_id,  " . $type . " AS  entity_type_id, COLUMN_NAME AS `attribute_code`, 'sys_entity' AS model, '' AS attribute_set_id , '' AS php_data_type , '' as default_value
                                    FROM
                                      INFORMATION_SCHEMA.COLUMNS
                                    WHERE table_name = 'sys_entity'
                                      AND COLUMN_NAME IN ('entity_auth_id', 'entity_type_id', 'identifier')";
        // $data = $this->cacheQuery($q, 60);

        $data = \DB::select($q);

        // return
        return $data;
    }


    /**
     * Check load time
     *
     * @param $start
     */
    private function _loadTime($started_at)
    {
        echo 'Cool, that only took ' . (microtime(TRUE) - $started_at) . ' seconds!';
    }

    /**
     * Get entity type identifier and attribute code
     * @param $entity_type_id
     * @param $attribute_id
     * @return bool
     */
    public function getLinkedAttribute($entity_type_id,$attribute_id)
    {
        $row = \DB::select("SELECT et.identifier,a.attribute_code FROM sys_entity_type et
        LEFT JOIN sys_entity_attribute eta ON et.entity_type_id = eta.entity_type_id
        LEFT JOIN sys_attribute a ON eta.attribute_id = a.attribute_id
        WHERE et.entity_type_id = $entity_type_id AND eta.attribute_id = $attribute_id");
        return isset($row[0])?$row[0]:false;
    }

    /**
     * Get Entity type and default attribute option
     * @param $entity_id
     * @return bool
     */
    public function getEntityTypeAndDefaultAttribute($entity_id)
    {
        $row = \DB::select("SELECT e.entity_type_id,
                            et.identifier as entity_type_identifier,
                            ea.attribute_id,
                            a.attribute_code 
             FROM sys_entity e
             LEFT JOIN sys_entity_type et ON et.entity_type_id = e.entity_type_id
             LEFT JOIN sys_entity_attribute ea
             LEFT JOIN sys_attribute a ON ea.attribute_id = a.attribute_id
             ON e.entity_type_id = ea.entity_type_id
             WHERE e.entity_id = $entity_id AND ea.is_default_option = 1");

        return isset($row[0])?$row[0]:false;
    }

    /**
     * Get Entity Cities
     * @param {string} city_id
     */
    public static function getCitiesCustoemr($city_id)
    {
        $query1 = \DB::table('order_pickup_flat')
                    ->select('entity_id','city','city_name','city_code','state_name','state_code','city_lat','city_long','customer_id')
                    ->groupBy('customer_id')
                    ->whereIn('city',[$city_id])
                    ->whereNull('deleted_at');
        $query2 = \DB::table('order_dropoff_flat')
                        ->select('entity_id','city','city_name','city_code','state_name','state_code','city_lat','city_long','customer_id')
                        ->whereNull('deleted_at')
                        ->union($query1)
                        ->whereIn('city',[$city_id])
                        ->groupBy('customer_id')
                        ->get();
        return $query2;
    }
}