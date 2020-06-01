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

class EntityEntityMapController extends Controller
{

    private $_apiData = array();
    private $_layout = "";
    private $_models = array();
    private $_jsonData = array();
    private $_object_identifier = "entity_entity_map";
    private $_sys_entity_entity_map_identifier = "system_entity_entity_map"; // usually routes path
    private $_sys_entity_entity_map_pk  = "entity_entity_map_id";
    private $_entityUcfirst = "EntityEntityMap";
    private $_sys_entity_entity_map_model = "SYSEntityEntityMap";
    private $_pluginConfig = array();
    public $validation = array();


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        // load entity model
        $this->_sys_entity_entity_map_model = $this->_modelPath . $this->_sys_entity_entity_map_model;
        $this->_sys_entity_entity_map_model = new $this->_sys_entity_entity_map_model;

        $this->__models['api_method_model'] = new ApiMethod;
        // error response by default
        $this->_apiData['kick_user'] = 0;
        $this->_apiData['response'] = "error";

        // plugin config
        //$this->_pluginConfig = $this->__models['entity_plugin_model']->getPluginSchema($this->_entity_entity_map_id, $this->_plugin_identifier);
        // set defaults
        //$this->_pluginConfig = isset($this->_pluginConfig->webservices) ? $this->_pluginConfig->webservices : array();
        //$this->_pluginConfig["webservices"] = $this->_pluginConfig;

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
        $ex1Model = $this->_modelPath . "SYSEntity";
        $ex1Model = new $ex1Model;

       // validations
        $rules = array(
            $ex1Model->primaryKey => 'required|exists:' . $ex1Model->table . "," . $ex1Model->primaryKey . ",deleted_at,NULL",
            'target_entity_id' => 'required|exists:' . $ex1Model->table . "," . $ex1Model->primaryKey . ",deleted_at,NULL",
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

            // init entity
            $entity = array();
            // map data
            foreach($rules as $key => $val) {
                $entity[$key] = $request->input($key,"");
            }
            // set data types for optional fields
            //$entity["parent_id"] = intval($entity["parent_id"]) > 0 ? $entity["parent_id"] : NULL;
            //$entity["allow_backend_login"] = intval($entity["allow_backend_login"]) > 0 ? 1 : 0;
            // other data
            $entity["created_at"] = date("Y-m-d H:i:s");


			$entity_entity_map_id = $this->_sys_entity_entity_map_model->put($entity);

            // response data
            $data[$this->_object_identifier] = $this->_sys_entity_entity_map_model->getData($entity_entity_map_id);

 
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
        $request->{$this->_sys_entity_entity_map_pk} = intval($request->input($this->_sys_entity_entity_map_pk, 0));

        // get data
        $entity = $this->_sys_entity_entity_map_model->get($request->{$this->_sys_entity_entity_map_pk});
        // validations
        /*if (!in_array("user/get", $this->_pluginConfig["webservices"])) {
            $this->_apiData['message'] = 'You are not authorized to access this service.';
        } else*/
        if ($request->{$this->_sys_entity_entity_map_pk} == 0) {
            $this->_apiData['message'] = trans('api_errors.pls_enter_entity_entity_map_id', array("entity" => $this->_object_identifier));
        } else {
            // init models
            //$this->__models['predefined_model'] = new Predefined;

            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // get user data
            $data[$this->_object_identifier] = $this->_sys_entity_entity_map_model->getData($entity->{$this->_sys_entity_entity_map_pk}, true);

            // message
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
        // extra models
        $ex1Model = $this->_modelPath . "SYSEntity";
        $ex1Model = new $ex1Model;

        // validations
        $rules = array(
            'entity_id ' => 'int|exists:' . $ex1Model->table . "," . $ex1Model->primaryKey . ",deleted_at,NULL",
            'target_entity_id' => 'int|exists:' . $ex1Model->table . "," . $ex1Model->primaryKey . ",deleted_at,NULL",
        );

          // override object identifier
        $this->_object_identifier = $this->_object_identifier . "_" . strtolower(__FUNCTION__);

        // allowed order
        $allowed_ordering = $allowed_searching = $this->_sys_entity_entity_map_model->primaryKey . ",identifier,created_at";
        $allowed_sorting = "asc,desc";
        //print_r($rules);exit;
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


            $query = $this->_sys_entity_entity_map_model->select($this->_sys_entity_entity_map_model->primaryKey);
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

                foreach ($raw_records as $raw_record) {
                    //$record = $raw_record;
                    $record = $this->_sys_entity_entity_map_model->getData($raw_record->{$this->_sys_entity_entity_map_model->primaryKey});

                    // init attributes
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
        $fix_indexes = array($this->_sys_entity_entity_map_pk, "target_entity_entity_map_id", "created_by");
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
            $this->_sys_entity_entity_map_pk => 'required|integer|exists:' . $this->_sys_entity_entity_map_model->table . "," . $this->_sys_entity_entity_map_model->primaryKey . ",deleted_at,NULL"
        );

        $validator = Validator::make($request->all(), $rules);

        // get data
        $sys_entity_entity_map = $this->_sys_entity_entity_map_model
            ->where($this->_sys_entity_entity_map_pk, "=", $request->{$this->_sys_entity_entity_map_pk})
            ->whereNull("deleted_at")
            ->first();

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        }  else {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // get
            $sys_entity_entity_map = json_decode(json_encode($sys_entity_entity_map), true);

            // to-do
            // delete dependencies first
            $this->_sys_entity_entity_map_model->delete($sys_entity_entity_map[$this->_sys_entity_entity_map_pk]);

            // response data
            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }
    
}