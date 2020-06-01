<?php namespace App\Http\Models;

use App\Libraries\CustomHelper;
use App\Libraries\EntityHelper;
use App\Libraries\System\Entity;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;
use App\Libraries\EntityTypeMask;
use Illuminate\Http\Request;
use Validator;
use App\Libraries\WalletTransaction;
// init models
//use App\Http\Models\Conf;

class SYSEntity extends Base
{

    use SoftDeletes;
    public $table = 'sys_entity';
    public $timestamps = true;
    public $primaryKey;
    private $_modelPath = "\App\Http\Models\\";
    public $_objectIdentifier = "entity";
    private $_entityTypeModel = "SYSEntityType";
    private $_PLAttachment = "PLAttachment";
    private $_SYSEntityAuth = "SYSEntityAuth";
    private $_SYSAttributeOption = "SYSAttributeOption";
    private $_SYSEntityAttribute = "SYSEntityAttribute";
    private $_SYSAttribute = "SYSAttribute";

    public $_mobile_json = false;

    protected $dates = ['deleted_at'];

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->primaryKey = 'entity_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        $this->_entityTypeModel = $this->_modelPath . $this->_entityTypeModel;
        $this->_entityTypeModel = new $this->_entityTypeModel;

        $this->_PLAttachment = $this->_modelPath . $this->_PLAttachment;
        $this->_PLAttachment = new $this->_PLAttachment;

        $this->_SYSEntityAuth = $this->_modelPath . $this->_SYSEntityAuth;
        $this->_SYSEntityAuth = new $this->_SYSEntityAuth;

        $this->_SYSAttributeOption = $this->_modelPath . $this->_SYSAttributeOption;
        $this->_SYSAttributeOption = new $this->_SYSAttributeOption;

        $this->_SYSEntityAttribute = $this->_modelPath . $this->_SYSEntityAttribute;
        $this->_SYSEntityAttribute = new $this->_SYSEntityAttribute;

        $this->_SYSAttribute = $this->_modelPath . $this->_SYSAttribute;
        $this->_SYSAttribute = new $this->_SYSAttribute;


        // set fields
        $this->__fields = array($this->primaryKey, 'entity_type_id', "entity_auth_id", 'created_at', 'updated_at', 'deleted_at');
    }

    /**
     * get Linked Entity Attribute Value
     * @param integer attribute_id,entity_id
     * @return NULL
     */
    function getLinkedEntityAttributeValue($attribute_id, $entity_id, $lang)
    {
        if(empty($entity_id) || empty($attribute_id))
            return NUll;
        // init data
        $data = array();
        $q = "SELECT
					REPLACE(
					REPLACE(
						REPLACE(
						  REPLACE(
							REPLACE(
							  REPLACE(
								sdt.`query`, 'ATTRIBUTE_ID_VALUE', ssa.`attribute_id`
							  ), 'ENTITY_ID_VALUE', " . $entity_id . "
							), 'ATTRIBUTE_CODE', CONCAT(\"'\", ssa.`attribute_code`, \"'\")
						  ), 'LINKED_ATTRIBUTE', CONCAT(
							\"'\", ssa.`linked_attribute_id`, \"'\", ''
						  )
						), 'LINKED_ENTITY_TYPE', CONCAT(
						  \"'\", ssa.linked_entity_type_id, \"'\", ''
						)
					  ), 'LANG_ID', '" . $lang . "') AS `query`
				FROM
				  `sys_attribute` sa
				  LEFT JOIN `sys_attribute` ssa
					ON ssa.`attribute_id` = sa.`linked_attribute_id`
				  LEFT JOIN sys_data_type sdt
					ON sdt.`data_type_id` = ssa.`data_type_id`
				WHERE sa.`attribute_id` = " . $attribute_id;
        //exit;
       // $data = $this->cacheQuery($q, 60);
        $data = \DB::select($q);
        if ($data[0]->query) {
            $data = \DB::select($data[0]->query);
          //  $data = $this->cacheQuery($data[0]->query, 1);
        }

        // return
        return $data;
    }

    /**
     * get Entity Attribute Values
     * @param integer entity_type_id
     * @return NULL
     */
    function getEntityAttributeValues($entity_type_id, $entity_id, $lang)
    {


        // init data
        $data = array();

        // validate type
        $type = trim($entity_type_id);

        $q = "SELECT   GROUP_CONCAT(
									REPLACE(
									REPLACE(
									  REPLACE(
										REPLACE(
										  REPLACE(
											REPLACE(
											  t2.`query`, 'ENTITY_ID_VALUE', se.`entity_id`
											), 'ATTRIBUTE_ID_VALUE', t2.attribute_id
										  ), 'ATTRIBUTE_CODE', CONCAT(\"'\", t2.attribute_code, \"'\", '')
										), 'LINKED_ATTRIBUTE', CONCAT(
										  \"'\", t2.linked_attribute_id, \"'\", ''
										)
									  ), 'LINKED_ENTITY_TYPE', CONCAT(
										\"'\", t2.linked_entity_type_id, \"'\", ''
									  )
									), 'LANG_ID', '" . $lang . "')
									 SEPARATOR ' UNION '
								  ) AS `value`
                                FROM
                                  (SELECT
                                    sea.`attribute_id` AS attribute_id, sea.`attribute_set_id` AS attribute_set_id, sea.`entity_attribute_id` AS entity_attribute_id, sea.`entity_type_id` AS entity_type_id, sa.`attribute_code` AS attribute_code, sdf.`identifier` AS identifier, sa.`backend_table` AS backend_table, sa.`frontend_class` AS frontend_class, sa.`frontend_input` AS frontend_input, sa.`frontend_label` AS frontend_label, sa.`is_required` AS is_required, eet.`title` AS entity_title, sa.`linked_entity_type_id` , sa.`linked_attribute_id`, GROUP_CONCAT(
                                      CONCAT(
                                        sao.`attribute_option_id`, ':', sao.`value`
                                      )
                                    ) AS `options`, `model`, sdf.`query`
                                  FROM
                                    `sys_entity_attribute` `sea`
                                    LEFT JOIN `sys_attribute` `sa`
                                      ON `sa`.`attribute_id` = `sea`.`attribute_id`
                                    LEFT JOIN `sys_entity_type` `eet`
                                      ON `eet`.`entity_type_id` = `sea`.`entity_type_id`
                                    LEFT JOIN `sys_attribute_option` `sao`
                                      ON `sao`.`attribute_id` = `sa`.`attribute_id`
                                    LEFT JOIN `sys_data_type` `sdf`
                                      ON sdf.data_type_id = `sa`.`data_type_id`
                                  WHERE sea.`entity_type_id` =  " . $type . "
                                  GROUP BY `sa`.`attribute_id`) AS t2
                                  LEFT JOIN `sys_entity` se
                                    ON se.`entity_type_id` = t2.entity_type_id
                                WHERE se.`entity_id` =  " . $entity_id;

        $data = \DB::select($q);
       // $data = $this->cacheQuery($q, 60);
        // return
        return $data;
    }

    /**
     * get Entity IDs by search
     * @param integer entity_type_id
     * @return NULL
     */
    function getEntityIDsBySearch($entity_type_id, $request)
    {
        // init data

        $attribute_ids = array();
        $attribute_keys = array();
        $entity_ids = array();
        $keyword = (isset($request->keyword)) ? $request->keyword : '';
        $match_case = false;
        $ex2Model = $this->_modelPath . "SYSEntityAttribute";
        $ex2Model = new $ex2Model;

        $fieldToBeValidate = $ex2Model->getEntityAttributeValidationList($request->entity_type_id, '');
        //print_r($fieldToBeValidate);exit;
        foreach ($fieldToBeValidate as $field) {
            if ($field->attribute_code !== 'entity_id') {
                if (isset($request->{$field->attribute_code})) {
                    if (trim($request->{$field->attribute_code}) != "") {
                        $attribute_ids[] = $field->attribute_id;
                        $attribute_keys[$field->attribute_code] = $request->{$field->attribute_code};
                        $match_case = true;
                    }
                }
            }
        }

        //echo '<pre>';print_r($attribute_ids);die;
        $type = trim($entity_type_id);
        if (count($attribute_ids) > 0) {
            $attribute_str = implode(", ", $attribute_ids);
            // fetch
           $qt = "SELECT `t2`.`attribute_id`, `t2`.`attribute_code`,
									  GROUP_CONCAT(
									  REPLACE(REPLACE(
									  REPLACE(
										REPLACE(
										  REPLACE(
											REPLACE(
											  t2.`query`, 'ENTITY_ID_VALUE', 'TYPE_ID'
											), 'ATTRIBUTE_ID_VALUE', t2.attribute_id
										  ), 'ATTRIBUTE_CODE', CONCAT(
											\"'\", t2.attribute_code, \"'\", ''
										  )
										), 'LINKED_ATTRIBUTE', CONCAT(
										  \"'\", t2.linked_attribute_id, \"'\", ''
										)
									  ), 'LINKED_ENTITY_TYPE', CONCAT(
										\"'\", t2.linked_entity_type_id, \"'\", ''
									  )
									), 'LANG_ID', '" . $request->_lang . "') SEPARATOR ' UNION '
									  ) AS `value`
									FROM
									  (SELECT
										sea.`attribute_id` AS attribute_id, sea.`attribute_set_id` AS attribute_set_id, sea.`entity_attribute_id` AS entity_attribute_id, sea.`entity_type_id` AS entity_type_id, sa.`attribute_code` AS attribute_code, sdf.`identifier` AS identifier, sa.`backend_table` AS backend_table, sa.`frontend_class` AS frontend_class, sa.`frontend_input` AS frontend_input, sa.`frontend_label` AS frontend_label, sa.`is_required` AS is_required, eet.`title` AS entity_title,  sa.`linked_entity_type_id` , sa.`linked_attribute_id`,  GROUP_CONCAT(
										  CONCAT(
											sao.`attribute_option_id`, ':', sao.`value`
										  )
										) AS `options`, `model`, sdf.`query` , php_data_type
									  FROM
										`sys_entity_attribute` `sea`
										LEFT JOIN `sys_attribute` `sa`
										  ON `sa`.`attribute_id` = `sea`.`attribute_id`
										LEFT JOIN `sys_entity_type` `eet`
										  ON `eet`.`entity_type_id` = `sea`.`entity_type_id`
										LEFT JOIN `sys_attribute_option` `sao`
										  ON `sao`.`attribute_id` = `sa`.`attribute_id`
										LEFT JOIN `sys_data_type` `sdf`
										  ON sdf.data_type_id = `sa`.`data_type_id`
									  WHERE sea.`entity_type_id` =  " . $type . "
									  AND `sa`.`attribute_id` IN (" . $attribute_str . ")
									  GROUP BY `sa`.`attribute_id`) AS t2";
            $q = \DB::select($qt);
           // $q = $this->cacheQuery($qt, 1);
            // if has records

            if (count($q[0]->value) > 0) {
                // get attributes
                $q[0]->value = str_replace(', GROUP_CONCAT(sei.`value`)  AS json_data ', ", ''  AS json_data ", $q[0]->value);
                $q[0]->value = str_replace('WHERE entity_id', 'WHERE entity_type_id', $q[0]->value);
                $q[0]->value = str_replace('WHERE sei.entity_id', 'WHERE sei.entity_type_id', $q[0]->value);
                //$q[0]->value = str_ireplace("SELECT",'SELECT entity_id, ',$q[0]->value);
                $q[0]->value = str_replace('TYPE_ID', $type, $q[0]->value);
                //echo '<pre>';print_r($q[0]->value);die;
                if ($match_case) {
                    $new_query = '';
                    if (count($attribute_keys) > 0) {
                        $srQueries = explode("UNION", $q[0]->value);

                        //echo '<pre>';print_r($srQueries);die;
                        foreach ($srQueries as $srQuery) {

                            foreach ($attribute_keys as $key => $attribute_val) {
                                if (stripos($srQuery, $key) !== false) {
                                    $search_keyword = trim($attribute_val);
                                    if (is_numeric($search_keyword)) {
                                        $srQuery = str_replace('WHERE ', 'WHERE sei.`value` =\'' . $search_keyword . '\' AND ', $srQuery);
                                    }else if(strpos($search_keyword, ',') !== false) {
                                        $srQuery = str_replace('WHERE ', 'WHERE FIND_IN_SET(sei.`value`,\'' . $search_keyword . '\') AND ', $srQuery);
                                    } else {
                                        $srQuery = str_replace('WHERE ', 'WHERE sei.`value` LIKE "%' . $search_keyword . '%" AND ', $srQuery);
                                    }
                                    break;
                                }
                            }
                            $new_query = ($new_query == '') ? $srQuery : "$new_query UNION $srQuery";
                        }

                        $attrs = \DB::select($new_query);
                       // $attrs = $this->cacheQuery($new_query, 1);
                        //echo '<pre>';print_r($new_query);die;
                        if (isset($attrs[0])) {
                            foreach ($attrs as $attr) {
                                $entity_ids[] = $attr->entity_id;
                            }
                        } else {
                            $entity_ids = array(0);
                        }

                    } else {
                        $entity_ids = array(0);
                    }
                } else {
                    $search_keyword = (isset($request->{$q[0]->attribute_code})) ? $request->{$q[0]->attribute_code} : $keyword;
                    if (is_numeric($search_keyword)) {
                        $q[0]->value = str_replace('WHERE ', 'WHERE sei.`value` =\'' . $search_keyword . '\' AND ', $q[0]->value);
                    } else {
                        $q[0]->value = str_replace('WHERE ', 'WHERE sei.`value` LIKE "%' . $search_keyword . '%" AND ', $q[0]->value);
                    }
                   // $attrs = $this->cacheQuery($q[0]->value, 1);
                    $attrs = \DB::select($q[0]->value);
                    if (isset($attrs[0])) {
                        foreach ($attrs as $attr) {
                            $entity_ids[] = $attr->entity_id;
                        }
                    } else {
                        $entity_ids = array(0);
                    }
                }
            }
        }

        // return
        return $entity_ids;
    }





    /**
     * get get Entity IDs By Flat
     * @param integer entity_type_id
     * @return NULL
     */
    function getEntityIDsByFlat($entityTypeData, $request, $isdelete = true, $returnQuery = false)
    {
        // init data
        $entityIds = array();
       // $keyword = (isset($request->keyword)) ? $request->keyword : '';
        $keyword = '';

        $or_fields = array();
        if (isset($request->or_fields) && !empty($request->or_fields)) {
            if(is_array($request->or_fields)){
                $or_fields = $request->or_fields;
            }else{
                $or_fields = explode(',', $request->or_fields);
            }
        }

        $range_fields=array();
        if (isset($request->range_fields) && !empty($request->range_fields)) {
            if(is_array($request->range_fields)){
                $range_fields = $request->range_fields;
            }else{
                $range_fields = explode(',', $request->range_fields);
            }
        }

        $entityTypeId = $entityTypeData->entity_type_id;
        $ex2Model = $this->_modelPath . "SYSEntityAttribute";
        $ex2Model = new $ex2Model;


        $fieldToBeValidate = $ex2Model->getEntityAttributeValidationList($entityTypeData->entity_type_id);
        $fieldToBeValidate[] = (object)array("attribute_code" => 'entity_id');
        //print_r($fieldToBeValidate); die;
        $queryOrWhere = '';
        $queryNotWhere = '';
        $queryAndWhere = '';

        foreach ($fieldToBeValidate as $field) {
            /*if (isset($request->match_not)) {
                foreach ($request->match_not as $key => $matchNot) {
                    if ($field->attribute_code == $key) {
                        $queryNotWhere = $queryNotWhere . (($queryNotWhere != "") ? " AND " : "") . " `" . $field->attribute_code . "`!=" . $matchNot;
                    }
                }
            }*/

            if (isset($request->{$field->attribute_code}) && ($request->{$field->attribute_code} != '' ||  $request->{$field->attribute_code} === 0)) {
                $searchKeyword = (isset($request->{$field->attribute_code})) ? $request->{$field->attribute_code} : $keyword;
            } else {
                $searchKeyword = $keyword;
            }


            if ($searchKeyword != '' ||  $searchKeyword === 0) {
                    $btw = false;

                    if(isset($field->php_data_type) && $field->php_data_type == 'comma_separated'){

                        $searchKeyword = explode(',', $searchKeyword);

                        if(in_array($field->attribute_code,$or_fields)) {
                            $queryOrWhere = $this->getMultiSearchQuery($queryOrWhere, $searchKeyword, $field, $request);
                        }else{
                            $queryAndWhere = $this->getMultiSearchQuery($queryAndWhere, $searchKeyword, $field, $request);
                        }

                    }else{

                        if(!is_array($searchKeyword) && strpos($searchKeyword,',')!==false){
                            $searchKeyword = explode(',', $searchKeyword);
                        }

                        if(in_array($field->attribute_code,$range_fields) && is_array($searchKeyword)) $btw = true;

                        if(in_array($field->attribute_code,$or_fields)) {
                            $queryOrWhere = $this->getSearchQuery($queryOrWhere, $searchKeyword, $field, $request, " OR ",$btw);
                        }else{
                            $queryAndWhere = $this->getSearchQuery($queryAndWhere, $searchKeyword, $field, $request," AND ",$btw);
                        }
                    }

            }

        }

        if ($isdelete) {
            $queryWhere = "deleted_at IS NULL";
        } else {
            $queryWhere = "1=1";
        }

        //Append where condition in query when list/search
        $queryWhereCondition = "";
        if(isset($request->where_condition)){
            $queryWhereCondition .= $request->where_condition;
        }

        $flatTable = $entityTypeData->identifier . '_flat';

       $query = "SELECT * FROM $flatTable WHERE $queryWhere";

        if ($queryOrWhere != '' || $queryAndWhere != '' || $queryNotWhere != '' || $queryWhereCondition!="") {
            if ($queryOrWhere != '') $query = "$query AND ($queryOrWhere) ";
            if ($queryAndWhere != '') $query = "$query AND ($queryAndWhere) ";
            if ($queryNotWhere != '') $query = "$query AND ($queryWhere) ";
            if ($queryWhereCondition != '') $query = "$query  $queryWhereCondition ";

           //echo $query; exit;
            if(!$returnQuery){
                $attrs = \DB::select($query);
                //  $attrs = $this->cacheQuery($query, 1);            //$attrs = \DB::select($query);
                if (isset($attrs[0])) {
                    foreach ($attrs as $attr) {
                        $entityIds[] = $attr->entity_id;
                    }
                } else {
                    $entityIds = array(0);
                }

            }

        }


        //if entity type is customer and business user then also search by role ids and auth columns
        if($entityTypeData->identifier == 'customer'){

            if((isset($request->email) && !empty($request->email)) ||
                (isset($request->mobile_no) && !empty($request->mobile_no))
            ){
                $entity_idds = $this->_getByAuthColumns($request,$entityTypeData,$entityIds,$returnQuery);

                if($returnQuery){
                    return $entity_idds;
                }

                if(count($entity_idds) > 0){
                    return $entity_idds;
                }
                return array(0);
            }
        }

        if($entityTypeData->identifier == 'business_user'){
            if((isset($request->email) && !empty($request->email)) ||
                (isset($request->mobile_no) && !empty($request->mobile_no)) ||
                (isset($request->role_id) && !empty($request->role_id)) ||
                (isset($request->parent_role_id) && !empty($request->parent_role_id))
            ){
                $entity_idds = $this->_getByAuthAndRoleColumns($request,$entityTypeData,$entityIds,$returnQuery);

                if($returnQuery){
                    return $entity_idds;
                }

                if(count($entity_idds) > 0){
                    return $entity_idds;
                }
                return array(0);
            }
        }

        if($returnQuery){
            return $query;
        }

        return $entityIds;
    }


    function getSearchQuery($queryWhere, $search_keyword, $field, $request, $pera = " AND ",$btw)
    {
        if($btw && is_array($search_keyword) && count($search_keyword)==2){
            return $queryWhere . (($queryWhere != "") ? $pera : ""). " `" . $field->attribute_code . "` BETWEEN $search_keyword[0] AND $search_keyword[1]";
        }else{
            if (is_array($search_keyword)) {
                $iQueryWhere = '';
                foreach ($search_keyword as $search_val) {
                    if (is_numeric($search_val)) {
                        $iQueryWhere = $iQueryWhere . (($iQueryWhere != "") ? " OR " : "") . " `" . $field->attribute_code . "`=" . $search_val;
                    } elseif (trim($search_val) != '') {
                        $iQueryWhere = $iQueryWhere . (($iQueryWhere != "") ? " OR " : "") . " `" . $field->attribute_code . "` LIKE '%" . $search_val . "%'";
                    }
                }
                if ($iQueryWhere != '' && $queryWhere != '') $queryWhere = "($iQueryWhere) AND $queryWhere";
                elseif ($iQueryWhere != '') $queryWhere = "(".$iQueryWhere.")";
                return $queryWhere;
            } else {
                if (is_numeric($search_keyword)) {
                    return $queryWhere . (($queryWhere != "") ? $pera : "") . " `" . $field->attribute_code . "`=" . $search_keyword;
                } elseif (trim($search_keyword) != '') {
                    return $queryWhere . (($queryWhere != "") ? $pera : "") . " `" . $field->attribute_code . "` LIKE '%" . trim($search_keyword) . "%'";
                }
            }
        }
        return '';
    }

    /**
     * @param $queryWhere
     * @param $search_keyword
     * @param $field
     * @param $request
     * @return string
     */
    function getMultiSearchQuery($queryWhere, $search_keyword, $field, $request)
    {
        if (is_array($search_keyword)) {
            $iQueryWhere = '';
            foreach ($search_keyword as $search_val) {

                $iQueryWhere =  $iQueryWhere . (($iQueryWhere != "") ? " OR " : "");
                $iQueryWhere .= "`" . $field->attribute_code . "` = '" . $search_val . "'";
                $iQueryWhere .= " OR `" . $field->attribute_code . "` LIKE '%," . $search_val . "%'";
                $iQueryWhere .= " OR `" . $field->attribute_code . "` LIKE '%" . $search_val . ",%'";
                $iQueryWhere .= " OR `" . $field->attribute_code . "` LIKE '%," . $search_val . ",%'";
            }
            if ($iQueryWhere != '' && $queryWhere != '') $queryWhere = "($iQueryWhere) AND $queryWhere";
            elseif ($iQueryWhere != '') $queryWhere = "(".$iQueryWhere.")";
            return $queryWhere;

        }
    }

    /**
     * mapAttributes
     *
     * @return Response
     */
    public function mapAttributes($attributes, $ext_attributes)
    {
        $merge_attributes = array();
        foreach ($ext_attributes as $ext_attribute) {
            foreach ($attributes as $attribute)
                if ($ext_attribute->attribute_id == $attribute->attribute_id) {
                    $merge_attributes[] = (object)array_merge((array)$ext_attribute, (array)$attribute);
                    break;
                }
        }
        return $merge_attributes;
    }

    public function entityCounts($entity_type_id, $fields, $returnID = false)
    {
        $entityCount = 0;

        $entityTypeData = $this->_entityTypeModel->getEntityTypeById($entity_type_id);

        if ($entityTypeData->use_flat_table == '1') {
            $attributes_search = $this->getEntityIDsByFlat($entityTypeData, $fields);
        } else {
            $attributes_search = $this->getEntityIDsBySearch($entity_type_id, $request);
        }

        if (count($attributes_search) > 0) {
            if ($attributes_search[0] != 0) {

                $query = $this->select($this->primaryKey);
                $query->whereNull("deleted_at"); // exclude deleted
                $query->where("entity_type_id", "=", $entity_type_id);
                $query->whereIn("entity_id", $attributes_search);
                $likes = $query->get();

                if ($returnID && $likes->count() > 0) {
                    $entityCount = $likes[0]->entity_id;
                } else {
                    $entityCount = $likes->count();
                }
            }
        }
        return $entityCount;

    }

    public function getListData($entity_id, $entity_type_id = '', $request = array(), $params = array())
    {
        $listOfAttributeToBeValidate = $this->_SYSEntityAttribute->getEntityAttributeValidationList($entity_type_id, $params);
        $is_mobile_json = intval($request->mobile_json) > 0 ? TRUE : FALSE;

        $exModel = $this->_modelPath . "SYSDataType";
        $exModel = new $exModel;
        if(\Request::input('_request_parameter'))
            $request_parameter  = explode(',',\Request::input('_request_parameter'));
        else
            $request_parameter =array();
        $listing = array();
        //$apiData['object_identifier'] = $identifier;
        $in_detail = \Request::input('_in_detail', 1);

        $totalRecords = 0;

        if($params['entityTypeData']->use_flat_table == '1') {

            //if custom query to get records
            if (isset($request->query)) {

                $query = $request->query;
                $queryRecords = \DB::select($request->query);
                if (isset($queryRecords[0])) $totalRecords = count($queryRecords);

                if ($params['limit'] != -1) {
                    if ($is_mobile_json) {
                        $query = "$query LIMIT " . $params['limit'];
                    } else {
                        $query = "$query LIMIT " . $params['offset'] . ',' . $params['limit'];
                    }
                }

                $raw_records = \DB::select($query);
                //echo "<pre>"; print_r($raw_records); exit;

            } else {

            $query = $this->getEntityIDsByFlat($params['entityTypeData'], $request, TRUE, TRUE);

            $queryRecords = \DB::select($query);
            if (isset($queryRecords[0])) $totalRecords = count($queryRecords);

            if ($is_mobile_json) {
                // mobile new pagination flow starts
                if ($params['offset'] > 0) {
                    $operator = strtolower($params['sorting']) == "asc" ? ">" : "<";
                    $query = "$query AND " . $this->primaryKey . " $operator " . $params['offset'];
                }
                // mobile new pagination flow ends
            }

            if (isset($params['multi_order_by'])) {
                $query = "$query ORDER BY " . $params['multi_order_by'];
            } else {
                $query = "$query ORDER BY " . $params['order_by'] . " " . strtoupper($params['sorting']);
            }

            if ($params['limit'] != -1) {
                if ($is_mobile_json) {
                    $query = "$query LIMIT " . $params['limit'];
                } else {
                    $query = "$query LIMIT " . $params['offset'] . ',' . $params['limit'];
                }
            }


            $raw_records = \DB::select($query);

        }

        } else {
            $flat_table = $params['entityTypeData']->identifier . '_flat';
            $query = $this->select('*')
                ->from($flat_table);


            //$query = $this->select($this->primaryKey);

            $query->whereNull("deleted_at"); // exclude deleted
            if (is_numeric($entity_type_id)) {
                //  $query->where("entity_type_id", "=", $entity_type_id);
            }

            if (is_numeric($entity_id)) {
                $query->where("entity_id", "=", $entity_id);
            } elseif (isset($entity_id) && !empty($entity_id)) {
                $entity_ids = explode(",", $entity_id);
                if (count($entity_ids) > 0) {
                    $query->whereIn("entity_id", $entity_ids);
                }
            }
            $attributes_search = [];
            //attributes search.. flat table // query issue OR and AND

            //if (empty($entity_id))
            if ($params['entityTypeData']->use_flat_table == '1'):
                $attributes_search = $this->getEntityIDsByFlat($params['entityTypeData'], $request);
            else:
                $attributes_search = $this->getEntityIDsBySearch($entity_type_id, $request);
            endif;
            // echo $attributes_search; exit;
            //print_r($request->all());

            if (count($attributes_search) > 0) {
                $query->whereIn("entity_id", $attributes_search);
            }

            // apply search
            //$query = $this->_search($request, $query, $params['allowed_searching']);


            // apply order
            $query->orderBy($params['order_by'], strtoupper($params['sorting']));
            $query->take($params['limit']);
            //$query->skip($offset);
            //$raw_records = $query->select(explode(",", $allowed_ordering))->get();

            if ($is_mobile_json) {
                // new pagination flow starts
                if ($params['offset'] > 0) {
                    $operator = strtolower($params['sorting']) == "asc" ? ">" : "<";
                    $query->where($this->primaryKey, $operator, $params['offset']);
                }
                // new pagination flow ends
            } else
                if ($params['limit'] > 0)
                    $query->skip($params['offset']);

            //echo $query->toSql();
            $raw_records = $query->get();
            $totalRecords = $query->count();

        }

        // get total
        $data['total_records'] = $totalRecords;


        //$offset = $offset < $total_records ? $offset : ($total_records - 1); // new pagination flow
        $data['next_offset'] = isset($params['offset']) ? $params['offset'] : ""; // - new pagination flow
        //print_r($raw_records); exit;

        $entity_lib = new Entity();
        $entityType = $params['entityTypeData'];


        $api_fields = new ApiMethodField();
        $api_column = $is_mobile_json ? true : false;


        $entity_attributes = $api_fields->getEntityAttributeList($entityType->entity_type_id,false,$api_column);
       // echo "<pre>"; print_r( $raw_records);
        // set records
        if (isset($raw_records[0])) {
            foreach ($raw_records as $raw_record) {
               // echo "<pre>"; print_r( $raw_record);
                //$record = $raw_record;
               // $record = (object)array()
              //  $record= $this->getData($raw_record->entity_id);
              $record = $entity_lib->getData($raw_record->entity_id,$request,$entityType,$entity_attributes,$raw_record);

                $data['next_offset'] = $raw_record->{$this->primaryKey}; // new pagination flow
                // init attributes
                $data['data'][] = $record;


                //  unset($entity_iid);
            }
        }

        // set pagination response

        return $data;

    }

    /**
     * Search
     * @param $query query
     * @return query
     */
    private function _search($request, $query, $allowed_searching = "")
    {
        // fix indexes
        $fix_indexes = array($this->_entityPk, "target_entity_id", "created_by");
        // search
        foreach (explode(",", $allowed_searching) as $field) {
            // if in fix indexes
            if (in_array($field, $fix_indexes)) {
                // all fix searches
                if (isset($request->{$field}) && $request->{$field} != "") {
                    $q = trim(strtolower($request->{$field}));
                    $query->where($field, '=', "$q");
                }
            } else {
                // all LIKE searches
                if (isset($request->{$field}) && $request->{$field} != "") {
                    $q = trim(strtolower($request->{$field}));
                    $query->where($field, 'like', "%$q%");
                }
            }
        }
        return $query;
    }

    /**
     * User data
     *
     * @return Response
     */
    public function getEntityData($request)
    {

        return $this->getData($request->{$this->primaryKey});
        $entity = $this->get($request->{$this->primaryKey});

        $listOfAttributeToBeValidate = $this->_SYSEntityAttribute->getEntityAttributeValidationList($request->entity_type_id, '');

        $identifier = $this->_objectIdentifier;

        if (is_numeric($request->entity_type_id)) {
            $entityTypeData = $this->_entityTypeModel->getEntityTypeById($request->entity_type_id);
            if ($request->mobile_json) $identifier = $entityTypeData->identifier;
            else $identifier = 'entity';
        }

        $EntityTypeMask = new EntityTypeMask();

        // init output data array
        $data = array();

        // get user data
        $data[$identifier] = $this->getData($entity->{$this->primaryKey}, true);
        if ($data[$identifier]->entity_auth_id != 0) {
            $data[$identifier]->auth = $this->_SYSEntityAuth->getData($data[$identifier]->entity_auth_id, true);
        }
        $data[$identifier]->entity_type_identifier = $identifier;
        if (isset($entityTypeData) && $entityTypeData->show_gallery == "1") {
            $data[$identifier]->gallery = array();
            $data[$identifier]->gallery = $this->_PLAttachment->getAttachmentByEntityID($entity->{$this->primaryKey});
        }

        $q = $this->getEntityAttributeValues($request->entity_type_id, $entity->{$this->primaryKey}, $request->_lang);
        $attrs = \DB::select($q[0]->value);
        //echo $q[0]->value;exit;
        foreach ($attrs as $field_action) {
            if ($field_action->linked_entity_type_id) {
                $temp = array();
                if ($field_action->data_type_id == 9 || $field_action->data_type_id == 5) {
                    foreach (explode(",", $field_action->json_data) as $tempList) {
                        $linkedAttribute = $this->getLinkedEntityAttributeValue($field_action->attribute_id, $tempList, 'en');
                        if (count($linkedAttribute)) {
                            $detail = $this->getData($linkedAttribute[0]->entity_id);
                            $temp[] = array('id' => $linkedAttribute[0]->entity_id, 'value' => $linkedAttribute[0]->attribute, 'detail' => $detail);
                        }
                    }
                } else {
                    $linkedAttribute = $this->getLinkedEntityAttributeValue($field_action->attribute_id, $field_action->attribute, $request->_lang);
                    if (count($linkedAttribute)) {
                        $detail = $this->getData($linkedAttribute[0]->entity_id);
                        $temp = array('id' => $linkedAttribute[0]->entity_id, 'value' => $linkedAttribute[0]->attribute, 'detail' => $detail);
                    }
                }
                if (!$this->_mobile_json)
                    $data[$identifier]->attributes[str_replace('_id', '', $field_action->name)] = $temp;
                else
                    $data[$identifier]->{str_replace('_id', '', $field_action->name)} = $temp;
            } else {
                if ($field_action->data_type_id == '6' || $field_action->data_type_id == '11' || $field_action->data_type_id == '4')
                    if (!$this->_mobile_json)
                        $data[$identifier]->attributes[$field_action->name] = $this->_SYSAttributeOption->getAttributeById($field_action->attribute_id, $field_action->attribute);
                    else
                        $data[$identifier]->{$field_action->name} = $this->_SYSAttributeOption->getAttributeById($field_action->attribute_id, $field_action->attribute);
                else
                    if (!$this->_mobile_json)
                        $data[$identifier]->attributes[$field_action->name] = $field_action->attribute;
                    else
                        $data[$identifier]->{$field_action->name} = $field_action->attribute;

            }
        }

        return $data;

        if (method_exists($EntityTypeMask, $identifier . "SystemAttributes")) {
            $data[$identifier] = call_user_func(array($EntityTypeMask, $identifier . "SystemAttributes"), $this, $data[$identifier], $entityTypeData, $request);
        }
        // return data
        return $data;

    }

    /**
     * @param $entity_auth_id
     * @param $entity_type_id
     * @return bool
     */
    public function getEntityByAuthAndEntityType($entity_auth_id, $entity_type_id)
    {
        $row = $this->select($this->primaryKey)
            ->where('entity_auth_id', '=', $entity_auth_id)
            ->where('entity_type_id', '=', $entity_type_id)
            ->whereNull("deleted_at")
            ->get();

        return isset($row[0]) ? $row[0]->{$this->primaryKey} : false;
    }


    /**
     * User data
     *
     * @return Response
     */
    public function getData($id = 0,$request_params = false)
    {
        $entity_lib = new Entity();
        return $entity_lib->getData($id,$request_params);
        // row data
        $data = $this->get($id);
        // get language from request (default : en)
        $lang = \Request::input('_lang', 'en');
        $in_detail = \Request::input('_in_detail', 1);
        if(\Request::input('_request_parameter'))
            $request_parameter  = explode(',',\Request::input('_request_parameter'));
        else
            $request_parameter =array();

        if ($data) {

            // get entity type data
            $entityType = $this->_entityTypeModel->get($data->{$this->_entityTypeModel->primaryKey});

            // attach auth data (if exists)
            if ((in_array('auth', $request_parameter) && !empty($request_parameter) ) ||  empty($request_parameter)  )
            if ($data->{$this->_SYSEntityAuth->primaryKey} > 0) {
                $data->auth = $this->_SYSEntityAuth->getData(
                    $data->{$this->_SYSEntityAuth->primaryKey},
                    $data->{$this->_entityTypeModel->primaryKey});

               /* //Get entity role and its parent id and save with auth session
                if( $data->auth){
                    $roleMapModel = new SYSEntityRoleMap();
                    $role = $roleMapModel->getRoleInfoByEntity($data->{$this->_SYSEntityAuth->primaryKey});
                    if ($role) {
                        $data->auth->role_id = $role->role_id;
                        $data->auth->parent_role_id = $role->parent_id;
                    }
                }*/

            }

            // get entity gallery (if attached)
            if ((in_array('gallery', $request_parameter) && !empty($request_parameter) ) ||  empty($request_parameter)  )
            if ($entityType->show_gallery == '1') {
                $data->gallery = array();
                $data->gallery = $this->_PLAttachment->getAttachmentByEntityID($data->{$this->primaryKey});
            }


            // if flat
            $q = $this->getEntityAttributeValues($data->entity_type_id, $data->{$this->primaryKey}, $lang);

            // if has attributes
            if (isset($q[0]) && $q[0]->value !== NULL) {
                $attrs = \DB::select($q[0]->value);

                foreach ($attrs as $field_action) {
                    if ((in_array($field_action->name, $request_parameter) && !empty($request_parameter) ) ||  empty($request_parameter)  )
                    if($field_action->attribute_id){
                        $attributeData = \DB::table('sys_attribute')->select('*')
                            ->where('attribute_id', $field_action->attribute_id)
                            ->first();
                        if ($field_action->data_type_id == 20) {

                            if ($field_action->attribute)
                                if (!$this->_mobile_json)
                                    $data->attributes[$field_action->name] = $this->getData($field_action->attribute);
                                else
                                    $data->{$field_action->name} = $this->getData($field_action->attribute);

                        }
                        else if ($field_action->linked_entity_type_id && $field_action->linked_attribute_id) {

                            $temp = array();
                            if ($field_action->data_type_id == 9 || $field_action->data_type_id == 5 ) {
                                foreach (explode(",", $field_action->json_data) as $tempList) {
                                    $linkedAttribute = $this->getLinkedEntityAttributeValue($field_action->attribute_id, $tempList, $lang);
                                    if (count($linkedAttribute)) {
                                        if($in_detail) $showDetail = $this->getData($linkedAttribute[0]->entity_id); else $showDetail = '';
                                        $temp[] = array('id' => $linkedAttribute[0]->entity_id, 'value' => $linkedAttribute[0]->attribute, 'detail' => $showDetail);
                                    }
                                }
                            }
                            else {
                                $linkedAttribute = $this->getLinkedEntityAttributeValue($field_action->attribute_id, $field_action->attribute, $lang);
                                if (count($linkedAttribute)) {
                                    if($in_detail) $showDetail = $this->getData($linkedAttribute[0]->entity_id); else $showDetail = '';
                                    $temp = array('id' => $linkedAttribute[0]->entity_id, 'value' => $linkedAttribute[0]->attribute, 'detail' => $showDetail);
                                }
                            }
                            if (!$this->_mobile_json){
                                $data->attributes[$field_action->name] = $temp;

                            }
                            else{
                                $data->{$field_action->name} = $temp;
                                //$data->{str_replace('_id', '', $field_action->name)} = $temp;

                            }

                        }
                        else if (isset($attributeData->backend_table) && $attributeData->backend_table != '') {

                            // category or role attached to any entity type
                            $temp = explode("_", $attributeData->backend_table);
                            $DymamicModel = $this->_modelPath . strtoupper($temp[0]) . ucfirst($temp[1]);
                            unset($temp);
                            $model = new $DymamicModel;
                            if ($field_action->data_type_id == 9 || $field_action->data_type_id == 5) {
                                foreach (explode(",", $field_action->json_data) as $tempList) {
                                    $temp[] = $model->getData($tempList);
                                }

                                if (!$this->_mobile_json)
                                    $data->attributes[$field_action->name] = $temp;
                                else
                                    $data->{$field_action->name} = $temp;
                            } else {
                                //print_r($field_action);
                                if ($field_action->data_type_id == '6' || $field_action->data_type_id == '11' || $field_action->data_type_id == '4')
                                    if (!$this->_mobile_json)
                                        $data->attributes[$field_action->name] = $model->getData($field_action->attribute);
                                    else
                                        $data->{$field_action->name} = $model->getData($field_action->attribute);
                            }
                        }
                        else {
                            if($field_action->linked_entity_type_id == 0 && $attributeData->backend_table == '' && $field_action->data_type_id == 5){
                                $temp = array();
                                foreach (explode(",", $field_action->json_data) as $tempList) {
                                    $temp[] = $this->_SYSAttributeOption->getAttributeById($field_action->attribute_id, $tempList);
                                }

                                if (!$this->_mobile_json)
                                    $data->attributes[$field_action->name] = $temp;
                                else
                                    $data->{$field_action->name} = $temp;

                            }

                            else if ($field_action->data_type_id == '6' || $field_action->data_type_id == '11' || $field_action->data_type_id == '4' || $field_action->data_type_id == '12') {

                                if (!$this->_mobile_json)
                                    $data->attributes[$field_action->name] = $this->_SYSAttributeOption->getAttributeById($field_action->attribute_id, $field_action->attribute);
                                else
                                    $data->{$field_action->name} = $this->_SYSAttributeOption->getAttributeById($field_action->attribute_id, $field_action->attribute);
                            } else {
                                if (!$this->_mobile_json)
                                    $data->attributes[$field_action->name] = $field_action->attribute;
                                else
                                    $data->{$field_action->name} = $field_action->attribute;
                            }

                        }

                    }
                }

                if(isset($entityType->depend_entity_type) && !empty($entityType->depend_entity_type)){

//                        $entity_data = \DB::select("SELECT identifier FROM sys_entity_type WHERE entity_type_id = ".$entityType->depend_entity_type.' limit 1');
//                        $_flatTable = $entity_data[0]->identifier.'_flat';
//                        $entity_data = \DB::select("SELECT * FROM $_flatTable WHERE cart_id =".$data->{$this->primaryKey});
//                        $data->depend_entity = $entity_data;

//                    $params = array();
//                    $params[$entityType->identifier.'_id'] = $data->{$this->primaryKey};
//                    $params['entity_type_id'] = $entityType->depend_entity_type;
//                     $request = new Request();
//                    $entity_data = CustomHelper::internalCall($request,\URL::to(DIR_API) . '/system/entities/listing', 'GET', $params, false);
//                    $data->depend_entity = $entity_data;
                };
            }

        }

        return $data;

    }

    /**
     * Incerement or decrement any entity value
     * @param $entity_id
     * @param $field
     * @param $value
     * @param $operator
     * @param bool $identifier
     */
    public function updateEntityAttributeValue($entity_id, $field, $value, $operator,$identifier = false)
    {
        DB::statement("CALL update_item_inventory($entity_id, '$field', $value,'$operator');");

        if($identifier){
            $table = $identifier."_flat";

            if (\Schema::hasTable($table)) {
              //  echo "UPDATE $table SET $field = IFNULL($field,0) $operator $value WHERE entity_id = $entity_id";
                \DB::select("UPDATE $table SET $field = IFNULL($field,0) $operator $value WHERE entity_id = $entity_id");
            }

        }
    }

    /**
     * @param $entity_auth_id
     * @param $entity_type_id
     * @return bool
     */
    public function getEntityAttributeValue($entity_id, $field)
    {
        return DB::select("CALL get_item_inventory($entity_id, '$field');");
    }

    /**
     * Update Entity Data
     * @param $entity_type_id
     * @return bool
     */
    public function saveData()
    {

    }

    /**
     * Delete all Enity attribute value map with specific entity and its attribute
     * @param $entity_id,$attribute_id,$table
     * @return bool
     */
    public function deleteAttribute($entity_id,$attribute_id,$table){
        DB::statement("CALL delete_entity_attribute_values($entity_id, $attribute_id,'".$table."')");
    }

    /**
     * @param $entity_type_id
     * @param bool $attribute_code
     * @param bool $where_column
     * @param bool $where_value
     * @return bool
     */
    public function getEntitiesListing($entity_type_id,$attribute_code = false,$where_condition = false)
    {
        $attr_code = "title";
        if($attribute_code && !empty($attribute_code)){
            $response =  $this->_SYSAttribute->getData($attribute_code);
            //$response = SYSAttribute::getLinkedAttributeCode($attribute_code);
            if($response != false && isset($response->attribute_code)){
                if(!empty($response->attribute_code))
                    $attr_code = $response->attribute_code;
            }

        }

        //Get Entity Type identifier
        $entity_type_model = new SYSEntityType();
       $entity_type_data =  $entity_type_model->getEntityTypeById($entity_type_id);
        if($entity_type_data){

            $entity_type_identifier = $entity_type_data->identifier;
            $order_item_flat = new SYSTableFlat($entity_type_identifier);

            //get where condition for status active/inactive
           $where_condition = EntityHelper::getStatusConditionByEntityType($entity_type_identifier,$where_condition);

            $result =  $order_item_flat->getDataByWhere($where_condition,array('entity_id',"$attr_code as `value`"));
        }
        //$result = DB::select("CALL get_entities_listing($entity_type_id, '$attr_code');");
        return isset($result) ? $result : false;

    }

    /**
     * Get Extra data for attributes
     * @param $entity_type_identifier
     * @param $attribute_code
     * @param $attribute_code_value
     * @return array
     */
    public function getAttributeData($entity_type_identifier,$attribute_code,$attribute_code_value){

        $return = array();
        if($entity_type_identifier == 'product'){

            if($attribute_code == "product_recipe_id"){
                if(isset($attribute_code_value) && !empty($attribute_code_value)){

                    if(\Schema::hasTable('recipe_item_flat')){
                        $entity_values = \DB::table('recipe_item_flat')->select('*')
                            ->where('recipe_id', $attribute_code_value)
                            ->get();
                        if(count($entity_values))
                            foreach($entity_values as $entity_value){
                                $recipe_item[] = $this->getData($entity_value->entity_id);
                            }

                        if(isset($recipe_item) && count($recipe_item) > 0){
                            $return['key'] = 'recipe_item';
                            $return['value'] = $recipe_item;
                        }

                    }
                }
            }
        }
        return $return;
    }

    /**
     * @param $identifier
     * @param $field_name
     * @param $entity_id
     * @return bool|object
     */
    private function _getRequestedAttributeData($identifier,$field_name,$entity_id)
    {
        $return = false;
        $entity_attribute_detail = $this->getAttributeData($identifier,$field_name,$entity_id);
        if(count($entity_attribute_detail)>0){
            if(isset($entity_attribute_detail['value'])){
                $ret = array();
                $ret['key'] = $entity_attribute_detail['key'];
                $ret['value'] = $entity_attribute_detail['value'];
                $return = (object)$ret;
                //print_r($return); exit;
            }
        }
        return $return;
    }

    /**
     * @param $request
     * @param $listOfAttributeToBeValidate
     * @return mixed
     */
    public function updateValidator($request, $listOfAttributeToBeValidate)
    {
       // print_r($listOfAttributeToBeValidate); exit;
        $apiData['kick_user'] = 0;
       // $apiData['response'] = "error";
        $apiData['error'] = 0;
        $rules = array(
            'entity_type_id' => 'integer|exists:' . $this->_entityTypeModel->table . "," . $this->_entityTypeModel->primaryKey . ",deleted_at,NULL",
            'entity_id' => 'integer|exists:' . $this->__table . "," . $this->primaryKey . ",deleted_at,NULL"
        );

        foreach ($listOfAttributeToBeValidate as $result) {
            if($result->attribute_code != "") {
                $rules[trim($result->attribute_code)] = $result->validation;
            }
        }

        $validator = Validator::make($request, $rules);
        // validator 2 for verifying correct entity_type with entity_id
        $validator2 = Validator::make($request, array(
            'entity_id' => 'required|integer|exists:' . $this->__table . ',' . $this->primaryKey . ',entity_type_id,' .$request['entity_type_id'] . ',deleted_at,NULL'
        ));
        if ($request['entity_type_id'] > 0 && $validator2->fails()) {
            $apiData['message'] = $validator->errors()->first();
            $apiData['error'] = 1;
           // return false;
        } else if ($validator->fails()) {
            $apiData['message'] = $validator->errors()->first();
            $apiData['error'] = 1;
           // return false;
        }
        return $apiData;
    }

    /**
     * @param $request_params
     * @param $listOfAttributeToBeValidate
     * @return mixed
     */
    public function addValidator($request_params, $listOfAttributeToBeValidate)
    {
        $apiData['kick_user'] = 0;
       // $apiData['response'] = "error";
        $apiData['error'] = 0;

        $rules = array(
            'entity_type_id' => 'required|integer|exists:' . $this->_entityTypeModel->table . "," . $this->_entityTypeModel->primaryKey,
        );

        $attributes_error = 1;
        if(isset($listOfAttributeToBeValidate[0])) {
            foreach ($listOfAttributeToBeValidate as $result) {
                $rules[$result->attribute_code] = $result->validation;
            }
            $attributes_error = 0;
        }


        $validator = Validator::make($request_params, $rules);

        // validate
        if ($validator->fails()) {
            $apiData['message'] = $validator->errors()->first();
            $apiData['error'] = 1;
        } else if($attributes_error > 0) {
            $apiData['message'] = "No attributes defined";
            $apiData['error'] = 1;
        }
        return $apiData;
    }

    /**
     * listing of hook entity type
     * @param $request
     * @param $hook
     * @param int $limit
     * @return array
     */
    public function listHookData($request,$hook,$limit = 5)
    {
            if($hook == 'recipe_product'){
                $search_columns['entity_type_id'] = 'product';
                $search_columns['product_type'] = 2;
                $search_columns['status'] = 1;
                $search_columns['availability'] = 1;
                $response_key = 'product';
            }
            else if($hook == 'bundle_product'){
                $search_columns['entity_type_id'] = 'product';
                $search_columns['product_type'] = 3;
                $search_columns['status'] = 1;
                $search_columns['availability'] = 1;
                $response_key = 'product';
            }
            else if($hook == 'product'){
                $search_columns['entity_type_id'] = 'product';
                $search_columns['product_type'] = 1;
                $search_columns['status'] = 1;
                $search_columns['availability'] = 1;
                $response_key = 'product';
            }
            else if($hook == 'promotion_discount'){
                $search_columns['entity_type_id'] = $response_key = $hook;
                $date = date('Y-m-d');
                $search_columns['where_condition'] = " AND start_date <= '$date' AND end_date >= '$date'";

            }
            else{
                 $search_columns['entity_type_id'] = $response_key = $hook;
            }

            if($hook == 'current_balance'){

                if(isset($request->customer_id)){

                    if(!empty($request->customer_id)){

                        $wallet_transaction = new WalletTransaction();
                        $current_balance = $wallet_transaction->getCurrentBalance($request->customer_id);

                        $data = new \StdClass();
                        $data->entity_id = $request->customer_id;
                        $data->balance = $current_balance;
                        return $data;
                    }

                }

            }else{
                $search_columns['limit'] = $limit;
                $search_columns['mobile_json'] = 1;
                $search_columns['inner_response'] = 1;
                $entity_lib = new Entity();
                $hook_data = $entity_lib->apiList($search_columns);
                $hook_data = json_decode(json_encode($hook_data));
              //  $hook_data = CustomHelper::internalCall($request,\URL::to(DIR_API) . '/system/entities/listing', 'GET', $search_columns,false);
              // echo "<pre>"; print_r( $hook_data); exit;

                $return = array();

                if (isset($hook_data->data->page->total_records) && $hook_data->data->page->total_records > 0) {
                    //print_r($hook_data->data->{$response_key}); exit;
                    if(isset($hook_data->data->{$response_key})){
                        return $hook_data->data->{$response_key};
                        // $return[$hook]['page'] = $hook_data->data->page;
                    }
                }
            }


        return  $return;
    }

    /**
     * Update entity attibute value
     * @param $entity_id
     * @param $field
     * @param $value
     * @param bool $identifier
     */
    public function updateEntityAttrValue($entity_id, $field, $value,$identifier = false)
    {
        DB::statement("CALL update_entity_attribute_value($entity_id, '$field', '$value');");

        if($identifier){
            $table = $identifier."_flat";

            if (\Schema::hasTable($table)) {
                DB::statement("UPDATE $table SET $field = '$value' WHERE entity_id = $entity_id");
            }

        }
    }

    /**
     * @param $backend_table
     * @param $backend_option
     * @param $backend_value
     * @param $where_value
     * @return bool
     */

    public static function getBackendTableValueByOption($backend_table,$backend_option,$backend_value,$where_value){

        $where_condition = ($where_value) ? " AND $backend_option = '$where_value'" : "";
        $row = \DB::select("SELECT `$backend_value` FROM $backend_table WHERE deleted_at IS NULL $where_condition");
        return isset($row[0]->{$backend_value})? $row[0]->{$backend_value}:false;
    }

    /**
     * @param $linked_entity_type_id
     * @param $linked_attribute_id
     * @param $where_value
     * @return bool
     */
    public function getLinkedAttributeEntityID($linked_entity_type_id,$linked_attribute_id = 25,$where_value)
    {
       $entity_attribute =  $this->_SYSEntityAttribute->getLinkedAttribute($linked_entity_type_id,$linked_attribute_id);

        if($entity_attribute){
            $entity_type_identifier = $entity_attribute->identifier;
            $attribute_code = ($entity_attribute->attribute_code && !empty($entity_attribute->attribute_code)) ?$entity_attribute->attribute_code : "title";

            $sys_flat_table_model = new SYSTableFlat($entity_type_identifier);
            return $sys_flat_table_model->columnValueByWhere($attribute_code,$where_value,'entity_id');
        }

        return false;
    }

    /**
     * soft Delete entity other tables
     * @param $entity_id
     * @return mixed
     */
    public function deleteEntityData($entity_id)
    {
        $deleted_at = date('Y-m-d H:i:s');
        return DB::select("CALL soft_delete_entity_data($entity_id, '$deleted_at');");
    }

    /**
     * @param $entity_auth_id
     * @return mixed
     */
    public function getEntityByAuthId($entity_auth_id)
    {
        $entity_raw = $this->getBy('entity_auth_id',$entity_auth_id);
        if(isset($entity_raw) && $entity_raw->entity_id){
            $entity_lib = new Entity();
            return $entity_lib->getData($entity_raw->entity_id,['mobile_json' => 1, 'in_detail' => 0]);
        }
    }

    /**
     * @param $request
     * @param $entity_type_id
     * @param array $entity_ids
     * @return array|string
     */
    private function _getByAuthColumns($request,$entityType,$entity_ids = array(),$returnQuery = false)
    {
        $entity = array();
        $entity_type_id = $entityType->entity_type_id;

        $where = '';
        if(isset($request->email) && !empty($request->email)){
            $where .= ' AND a.email = "'.$request->email.'"';
        }
        if(!empty($request->mobile_no) && !empty($request->mobile_no)){
            $request->mobile_no = str_replace('+','',$request->mobile_no);
            $where .=  ' AND a.mobile_no = "'.$request->mobile_no.'"';
        }

        if(!empty($where)){

            $flat_table = $entityType->identifier.'_flat';

            if($returnQuery){
                $query = "SELECT f.*";
            }
            else{
                $query = "SELECT e.entity_id";
            }

        $query .= " FROM sys_entity e
            INNER JOIN sys_entity_auth a ON e.entity_auth_id = a.entity_auth_id
            INNER JOIN $flat_table  f ON f.entity_id = e.entity_id
            WHERE f.deleted_at IS NULL $where 
            AND e.entity_type_id = $entity_type_id";

        if(count($entity_ids) > 0){
            $entity_ids = implode(',',$entity_ids);
            $query .= " AND e.entity_id IN ($entity_ids)";
        }

            if($returnQuery){ //die($query);
                return $query;
            }

            $row = \DB::select($query);
            if(isset($row[0])){

                foreach($row as $records){
                    $entity[] = $records->entity_id;
                }
            }

            return $entity;
        }

        return $entity_ids;

    }

    /**
     * @param $request
     * @param $entity_type_id
     * @param array $entity_ids
     * @return array|string
     */
    private function _getByAuthAndRoleColumns($request,$entityType,$entity_ids = array(),$returnQuery = false)
    {
        $entity = array();
        $entity_type_id = $entityType->entity_type_id;
        $where = '';
        if(isset($request->email) && !empty($request->email)){
            $where .= ' AND a.email = "'.$request->email.'"';
        }
        if(!empty($request->mobile_no) && !empty($request->mobile_no)){
            $request->mobile_no = str_replace('+','',$request->mobile_no);
            $where .=  ' AND a.mobile_no = "'.$request->mobile_no.'"';
        }

        if(isset($request->role_id) && !empty($request->role_id)){
            $where .= " AND er.role_id = $request->role_id";
           // $where .= " AND r.role_id = $request->role_id";
        }

        if($returnQuery){
            $query = "SELECT f.*";
        }
        else{
            $query = "SELECT e.entity_id";
        }

        $flat_table = $entityType->identifier.'_flat';

        $query .= " FROM sys_entity e
            INNER JOIN sys_entity_auth a ON e.entity_auth_id = a.entity_auth_id
            INNER JOIN $flat_table  f ON f.entity_id = e.entity_id
            INNER JOIN sys_entity_role er ON er.entity_id = e.entity_id";

        if(isset($request->parent_role_id) && !empty($request->parent_role_id)){
            $query .= " INNER JOIN sys_role r ON er.role_id = r.role_id";
            $where .= " AND r.parent_id = $request->parent_role_id";
        }


        if(!empty($where)){

          /*  $query .= " WHERE f.deleted_at IS NULL $where
                    AND e.entity_type_id = $entity_type_id";*/

            $query .= " WHERE f.deleted_at IS NULL $where 
                    AND e.entity_type_id = $entity_type_id";

            if(count($entity_ids) > 0){
                $entity_ids = implode(',',$entity_ids);
                $query .= " AND e.entity_id IN ($entity_ids)";
            }

            if($returnQuery){ //die($query);
                return $query;
            }

         // echo $query; exit;
            $row = \DB::select($query);
            if(isset($row[0])){

                foreach($row as $records){
                    $entity[] = $records->entity_id;
                }
            }

            return $entity;
        }

       return $entity_ids;
    }

}