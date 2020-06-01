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

class AttributeSetController extends Controller
{

    private $_apiData = array();
    private $_layout = "";
    private $_models = array();
    private $_jsonData = array();
    private $_model_path = "\App\Http\Models\\";
    private $_object_identifier = "attribute_set";
    private $_attribute_identifier = "system_attribute_set"; // usually routes path
    private $_attribute_set_pk = "attribute_set_id";
    private $_attribute_ucfirst = "attribute_set";
    private $_attribute_set_model = "SYSAttributeSet";
    private $_plugin_config = array();
    public $attribute_set = array();


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        // load attribute model
        $this->_attribute_set_model = $this->_model_path . $this->_attribute_set_model;
        $this->_attribute_set_model = new $this->_attribute_set_model;

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
            //'entity_type_id' => 'required|integer',
            'attribute_set_name' => 'required|string',
			'sort_order' => 'required|integer',
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

            // init attribute

            // map data
            foreach($rules as $key => $val) {
                $attribute_set[$key] = $request->input($key,"");
            }

            // other data
            $attribute_set["created_at"] = date("Y-m-d H:i:s");


            $attribute_set_id = $this->_attribute_set_model->put($attribute_set);

            // response data
            $data[$this->_object_identifier] = $this->_attribute_set_model->getData($attribute_set_id);

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

        // validations
        $rules = array(
			'attribute_set_id' => 'required|integer',
            //'entity_type_id' => 'required|integer',
            'attribute_set_name' => 'required|string',
			'sort_order' => 'required|integer',
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

            // init attribute

            // map data
            foreach($rules as $key => $val) {
                $attribute_set[$key] = $request->input($key,"");
            }

            // other data
            $attribute_set["updated_at"] = date("Y-m-d H:i:s");


            $attribute_set_id = $this->_attribute_set_model->set( $attribute_set[$this->_attribute_set_pk],$attribute_set);

            // response data
            $data[$this->_object_identifier] = $this->_attribute_set_model->getData($attribute_set[$this->_attribute_set_pk]);

            $this->_apiData['message'] = trans('api_errors.success');

            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);
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

            $data[$this->_object_identifier] = $entity;


            // overrite message
            $this->_apiData['message'] = trans('api_errors.entity_already_exists',array("entity" => $this->_object_identifier));

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
        $request->{$this->_attribute_set_pk} = intval($request->input($this->_attribute_set_pk, 0));

        // get data
        $attribute_set = $this->_attribute_set_model->get($request->{$this->_attribute_set_pk});
        // validations
        /*if (!in_array("user/get", $this->_plugin_config["webservices"])) {
            $this->_apiData['message'] = 'You are not authorized to access this service.';
        } else*/
        if ($request->{$this->_attribute_set_pk} == 0) {
            $this->_apiData['message'] = trans('api_errors.pls_enter_attribute_set_id', array("attribute_set" => $this->_object_identifier));
        } else if ($attribute_set === FALSE) {
            $this->_apiData['message'] = trans('api_errors.invalid_record_request');
        }  else {
            // init models
            //$this->__models['predefined_model'] = new Predefined;

            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // update user data
            $this->_attribute_set_model->set($attribute_set->{$this->_attribute_set_pk}, (array)$attribute_set);

            // get user data
            $data[$this->_object_identifier] = $this->_attribute_set_model->getData($attribute_set->{$this->_attribute_set_pk}, true);

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
            $this->_attribute_set_pk => 'required|integer|exists:' . $this->_attribute_set_model->table . "," . $this->_attribute_set_model->primaryKey . ",deleted_at,NULL"
        );

        $validator = Validator::make($request->all(), $rules);

        // get data
        $attribute_set = $this->_attribute_set_model
            ->where($this->_attribute_set_pk, "=", $request->{$this->_attribute_set_pk})
            ->whereNull("deleted_at")
            ->first();

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else if (!$attribute_set) {
            $this->_apiData['message'] = trans('api_errors.invalid_record_request');
        } else {
			
			 // extra models
        	$ex2Model = $this->_model_path . "SYSEntityAttribute";
        	$ex2Model = new $ex2Model;
			
			$check_exists = $ex2Model
                ->where($this->_attribute_set_pk, "=",$request->{$this->_attribute_set_pk})
                ->whereNull("deleted_at")
                ->first();
				 
			if(!$check_exists){
				// success response
				$this->_apiData['response'] = "success";
	
				// init output data array
				$this->_apiData['data'] = $data = array();
	
				// get
				$attribute_set = json_decode(json_encode($attribute_set), true);
	
				// to-do
				// delete dependencies first
				$this->_attribute_set_model->remove($attribute_set[$this->_attribute_set_pk]);
	
				// response data
				$data[$this->_object_identifier] = $this->_attribute_set_model->getData($attribute_set[$this->_attribute_set_pk]);
	
				$this->_apiData['message'] = trans('api_errors.success');
	
				// assign to output
				$this->_apiData['data'] = $data;
			}else{
				$this->_apiData['message'] = trans('api_errors.attribute_set_in_use');
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
       /* $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));*/

        // override object identifier
        $this->_object_identifier = $this->_object_identifier . "_" . strtolower(__FUNCTION__);
        // extra models
        $exModel = $this->_model_path . "SYSEntityType";
        $exModel = new $exModel;
        // allowed order
        $allowed_ordering = $allowed_searching = $this->_attribute_set_model->primaryKey . ",entity_type_id,attribute_set_name,created_at";
        $allowed_sorting = "asc,desc";


        // validations
        $rules = array(
            'entity_type_id' => 'integer:exists:' . $exModel->table . "," . $exModel->primaryKey . ",deleted_at,NULL",
            'attribute_set_name' => "string",
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


            $query = $this->_attribute_set_model->select($this->_attribute_set_model->primaryKey);
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
                    $record = $this->_attribute_set_model->getData($raw_record->{$this->_attribute_set_model->primaryKey});

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
        $fix_indexes = array($this->_attribute_set_pk, "entity_type_id", "created_by");
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