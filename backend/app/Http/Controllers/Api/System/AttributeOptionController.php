<?php
namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Controller;
use App\Http\Models\PLAttachment;
use App\Http\Models\SYSAttribute;
use App\Libraries\System\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use View;
use Validator;
// load models
use App\Http\Models\ApiMethod;
use App\Http\Models\ApiMethodField;
use App\Http\Models\ApiUser;
use App\Http\Models\EFEntityPlugin;
use App\Http\Models\Conf;

//use Twilio;

class AttributeOptionController extends Controller
{
    private $_apiData = array();
    private $_layout = "";
    private $_models = array();
    private $_jsonData = array();
    private $_model_path = "\App\Http\Models\\";
    private $_object_identifier = "attribute_option";
    private $_attribute_option_identifier = "system_attribute_option"; // usually routes path
    private $_attribute_option_pk = "attribute_option_id";
    private $_attribute_option_ucfirst = "attributeOption";
    private $_attribute_option_model = "SYSAttributeOption";
    private $_plugin_config = array();
	private $_mobile_json = false;
	
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        // load attribute model
        $this->_attribute_option_model = $this->_model_path . $this->_attribute_option_model;
        $this->_attribute_option_model = new $this->_attribute_option_model;

        // error response by default
        $this->_apiData['kick_user'] = 0;
        $this->_apiData['response'] = "error";
		$this->_mobile_json = (isset($request->mobile_json))?true:false;
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
       // $request->merge(array_map('strip_tags', $request->all()));
        //$request->merge(array_map('trim', $request->all()));

        // validations
        $rules = array(
            'attribute_id' => 'required|integer',
            'value' => "required|string",
            'option' => "required|string",
        );
        $validator = Validator::make($request->all(), $rules);

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else {

            // init attribute
            $attribute = array();

            //upload image for option
            if ($request->file('file')) {

                $request['is_sys_attachment'] = 1;

                $sys_attribute_model = new SYSAttribute();
                $attribute_raw = $sys_attribute_model->get($request->attribute_id);
                //echo "<pre>"; print_r($attribute_raw); exit;
                if(isset($attribute_raw)){
                    $request['attribute_code'] = $attribute_raw->attribute_code;
                }

                $attachment_lib = new Attachment();
                $file_response =  $attachment_lib->saveAttachment($request);

                $file_response = json_decode(json_encode($file_response));
               // echo "<pre>"; print_r($file_response); exit;
                if($file_response->error == 1){
                    $this->_apiData['message'] = $file_response->message;
                    return $this->__apiResponse($request, $this->_apiData);
                }
                else{
                    $attribute["file"] = $file_response->data->attachment->attachment_id;
                }
            }

            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // map data
            foreach($rules as $key => $val) {
                $attribute[$key] = $request->input($key,"");
            }

            // other data
            $attribute["created_at"] = date("Y-m-d H:i:s");


            $attribute_option_id = $this->_attribute_option_model->put($attribute);

            // response data
            $data[$this->_object_identifier] = $this->_attribute_option_model->getData($attribute_option_id);

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
      //  $request->merge(array_map('strip_tags', $request->all()));
       // $request->merge(array_map('trim', $request->all()));

        // validations
        $rules = array(
			'attribute_option_id' => 'required|integer',
            'attribute_id' => 'required|integer',
            'value' => "required|string",
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
            $attribute = array();

            //upload image for option
            if ($request->file('file')) {

                $request['is_sys_attachment'] = 1;

                $sys_attribute_model = new SYSAttribute();
                $attribute_raw = $sys_attribute_model->get($request->attribute_id);
                //echo "<pre>"; print_r($attribute_raw); exit;
                if(isset($attribute_raw)){
                    $request['attribute_code'] = $attribute_raw->attribute_code;
                }

                $attachment_lib = new Attachment();
                $file_response =  $attachment_lib->saveAttachment($request);

                $file_response = json_decode(json_encode($file_response));
                // echo "<pre>"; print_r($file_response); exit;
                if($file_response->error == 1){
                    $this->_apiData['message'] = $file_response->message;
                    return $this->__apiResponse($request, $this->_apiData);
                }
                else{

                    //Remove existing file
                    $attribute_option_data = $this->_attribute_option_model->get($request->attribute_option_id);
                    if(!empty($attribute_option_data->file)){
                        $attachment_lib->_model->hardRemove($attribute_option_data->file);
                    }

                    $attribute["file"] = $file_response->data->attachment->attachment_id;
                }
            }

            // map data
            foreach($rules as $key => $val) {
                $attribute[$key] = $request->input($key,"");
            }

            // other data
            $attribute["updated_at"] = date("Y-m-d H:i:s");

            $attribute_option_id = $this->_attribute_option_model->set($attribute[$this->_attribute_option_pk],$attribute);

            // response data
            $data[$this->_object_identifier] = $this->_attribute_option_model->getData($attribute[$this->_attribute_option_pk]);

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
        $request->{$this->_attribute_option_pk} = intval($request->input($this->_attribute_option_pk, 0));

        // get data
        $attribute = $this->_attribute_option_model->get($request->{$this->_attribute_option_pk});
        // validations
        /*if (!in_array("user/get", $this->_plugin_config["webservices"])) {
            $this->_apiData['message'] = 'You are not authorized to access this service.';
        } else*/
        if ($request->{$this->_attribute_option_pk} == 0) {
            $this->_apiData['message'] = trans('api_errors.pls_enter_attribute_option_id', array("attribute" => $this->_object_identifier));
        }  else {
            // init models
            //$this->__models['predefined_model'] = new Predefined;

            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // update user data
            $this->_attribute_option_model->set($attribute->{$this->_attribute_option_pk}, (array)$attribute);

            // get user data
            $data[$this->_object_identifier] = $this->_attribute_option_model->getData($attribute->{$this->_attribute_option_pk}, true);

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
            $this->_attribute_option_pk => 'required|integer|exists:' . $this->_attribute_option_model->table . "," . $this->_attribute_option_model->primaryKey . ",deleted_at,NULL"
        );

        $validator = Validator::make($request->all(), $rules);

        // get data
        $attribute = $this->_attribute_option_model
            ->where($this->_attribute_option_pk, "=", $request->{$this->_attribute_option_pk})
            ->whereNull("deleted_at")
            ->first();

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else if (!$attribute) {
            $this->_apiData['message'] = trans('api_errors.invalid_record_request');
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // get
            $attribute = json_decode(json_encode($attribute), true);

            // to-do
            // delete dependencies first
            $this->_attribute_option_model->delete($attribute[$this->_attribute_option_pk]);

            // response data
            $data[$this->_object_identifier] = $this->_attribute_option_model->getData($attribute[$this->_attribute_option_pk]);

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
        /*$request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));*/

        // override object identifier
        $this->_object_identifier = $this->_object_identifier . "_" . strtolower(__FUNCTION__);
        // extra models
        $exModel = $this->_model_path . "SYSAttribute";
        $exModel = new $exModel;
        // allowed order
        $allowed_ordering = $allowed_searching = $this->_attribute_option_model->primaryKey . ",attribute_id,value,created_at";
        $allowed_sorting = "asc,desc";

		if(isset($request->attribute_code) && $request->attribute_code!=''){
			$attributeData = $exModel->getEntityTypeByName($request->attribute_code);
			$request->attribute_id = 0;
			if($attributeData) {
				$request->attribute_id = (integer)$attributeData->attribute_id;
				$request->attribute_id =  $attributeData->attribute_id;
			}
			$request->replace(array_merge($request->all(),array('attribute_id' =>$request->attribute_id)));
		}

        // validations
        $rules = array(
            'attribute_id' => 'exists:' . $exModel->table . "," . $exModel->primaryKey . ",deleted_at,NULL",
            'value' => "string",
            'identifier' => "string",
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


            $query = $this->_attribute_option_model->select($this->_attribute_option_model->primaryKey);
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
			if($this->_mobile_json) $data[$this->_object_identifier] =(object)array();
            // set records
            if (isset($raw_records[0])) {
                //print_r($raw_records); exit;
                foreach ($raw_records as $raw_record) {
                    //$record = $raw_record;
                    //$record = $this->_attribute_option_model->getData($raw_record->{$this->_attribute_option_model->primaryKey});
					$record = $this->_attribute_option_model->getOptionData($raw_record->{$this->_attribute_option_model->primaryKey});
                   // echo "<pre>"; print_r($record); continue;
                    if($record){

                        //if option has image then get the attach
                        $file = new \StdClass();
                        if($record->file != ''){
                            $pl_attachment = new PLAttachment();

                            if($this->_mobile_json){
                               $file =  $pl_attachment->getAttachmentGallery($record->file);
                            }else{
                                $file = $pl_attachment->getData($record->file);
                            }
                        }

                        if($this->_mobile_json){

                            if(!isset($data[$this->_object_identifier]->{$record->attribute_code})){
                                $data[$this->_object_identifier]->{$record->attribute_code} = (object)array('options'=>array());
                            }
                            $data[$this->_object_identifier]->{$record->attribute_code}->{'attribute_code'} = $record->attribute_code;
                            $data[$this->_object_identifier]->{$record->attribute_code}->{'frontend_label'} = $record->frontend_label;
                            $data[$this->_object_identifier]->{$record->attribute_code}->options[] = array('title'=>$record->option,
                                'value'=>$record->value,
                                'is_other'=>$record->is_other,
                                'file' => $file
                            );

                        }else{
                            $record->file = $file;
                            $data[$this->_object_identifier][] = $record;
                        }
                    }

					unset($record);
                   // print_r($data); exit;
                }
                //exit;
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
        $fix_indexes = array($this->_attribute_option_pk, "attribute_id", "created_by");
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