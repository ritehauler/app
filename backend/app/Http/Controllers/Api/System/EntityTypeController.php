<?php
namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Controller;
use App\Http\Models\SYSEntityAttribute;
use App\Http\Models\SYSEntityRoleMap;
use App\Http\Models\SYSEntityType;
use App\Http\Models\SYSModule;
use App\Http\Models\SYSRole;
use App\Libraries\CustomHelper;
use Illuminate\Http\Request;
use Validator;
use View;

// load models

//use Twilio;

class EntityTypeController extends Controller
{

    private $_apiData = array();
    private $_layout = "";
    private $_models = array();
    private $_jsonData = array();
    private $_model_path = "\App\Http\Models\\";
    private $_object_identifier = "sys_entity_type";
    private $_sys_entity_type_identifier = "sys_entity_type"; // usually routes path
    private $_sys_entity_type_pk = "entity_type_id";
    private $_sys_entity_type_ucfirst = "EntityType";

    /**
     * Model set
     */
    private

        /** Entity Type
         *
         * @var SYSEntityType
         */
        $_eTypeModel,

        /**
         * Role
         *
         * @var SYSRole
         */
        $_roleModel,

        /**
         * Entity Role Map
         *
         * @var SYSEntityRoleMap
         */
        $_roleEntityMapModel,

        /** Attribute
         *
         * @var SYSEntityAttribute
         */
        $_entityAttributeModel;

    private $_plugin_config = array();


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        // load sys_entity_type model
        $this->_eTypeModel = new SYSEntityType();
        $this->_roleModel = new SYSRole();
        $this->_roleEntityMapModel = new SYSEntityRoleMap();
        $this->_entityAttributeModel = new SYSEntityAttribute();

        // error response by default
        $this->_apiData['kick_user'] = 0;
        $this->_apiData['response'] = "error";

    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function index(Request $request)
    {

    }


    /**
     * Create
     *
     * @return Response
     */
    public function post(Request $request)
    {

        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));

        // validations
        $rules = array(
            'title' => 'required|string',
            'identifier' => "required|string|unique:" . $this->_eTypeModel->table . ",identifier,NULL,deleted_at",
            'allow_auth' => 'required|integer',
            'allow_backend_auth' => 'required|integer',
            'show_in_menu' => 'required|integer',
            'use_flat_table' => 'required|integer',
            'has_subscription' => 'integer',
            'subscription_entity_type_id' => 'integer',
            'depend_entity_type' => 'exists:' . $this->_eTypeModel->table . "," . $this->_eTypeModel->primaryKey . ",deleted_at,NULL",
        );
        $validator = Validator::make($request->all(), $rules);


        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // init sys_entity_type
            $sys_entity_type = array();
            // map data
            foreach ($rules as $key => $val) {
                $sys_entity_type[ $key ] = $request->input($key, "");
            }

            if (empty($sys_entity_type["use_flat_table"])) $sys_entity_type["use_flat_table"] = "0";
            // other data
            $sys_entity_type["created_at"] = date("Y-m-d H:i:s");

            $sys_entity_type_id = $this->_eTypeModel->put($sys_entity_type);

            // if allow auth or allow backend auth yes

            if ($request->allow_auth == 1 || $request->allow_backend_auth == 1) {
                $getRoles = $this->_roleModel->where("slug", "=", $request->identifier)->count();
                if ($getRoles > 0) {
                    $getRoles = $this->_roleModel->where("slug", "=", $request->identifier)->first();
                    $role_id = $getRoles->role_id;
                    // $this->_roleEntityMapModel->InsertRoleEntity($role_id , $sys_entity_type_id , 0);
                } else {
                    $roleData['entity_type_id'] = $sys_entity_type_id;
                    $roleData['title'] = $request->title;
                    $roleData['slug'] = $request->identifier;
                    $roleData['is_default'] = 1;
                    $roleData['created_at'] = date("Y-m-d H:i:s");
                    $role_id = $this->_roleModel->insertGetId($roleData);
                    //  $this->_roleEntityMapModel->InsertRoleEntity($role_id , $sys_entity_type_id , 1);
                }
            }
            // response data
            $data[ $this->_object_identifier ] = $this->_eTypeModel->getData($sys_entity_type_id);

            if ($request->show_in_menu == "1") {
                $menu = new SYSModule();
                $sys_menu["created_at"] = date("Y-m-d H:i:s");
                $sys_menu["is_active"] = "1";
                $sys_menu["entity_type_id"] = $sys_entity_type_id;
                $sys_menu["title"] = $request->title;
                $sys_menu["slug"] = $request->identifier;
                $menu->insert($sys_menu);
            }
            if ($sys_entity_type["use_flat_table"] == "1") {
                $this->_eTypeModel->createTable($data[ $this->_object_identifier ]->identifier . "_flat");
            }
            $this->_apiData['message'] = trans('api_errors.success');

            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * Update
     *
     * @return Response
     */
    public function update(Request $request)
    {
        // trim/escape all
        /*$request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));*/

        // validations
        $rules = array(
            'entity_type_id' => 'required|integer',
            'title' => 'required|string',
            'allow_auth' => 'required|integer',
            'allow_backend_auth' => 'required|integer',
            'show_in_menu' => 'required|integer',
            'use_flat_table' => 'required|integer',
            'depend_entity_type' => 'exists:' . $this->_eTypeModel->table . "," . $this->_eTypeModel->primaryKey . ",deleted_at,NULL",

        );
        $validator = Validator::make($request->all(), $rules);

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // init sys_entity_type
            $sys_entity_type = array();
            // map data
            foreach ($rules as $key => $val) {
                $sys_entity_type[ $key ] = $request->input($key);
            }

            $menu = new SYSModule();
            $sys_menu["updated_at"] = date("Y-m-d H:i:s");
            $sys_menu["is_active"] = ($request->show_in_menu == "1") ? "1" : 0;
            $sys_menu["entity_type_id"] = $request->entity_type_id;
            $sys_menu["title"] = $request->title;
            $sys_menu["slug"] = $request->identifier;


            $menu_data = $menu
                ->where("entity_type_id", "=", $request->entity_type_id)
                ->whereNull("deleted_at")
                ->first();

            if ($menu_data) {
                $menu
                    ->where("entity_type_id", "=", $request->entity_type_id)
                    ->update($sys_menu);
            } elseif ($request->show_in_menu == "1") {
                $menu->insert($sys_menu);
            }
            $insertdata = 0;
            $r = $request->all();
            $request_params["entity_type_id"] = $request->entity_type_id;
            $request_params['limit'] = 100000;

            if ($request->use_flat_table)
                $response = CustomHelper::internalCall($request, "api/system/entities/listing", 'GET', $request_params, FALSE);
            $request->replace($r);

            // other data
            $sys_entity_type["updated_at"] = date("Y-m-d H:i:s");

            $sys_entity_type_id = $this->_eTypeModel->set($sys_entity_type[ $this->_sys_entity_type_pk ], $sys_entity_type);
            // response data
            $data[ $this->_object_identifier ] = $this->_eTypeModel->getData($sys_entity_type[ $this->_sys_entity_type_pk ]);
            //print_r($request->all());exit;
            if (isset($request->use_flat_table) && $request->use_flat_table == 1) {

                // Creating flat table
                $table_name = $data[ $this->_object_identifier ]->identifier . "_flat";
                if (!$this->is_table_exist($table_name)) {
                    $this->create_table($table_name);
                    $insertdata = 1;
                }
                $ex2Model = $this->_model_path . "SYSEntityAttribute";
                $ex2Model = new $ex2Model;

                $attributeFields = $ex2Model->getEntityAttributeValidationList($request->entity_type_id);

                if ($attributeFields) {
                    foreach ($attributeFields as $attField) {
                        if (!$this->is_column_exist($table_name, $attField->attribute_code)) {
                            $this->create_column($table_name, $attField->attribute_code, $attField->flat_table_type);
                        }
                    }
                }

            } else {
                $table_name = $data[ $this->_object_identifier ]->identifier . "_flat";
                if ($this->is_table_exist($table_name)) {
                    $data = \DB::select("DROP TABLE " . $table_name);
                }


            }

            if ($insertdata)
                if (isset($response->data->entity_listing))
                    foreach ($response->data->entity_listing as $entity) {
                        $flat_entity = array('entity_id' => $entity->entity_id,
                            'created_at' => $entity->created_at,
                            'updated_at' => $entity->updated_at,
                            'deleted_at' => $entity->deleted_at,
                        );

                        if (isset($entity->attributes)) {
                            foreach ($entity->attributes as $key => $value) {

                                $flat_fields[] = $key;
                                if (is_array($value)) {
                                    $string = '';
                                    foreach ($value as $list) {
                                        if (isset($list->category_id))
                                            $string .= $list->category_id . ',';
                                        else if (isset($list->$key))
                                            $string .= $list->$key . ',';
                                    }
                                    $flat_entity[ $key ] = rtrim($string, ',');
                                } elseif (is_object($value)) {
                                    if (isset($value->category_id))
                                        $flat_entity[ $key ] = $value->category_id;
                                    else if (isset($value->id))
                                        $flat_entity[ $key ] = $value->id;
                                    else
                                        $flat_entity[ $key ] = $value->value;
                                } else
                                    $flat_entity[ $key ] = $value;

                            }
                        }
                        $SYSTableFlatModel = $this->_modelPath . "SYSTableFlat";
                        $SYSTableFlatModel = new $SYSTableFlatModel($request->identifier);
                        $SYSTableFlatModel->__fields = $flat_fields;
                        $SYSTableFlatModel->put($flat_entity);
                    }

            $this->_apiData['message'] = trans('api_errors.success');

            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }


    /**
     * @param $table_name
     */
    public function create_table($table_name)
    {
        $data = \DB::select("CREATE TABLE " . $table_name . "(
					id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
					entity_id bigint(20) unsigned NOT NULL,
					updated_at datetime NULL,
					deleted_at datetime NULL,
					created_at TIMESTAMP,
					KEY `entity_id` (`entity_id`),
					KEY `deleted_at` (`deleted_at`)
					)");
    }


    /**
     * @param $table_name
     * @param $column_name
     * @param string $type
     * @return bool
     */
    private function create_column($table_name, $column_name, $type = "text NULL")
    {
        if (!$this->is_column_exist($table_name, $column_name)) {
            $data = \DB::select("ALTER TABLE $table_name ADD `$column_name` $type");

            return $data;
        }

        return FALSE;
    }


    /**
     * @param $table_name
     * @param $column_name
     * @return bool
     */
    public function is_column_exist($table_name, $column_name)
    {
        $where = "table_schema='" . MASTER_DB_NAME . "' AND TABLE_NAME='$table_name' AND COLUMN_NAME LIKE '$column_name'";
        $columns = "column_name,data_type,column_key";
        $order_by = "ORDER BY column_name asc";
        $column = \DB::select("SELECT $columns FROM INFORMATION_SCHEMA.COLUMNS WHERE $where $order_by");
        if (!empty($column)) {
            return $column[0]->column_name;
        }

        return FALSE;
    }

    /**
     * @param $table_name
     * @return bool
     */
    public function is_table_exist($table_name)
    {
        $where = "table_schema='" . MASTER_DB_NAME . "' AND TABLE_NAME='$table_name'";
        $column = \DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE $where");
        if (!empty($column)) {
            return $column[0]->TABLE_NAME;
        }

        return FALSE;
    }

    /**
     * check Existance
     *
     * @return Response
     */
    public function checkExist(Request $request)
    {
        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));

        $identity = trim(str_replace(" ", "", strip_tags($request->input('email', ''))));

        // if found "@" sign
        if (config('pl_' . $this->_entity_identifier . '.SMS_SIGNUP_ENABLED') && !preg_match("/@/", $identity)) {
            // validations
            $validator = Validator::make($request->all(), array(
                'email' => "required"
            ));
            // check exists
            $check_exists = $this->_entity_model
                ->select($this->_entity_pk)
                ->where("mobile_no", "=", $request->email)
                ->whereNull("deleted_at")
                ->get();
            // get entity
            $entity = isset($check_exists[0]) ? $this->_entity_model->getData($check_exists[0]->{$this->_entity_pk}) : FALSE;

        } else {
            // validations
            $validator = Validator::make($request->all(), array(
                'email' => "required|email"
            ));
            // check exists
            $check_exists = $this->_entity_model
                ->select($this->_entity_pk)
                ->where("email", "=", $request->email)
                ->whereNull("deleted_at")
                ->get();
            // get entity
            $entity = isset($check_exists[0]) ? $this->_entity_model->getData($check_exists[0]->{$this->_entity_pk}) : FALSE;
        }


        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } elseif (!$entity) {
            // message
            $this->_apiData['message'] = trans('api_errors.invalid_entity_request', array("entity" => $this->_object_identifier));
        } else {
            // success response
            $this->_apiData['response'] = "success";
            $this->_apiData['message'] = trans('api_errors.success');

            // init output data array
            $this->_apiData['data'] = $data = array();

            $data[ $this->_object_identifier ] = $entity;


            // overrite message
            $this->_apiData['message'] = trans('api_errors.entity_already_exists', array("entity" => $this->_object_identifier));

            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * User data
     *
     * @return Response
     */
    public function get(Request $request)
    {
        // trim/escape all
        // $request->merge(array_map('strip_tags', $request->all()));
        // $request->merge(array_map('trim', $request->all()));

        // default method required param
        $request->{$this->_sys_entity_type_pk} = intval($request->input($this->_sys_entity_type_pk, 0));

        // get data
        $sys_entity_type = $this->_eTypeModel->get($request->{$this->_sys_entity_type_pk});
        // validations
        /*if (!in_array("user/get", $this->_plugin_config["webservices"])) {
            $this->_apiData['message'] = 'You are not authorized to access this service.';
        } else*/
        if ($request->{$this->_sys_entity_type_pk} == 0) {
            $this->_apiData['message'] = trans('api_errors.pls_enter_sys_entity_type_id', array("sys_entity_type" => $this->_object_identifier));
        } else if ($sys_entity_type === FALSE) {
            $this->_apiData['message'] = trans('api_errors.invalid_record_request');
        } else {
            // init models
            //$this->__models['predefined_model'] = new Predefined;

            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();


            // update user data
            $this->_eTypeModel->set($sys_entity_type->{$this->_sys_entity_type_pk}, (array)$sys_entity_type);

            // get user data
            $data[ $this->_object_identifier ] = $this->_eTypeModel->getData($sys_entity_type->{$this->_sys_entity_type_pk}, TRUE);

            // message
            $this->_apiData['message'] = trans('api_errors.success');

            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * Delete
     *
     * @return Response
     */
    public function delete(Request $request)
    {
        // trim/escape all
        //$request->merge(array_map('strip_tags', $request->all()));
        //$request->merge(array_map('trim', $request->all()));

        $rules = array(
            $this->_sys_entity_type_pk => 'required|integer|exists:' . $this->_eTypeModel->table . "," . $this->_eTypeModel->primaryKey . ",deleted_at,NULL"
        );

        $validator = Validator::make($request->all(), $rules);

        // get data
        $sys_entity_type = $this->_eTypeModel
            ->where($this->_sys_entity_type_pk, "=", $request->{$this->_sys_entity_type_pk})
            ->whereNull("deleted_at")
            ->first();

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else if (!$sys_entity_type) {
            $this->_apiData['message'] = trans('api_errors.invalid_record_request');
        } else {
            // extra models
            $ex2Model = $this->_model_path . "SYSEntityAttribute";
            $ex2Model = new $ex2Model;

            $check_exists = $ex2Model
                ->where($this->_sys_entity_type_pk, "=", $request->{$this->_sys_entity_type_pk})
                ->whereNull("deleted_at")
                ->first();

            if (!$check_exists) {
                // success response
                $this->_apiData['response'] = "success";

                // init output data array
                $this->_apiData['data'] = $data = array();

                // get
                $sys_entity_type = json_decode(json_encode($sys_entity_type), TRUE);

                $this->_eTypeModel->remove($sys_entity_type[ $this->_sys_entity_type_pk ]);
                $this->_apiData['message'] = trans('api_errors.entity_type_delete_success');
                // response data
                // assign to output
                $this->_apiData['data'] = $data;
            } else {
                $this->_apiData['message'] = trans('api_errors.entity_type_in_use');
            }
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * Listin / Search
     *
     * @return Response
     */
    public function listing(Request $request)
    {
        // trim/escape all
        /*$request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));*/

        // override object identifier
        $this->_object_identifier = $this->_object_identifier . "_" . strtolower(__FUNCTION__);
        // allowed order
        $allowed_ordering = $allowed_searching = $this->_eTypeModel->primaryKey . ",title,attribute_code,created_at";
        $allowed_sorting = "asc,desc";


        // validations
        $rules = array(
            $this->_sys_entity_type_pk => 'integer|exists:' . $this->_eTypeModel->table . "," . $this->_eTypeModel->primaryKey . ",deleted_at,NULL",
            'title ' => "string",
            'identifier ' => "string",
            "order_by" => "in:" . $allowed_ordering,
            "sorting" => "in:" . $allowed_sorting,
            "offset" => "integer",
            "limit" => "integer"
        );
        $validator = Validator::make($request->all(), $rules);

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            $listing = array();
            // init listing object
            $data[ $this->_object_identifier ] = array();

            // sorting defaults
            $request->order_by = $request->input("order_by", "") == "" ? explode(",", $allowed_ordering)[0] : $request->order_by;
            $request->sorting = $request->input("sorting", "") == "" ? explode(",", $allowed_sorting)[0] : $request->sorting;


            $query = $this->_eTypeModel->select($this->_eTypeModel->primaryKey);
            $query->whereNull("deleted_at"); // exclude deleted
            // apply search
            $query = $this->_search($request, $query, $allowed_searching);
            // get total
            $total_records = $query->count();


            // default offset / limits
            $request->offset = 0;
            $request->limit = $total_records;
            // if need paging
            // params
            $request->limit = $request->input("limit", "") == "" ? PAGE_LIMIT_API : intval($request->input("limit", ""));
            $request->offset = intval($request->input("offset", 0));
            // offfset / limits / valid pages
            $request->offset = $request->offset < $total_records ? $request->offset : ($total_records - 1);
            $request->offset = $request->offset < 0 ? 0 : $request->offset;

            // apply order
            $query->orderBy($request->order_by, strtoupper($request->sorting));
            $query->take($request->limit);
            $query->skip($request->offset);
            //$raw_records = $query->select(explode(",", $allowed_ordering))->get();

            $raw_records = $query->get();

            // set records
            if (isset($raw_records[0])) {
                //var_dump($raw_records); exit;
                foreach ($raw_records as $raw_record) {
                    //$record = $raw_record;
                    $record = $this->_eTypeModel->getData($raw_record->{$this->_eTypeModel->primaryKey});

                    $data[ $this->_object_identifier ][] = $record;
                }
            }

            // set pagination response
            $data["page"] = array(
                "offset" => $request->offset,
                "limit" => $request->limit,
                "total_records" => $total_records,
                "next_offset" => ($request->offset + $request->limit),
                "prev_offset" => $request->offset > 0 ? ($request->offset - $request->limit) : $request->offset,
            );


            // message
            $this->_apiData['message'] = trans('api_errors.success');

            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * Search
     *
     * @param $query query
     * @return query
     */
    private function _search($request, $query, $allowed_searching = "")
    {
        // fix indexes
        $fix_indexes = array($this->_sys_entity_type_pk, "target_entity_id", "created_by");
        // search
        foreach (explode(",", $allowed_searching) as $field) {
            // if in fix indexes
            if (in_array($field, $fix_indexes)) {
                // all fix searches
                if ($request->{$field} != "") {
                    $q = trim(strtolower($request->{$field}));
                    $query->where($field, '=', "$q");
                }
            } else {
                // all LIKE searches
                if ($request->{$field} != "") {
                    $q = trim(strtolower($request->{$field}));
                    $query->where($field, 'like', "%$q%");
                }
            }
        }

        return $query;
    }


}