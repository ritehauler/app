<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use View;
use Validator;
// load models
use App\Http\Models\ApiMethod;
use App\Http\Models\ApiMethodField;
use App\Http\Models\ApiUser;
use App\Http\Models\EFEntityPlugin;
use App\Http\Models\Conf;

//use Twilio;

class EntityAttributeController extends Controller
{

    private $_apiData = array();
    private $_layout = "";
    private $_models = array();
    private $_jsonData = array();
    private $_model_path = "\App\Http\Models\\";
    private $_object_identifier = "sys_entity_attribute";
    private $_sys_entity_attribute_identifier = "system_entity_attribute"; // usually routes path
    private $_sys_entity_attribute_pk = "entity_attribute_id";
    private $_sys_entity_attribute_ucfirst = "EntityAttribute";
    private $_sys_entity_attribute_model = "SYSEntityAttribute";
    private $_sys_entity_type_model = "SYSEntityType";
    private $_plugin_config = array();
    private $sys_entity_type_pk = "entity_type_id";
    public $_config_dir = "";


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        // load sys_entity_attribute model
        $this->_sys_entity_attribute_model = $this->_model_path . $this->_sys_entity_attribute_model;
        $this->_sys_entity_attribute_model = new $this->_sys_entity_attribute_model;

        $this->_sys_entity_type_model = $this->_model_path . $this->_sys_entity_type_model;
        $this->_sys_entity_type_model = new $this->_sys_entity_type_model;
        $this->_config_dir = "panel";

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
        // extra models
        $ex1Model = $this->_model_path . "SYSEntityType";
        $ex1Model = new $ex1Model;
        // extra models
        $ex2Model = $this->_model_path . "SYSAttributeSet";
        $ex2Model = new $ex2Model;
        // extra models
        $ex3Model = $this->_model_path . "SYSAttribute";
        $ex3Model = new $ex3Model;
        // validations
        $rules = array(
            'entity_type_id' => 'required|integer|exists:' . $ex1Model->table . "," . $ex1Model->primaryKey . ",deleted_at,NULL",
            'attribute_set_id' => 'required|integer|exists:sys_attribute_set,attribute_set_id,deleted_at,NULL',
            'attribute_id' => 'required|integer|exists:' . $ex3Model->table . "," . $ex3Model->primaryKey . ",deleted_at,NULL",
            "frontend_input" => "required|string",
            "frontend_label" => "required|string",
            'is_required' => 'required|integer',
            'is_read_only' => 'required',
            //'validation' => 'required',
            //'classes' => 'required',
           // 'placeholder' => 'required',
           // 'default_value' => 'string',
            'is_unique' => 'required|integer',
            'show_in_list' => 'required|integer',
            'show_in_search' => 'required|integer',
            'sort_order' => 'required|integer',
            'view_at' => 'integer',
            'list_order' => 'integer',
            'api_column' => 'integer'

        );
        $validator = Validator::make($request->all(), $rules);

        // check combination exist
        $entity_attribute_exist = $this->_sys_entity_attribute_model
            ->where("entity_type_id", "=", $request->entity_type_id)
            ->where("attribute_id", "=", $request->attribute_id)
            ->where("attribute_set_id", "=", $request->attribute_set_id)
            ->whereNull("deleted_at")
            ->count();

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else if ($entity_attribute_exist > 0) {
            $this->_apiData['message'] = trans('api_errors.record_requested_is_already_exist');
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // init sys_entity_attribute
            $sys_entity_attribute = array();
            // map data
            foreach ($rules as $key => $val) {
                $sys_entity_attribute[$key] = $request->input($key, "");
            }

            // other data
            $sys_entity_attribute["created_at"] = date("Y-m-d H:i:s");
            if(isset($sys_entity_attribute['list_order']) && empty($sys_entity_attribute['list_order'])){
                $sys_entity_attribute['list_order'] = $sys_entity_attribute['sort_order'];
            }

            $sys_entity_type = $this->_sys_entity_type_model
                ->where($this->sys_entity_type_pk, "=", $request->input($this->sys_entity_type_pk))
                ->whereNull("deleted_at")
                ->first();

            if ($sys_entity_type->use_flat_table == "1") {
                $entity_attribute = $ex3Model->getData($sys_entity_attribute['attribute_id']);

                if ($entity_attribute) {

                    $ex4Model = $this->_model_path . "SYSDataType";
                    $ex4Model = new $ex4Model;

                    $data_type = $ex4Model->getData($entity_attribute->data_type_id);
                    $type = "text NULL";
                    if ($data_type) $type = $data_type->flat_table_type;
                    $this->create_column($sys_entity_type->identifier . "_flat", $entity_attribute->attribute_code, $type);
                }
            }
            $sys_entity_attribute_id = $this->_sys_entity_attribute_model->put($sys_entity_attribute);

            // response data
            $data[$this->_object_identifier] = $this->_sys_entity_attribute_model->getData($sys_entity_attribute_id);

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
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));
        // extra models
        $ex1Model = $this->_model_path . "SYSEntityType";
        $ex1Model = new $ex1Model;
        // extra models
        $ex2Model = $this->_model_path . "SYSAttributeSet";
        $ex2Model = new $ex2Model;
        // extra models
        $ex3Model = $this->_model_path . "SYSAttribute";
        $ex3Model = new $ex3Model;
        $ex4Model = $this->_model_path . "SYSEntityAttribute";
        $ex4Model = new $ex4Model;
        // validations
        $rules = array(
            'entity_attribute_id' => 'required|integer|exists:' . $ex4Model->table . "," . $ex4Model->primaryKey . ",deleted_at,NULL",
            'entity_type_id' => 'required|integer|exists:' . $ex1Model->table . "," . $ex1Model->primaryKey . ",deleted_at,NULL",
            'attribute_id' => 'required|integer|exists:' . $ex3Model->table . "," . $ex3Model->primaryKey . ",deleted_at,NULL",
            'attribute_set_id' => 'required|integer|exists:sys_attribute_set,attribute_set_id,deleted_at,NULL',
            "frontend_input" => "string",
            "frontend_label" => "string",
            'is_required' => 'required|integer',
            'is_read_only' => 'required|integer',
            'validation' => 'string',
            'classes' => 'string',
            'placeholder' => 'string',
            'default_value' => 'string',
            'is_unique' => 'required|integer',
            'show_in_list' => 'required|integer',
            'show_in_search' => 'required|integer',
            'sort_order' => 'required|integer',
            'view_at' => 'integer',
            'api_column' => 'integer'
        );

        $validator = Validator::make($request->all(), $rules);

        // check combination exist
        $entity_attribute_exist = $this->_sys_entity_attribute_model
            ->where("entity_attribute_id", "=", $request->entity_attribute_id)
            ->whereNull("deleted_at")
            ->count();

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else if ($entity_attribute_exist == 0) {
            $this->_apiData['message'] = trans('api_errors.entity_attribute_record_not_exist');
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // init sys_entity_attribute
            $sys_entity_attribute = array();
            // map data
            foreach ($rules as $key => $val) {
                $sys_entity_attribute[$key] = $request->input($key, "");
            }

            // other data
            $sys_entity_attribute["updated_at"] = date("Y-m-d H:i:s");

            $sys_entity_attribute_id = $this->_sys_entity_attribute_model->set($request->entity_attribute_id, $sys_entity_attribute);

            // response data
            $data[$this->_object_identifier] = $this->_sys_entity_attribute_model->getData($sys_entity_attribute_id);

            $this->_apiData['message'] = trans('api_errors.success');

            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }

    private function create_column($table_name, $column_name, $type = "text NULL")
    {
        if (!$this->is_column_exist($table_name, $column_name)) {
            $data = \DB::select("ALTER TABLE $table_name ADD `$column_name` $type");
            return $data;
        }
        return false;
    }

    public function is_column_exist($table_name, $column_name)
    {
        $where = "table_schema='" . MASTER_DB_NAME . "' AND TABLE_NAME='$table_name' AND COLUMN_NAME LIKE '%$column_name%'";
        $columns = "column_name,data_type,column_key";
        $order_by = "ORDER BY column_name asc";
        $column = \DB::select("SELECT $columns FROM INFORMATION_SCHEMA.COLUMNS WHERE $where $order_by");
        if (!empty($column)) {
            return $column[0]->column_name;
        }
        return false;
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
        if (config($this->_config_dir . '.SMS_SIGNUP_ENABLED') && !preg_match("/@/", $identity)) {
            // validations
            $validator = Validator::make($request->all(), array(
                'email' => "required"
            ));
            // check exists
            $check_exists = $this->_entity_model
                ->select($this->_sys_entity_attribute_pk)
                ->where("mobile_no", "=", $request->email)
                ->whereNull("deleted_at")
                ->get();
            // get entity
            $entity = isset($check_exists[0]) ? $this->_entity_model->getData($check_exists[0]->{$this->_sys_entity_attribute_pk}) : FALSE;

        } else {
            // validations
            $validator = Validator::make($request->all(), array(
                'email' => "required|email"
            ));
            // check exists
            $check_exists = $this->_entity_model
                ->select($this->_sys_entity_attribute_pk)
                ->where("email", "=", $request->email)
                ->whereNull("deleted_at")
                ->get();
            // get entity
            $entity = isset($check_exists[0]) ? $this->_entity_model->getData($check_exists[0]->{$this->_sys_entity_attribute_pk}) : FALSE;
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

            $data[$this->_object_identifier] = $entity;


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
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));

        // default method required param
        $request->entity_type_id = intval($request->input('entity_type_id', 0));

        // validations
        /*if (!in_array("user/get", $this->_plugin_config["webservices"])) {
            $this->_apiData['message'] = 'You are not authorized to access this service.';
        } else*/
        if ($request->entity_type_id == 0) {
            $this->_apiData['message'] = trans('api_errors.pls_enter_entity_type_id', array("sys_entity_attribute" => $this->_object_identifier));
        } else {
            // init models
            //$this->__models['predefined_model'] = new Predefined;

            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // update user data
            //$this->_sys_entity_attribute_model->set($sys_entity_attribute->{$this->_sys_entity_attribute_pk}, (array)$sys_entity_attribute);

            // get user data
            $data[$this->_object_identifier] = $this->_sys_entity_attribute_model->getEntityAttributeList($request->entity_type_id);

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
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));

        $rules = array(
            $this->_sys_entity_attribute_pk => 'required|integer|exists:' . $this->_sys_entity_attribute_model->table . "," . $this->_sys_entity_attribute_model->primaryKey . ",deleted_at,NULL"
        );

        $validator = Validator::make($request->all(), $rules);

        // get data
        $sys_entity_attribute = $this->_sys_entity_attribute_model
            ->where($this->_sys_entity_attribute_pk, "=", $request->{$this->_sys_entity_attribute_pk})
            ->whereNull("deleted_at")
            ->first();

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else if (!$sys_entity_attribute) {
            $this->_apiData['message'] = trans('api_errors.invalid_record_request');
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // get
            $sys_entity_attribute = json_decode(json_encode($sys_entity_attribute), true);

            // to-do
            // delete dependencies first
            $this->_sys_entity_attribute_model->remove($sys_entity_attribute[$this->_sys_entity_attribute_pk]);
            $this->_apiData['message'] = trans('api_errors.entity_attribute_delete_success');
            // response data
            // assign to output
            $this->_apiData['data'] = $data;
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
        // extra models
        $ex1Model = $this->_model_path . "SYSEntityType";
        $ex1Model = new $ex1Model;
        // extra models
        $ex2Model = $this->_model_path . "SYSAttributeSet";
        $ex2Model = new $ex2Model;
        // extra models
        $ex3Model = $this->_model_path . "SYSAttribute";
        $ex3Model = new $ex3Model;
        // allowed order
        $allowed_ordering = $allowed_searching = $this->_sys_entity_attribute_model->primaryKey . ",attribute_code,created_at";
        $allowed_sorting = "asc,desc";


        // validations
        $rules = array(
            $this->_sys_entity_attribute_pk => 'integer|exists:' . $this->_sys_entity_attribute_model->table . "," . $this->_sys_entity_attribute_model->primaryKey . ",deleted_at,NULL",
            'entity_type_id' => 'integer|exists:' . $ex1Model->table . "," . $ex1Model->primaryKey . ",deleted_at,NULL",
            'attribute_set_id' => 'integer|exists:' . $ex2Model->table . "," . $ex2Model->primaryKey . ",deleted_at,NULL",
            'attribute_id' => 'integer|exists:' . $ex3Model->table . "," . $ex3Model->primaryKey . ",deleted_at,NULL",
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
            $data[$this->_object_identifier] = array();

            // sorting defaults
            $request->order_by = $request->input("order_by", "") == "" ? explode(",", $allowed_ordering)[0] : $request->order_by;
            $request->sorting = $request->input("sorting", "") == "" ? explode(",", $allowed_sorting)[0] : $request->sorting;


            $query = $this->_sys_entity_attribute_model->select($this->_sys_entity_attribute_model->primaryKey);
            $query->whereNull("deleted_at"); // exclude deleted
            // apply search
            $query = $this->_search($request, $query, $allowed_searching);
            // get total
            $total_records = $query->count();


            // default offset / limits
            //$request->offset = 0;
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
                    $record = $this->_sys_entity_attribute_model->getData($raw_record->{$this->_sys_entity_attribute_model->primaryKey});

                    $data[$this->_object_identifier][] = $record;
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
     * @param $query query
     * @return query
     */
    private function _search($request, $query, $allowed_searching = "")
    {
        // fix indexes
        $fix_indexes = array($this->_sys_entity_attribute_pk, "target_entity_id", "created_by");
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