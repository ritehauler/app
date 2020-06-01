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

class EntityTypeRelationController extends Controller
{

    private $_apiData = array();
    private $_layout = "";
    private $_models = array();
    private $_jsonData = array();
    private $_model_path = "\App\Http\Models\\";
    private $_object_identifier = "sys_entity_type_relation";
    private $_sys_entity_type_identifier = "sys_entity_type_relation"; // usually routes path
    private $_sys_entity_type_relation_pk = "entity_type_relation_id";
    private $_sys_entity_type_relation_ucfirst = "EntityTypeRelation";
    private $_sys_entity_type_relation_model = "SYSEntityTypeRelation";
    private $_plugin_config = array();


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        // load sys_entity_type_relation model
        $this->_sys_entity_type_relation_model = $this->_model_path . $this->_sys_entity_type_relation_model;
        $this->_sys_entity_type_relation_model = new $this->_sys_entity_type_relation_model;

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

        // validations
        $rules = array(
            'entity_type_id' =>  'required|integer|exists:' . $ex1Model->table . "," . $ex1Model->primaryKey . ",deleted_at,NULL",
            'reference_type_id' =>  'required|integer|exists:' . $ex1Model->table . "," . $ex1Model->primaryKey . ",deleted_at,NULL",

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

            // init sys_entity_type_relation
            $sys_entity_type_relation = array();
            // map data
            foreach($rules as $key => $val) {
                $sys_entity_type_relation[$key] = $request->input($key,"");
            }
			
            // other data
            $sys_entity_type_relation["created_at"] = date("Y-m-d H:i:s");

            $sys_entity_type_relation_id = $this->_sys_entity_type_relation_model->put($sys_entity_type_relation);
			
            // response data
            $data[$this->_object_identifier] = $this->_sys_entity_type_relation_model->getData($sys_entity_type_relation_id);
			
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

        // validations
        $rules = array(
            'entity_type_relation_id' =>  'required|integer',
            'entity_type_id' =>  'required|integer|exists:' . $ex1Model->table . "," . $ex1Model->primaryKey . ",deleted_at,NULL",
            'reference_type_id' =>  'required|integer|exists:' . $ex1Model->table . "," . $ex1Model->primaryKey . ",deleted_at,NULL",

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

            // init sys_entity_type_relation
            $sys_entity_type_relation = array();
            // map data
            foreach($rules as $key => $val) {
                $sys_entity_type_relation[$key] = $request->input($key,"");
            }
			
			
            // other data
            $sys_entity_type_relation["updated_at"] = date("Y-m-d H:i:s");

            $sys_entity_type_relation_id = $this->_sys_entity_type_relation_model->set($sys_entity_type_relation[$this->_sys_entity_type_relation_pk],$sys_entity_type_relation);
			
            // response data
            $data[$this->_object_identifier] = $this->_sys_entity_type_relation_model->getData($sys_entity_type_relation[$this->_sys_entity_type_relation_pk]);
			
            $this->_apiData['message'] = trans('api_errors.success');

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
        $request->{$this->_sys_entity_type_relation_pk} = intval($request->input($this->_sys_entity_type_relation_pk, 0));

        // get data
        $sys_entity_type_relation = $this->_sys_entity_type_relation_model->get($request->{$this->_sys_entity_type_relation_pk});
        // validations
        /*if (!in_array("user/get", $this->_plugin_config["webservices"])) {
            $this->_apiData['message'] = 'You are not authorized to access this service.';
        } else*/
        if ($request->{$this->_sys_entity_type_relation_pk} == 0) {
            $this->_apiData['message'] = trans('api_errors.pls_enter_sys_entity_type_relation_id', array("sys_entity_type_relation" => $this->_object_identifier));
        } else if ($sys_entity_type_relation === FALSE) {
            $this->_apiData['message'] = trans('api_errors.invalid_record_request');
        } else {
            // init models
            //$this->__models['predefined_model'] = new Predefined;

            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();


            // update user data
            $this->_sys_entity_type_relation_model->set($sys_entity_type_relation->{$this->_sys_entity_type_relation_pk}, (array)$sys_entity_type_relation);

            // get user data
            $data[$this->_object_identifier] = $this->_sys_entity_type_relation_model->getData($sys_entity_type_relation->{$this->_sys_entity_type_relation_pk}, true);

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
            $this->_sys_entity_type_relation_pk => 'required|integer|exists:' . $this->_sys_entity_type_relation_model->table . "," . $this->_sys_entity_type_relation_model->primaryKey . ",deleted_at,NULL"
        );

        $validator = Validator::make($request->all(), $rules);

        // get data
        $sys_entity_type_relation = $this->_sys_entity_type_relation_model
            ->where($this->_sys_entity_type_relation_pk, "=", $request->{$this->_sys_entity_type_relation_pk})
            ->whereNull("deleted_at")
            ->first();

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else if (!$sys_entity_type_relation) {
            $this->_apiData['message'] = trans('api_errors.invalid_record_request');
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // get
            $sys_entity_type_relation = json_decode(json_encode($sys_entity_type_relation), true);

            // to-do
            // delete dependencies first
            $this->_sys_entity_type_relation_model->remove($sys_entity_type_relation[$this->_sys_entity_type_relation_pk]);

            // response data
            $data[$this->_object_identifier] = $this->_sys_entity_type_relation_model->getData($sys_entity_type_relation[$this->_sys_entity_type_relation_pk]);

            $this->_apiData['message'] = trans('api_errors.success');

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
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));

        // override object identifier
        $this->_object_identifier = $this->_object_identifier . "_" . strtolower(__FUNCTION__);
        // allowed order
        $allowed_ordering = $allowed_searching = $this->_sys_entity_type_relation_model->primaryKey . ",title,attribute_code,created_at";
        $allowed_sorting = "asc,desc";


        // extra models
        $ex1Model = $this->_model_path . "SYSEntityType";
        $ex1Model = new $ex1Model;

        // validations
        $rules = array(
            'entity_type_id' =>  'integer|exists:' . $ex1Model->table . "," . $ex1Model->primaryKey . ",deleted_at,NULL",
            'reference_type_id' =>  'integer|exists:' . $ex1Model->table . "," . $ex1Model->primaryKey . ",deleted_at,NULL",

        );;
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


            $query = $this->_sys_entity_type_relation_model->select($this->_sys_entity_type_relation_model->primaryKey);
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
                    $record = $this->_sys_entity_type_relation_model->getData($raw_record->{$this->_sys_entity_type_relation_model->primaryKey});

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
        $fix_indexes = array($this->_sys_entity_type_relation_pk, "target_entity_id", "created_by");
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