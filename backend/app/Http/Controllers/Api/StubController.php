<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Validator;
// load models
use App\Http\Models\ApiMethod;
use App\Http\Models\ApiMethodField;
use App\Http\Models\ApiUser;
use App\Http\Models\Conf;
use App\Http\Models\Asset;


class StubController extends Controller
{

    public $_assignData = array(
        'pDir' => '',
        'dir' => DIR_API
    );
    public $_apiData = array();
    public $_layout = "";
    public $_models = array();
    public $_jsonData = array();
    protected $_model = "ApiStub";
    protected $_hook = "APIStub";

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        // load extension model
        $this->_model = $this->_modelPath . $this->_model;
        $this->_model = new $this->_model;

        // error response by default
        $this->_apiData['kick_user'] = 0;
        $this->_apiData['response'] = "error";
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function index()
    {


    }


    /**
     * post
     *
     * @return Response
     */
    public function post(Request $request)
    {
        // optional fields
        $optional_fields = array(
            'description'
        );

        // validation rules
        $rules = array(
            'title' => 'required|string',
            'object_identifier' => 'required|string',
            'endpoint_uri' => 'required|string|unique:' . $this->_model->table . ',endpoint_uri,0,' . $this->_model->primaryKey . ',request_type,' . $request->input('request_type').',deleted_at,NULL',
            'request_type' => 'required|string|in:get,post',
            'request' => 'required|string',
            'response' => 'required|string',
        );
        // validations
        $validator = Validator::make($request->all(), $rules);

        // set
        $p_request = json_encode(json_decode(trim($request->input('request'))));
        $p_request = $p_request == '{}' ? null : $p_request;
        $p_response = json_encode(json_decode(trim($request->response)));
        $p_response = $p_response == '{}' ? null : $p_response;

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } elseif (!$p_request) {
            $this->_apiData['message'] = trans('api_errors.entity_is_invalid', array("entity" => 'request json'));
        } elseif (!$p_response) {
            $this->_apiData['message'] = trans('api_errors.entity_is_invalid', array("entity" => 'response json'));
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // init data
            $entity = $save = array();
            $datestamp = date("Y-m-d H:i:s");

            // set optional fields
            if (isset($optional_fields[0])) {
                foreach ($optional_fields as $field) {
                    $val = trim($request->input($field));
                    if ($val !== '') {
                        $save[$field] = $val;
                    }
                }
            }

            // set required fields
            if (isset($rules) && count($rules) > 0) {
                foreach ($rules as $key => $val) {
                    $save[$key] = $request->input($key);
                }
            }

            // set other fields
            $save['created_at'] = $datestamp;

            // other fields
            $id = $this->_model->put($save);

            // get data
            $entity = $this->_model->getData($id);


            // response data
            $data[$request->object_identifier] = $entity;

            // assign to output
            $this->_apiData['data'] = $data;

        }

        // call hook
        //$this->_apiData = $this->_hookData($this->_hook, __FUNCTION__, $request, $this->_apiData);

        return $this->__ApiResponse($request, $this->_apiData);

    }


    /**
     * get
     *
     * @return Response
     */
    public function get(Request $request)
    {
        // validation rules
        $rules = array(
            $this->_model->primaryKey => 'required|integer|exists:' . $this->_model->table . ',' . $this->_model->primaryKey.',deleted_at,NULL'
        );
        // validations
        $validator = Validator::make($request->all(), $rules);

        $record = $this->_model->get($request->{$this->_model->primaryKey}, true);

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } elseif (!$record) {
            $this->_apiData['message'] = trans('api_errors.entity_is_invalid', array("entity" => 'request json'));
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();


            // get data
            $entity = $this->_model->getData($record->{$this->_model->primaryKey});

            // response data
            $data[$request->object_identifier] = $entity;

            // assign to output
            $this->_apiData['data'] = $data;

        }

        // call hook
        //$this->_apiData = $this->_hookData($this->_hook, __FUNCTION__, $request, $this->_apiData);

        return $this->__ApiResponse($request, $this->_apiData);

    }


    /**
     * update
     *
     * @return Response
     */
    public function update(Request $request)
    {
        // optional fields
        $optional_fields = array(
            'description'
        );

        // validation rules
        $rules = array(
            $this->_model->primaryKey => 'required|integer|exists:' . $this->_model->table . ',' . $this->_model->primaryKey.',deleted_at,NULL',
            'title' => 'required|string',
            'object_identifier' => 'required|string',
            'endpoint_uri' => 'required|string|unique:' . $this->_model->table . ',endpoint_uri,'.$request->{$this->_model->primaryKey}.',' . $this->_model->primaryKey . ',request_type,' . $request->input('request_type').',deleted_at,NULL',
            'request_type' => 'required|string|in:get,post',
            'request' => 'required|string',
            'response' => 'required|string',
        );
        // validations
        $validator = Validator::make($request->all(), $rules);

        // set
        $p_request = json_encode(json_decode(trim($request->input('request'))));
        $p_request = $p_request == '{}' ? null : $p_request;
        $p_response = json_encode(json_decode(trim($request->response)));
        $p_response = $p_response == '{}' ? null : $p_response;



        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } elseif (!$p_request) {
            $this->_apiData['message'] = trans('api_errors.entity_is_invalid', array("entity" => 'request json'));
        } elseif (!$p_response) {
            $this->_apiData['message'] = trans('api_errors.entity_is_invalid', array("entity" => 'response json'));
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // init data
            $entity = $save = array();
            $datestamp = date("Y-m-d H:i:s");

            // get record
            $record = $this->_model->get($request->{$this->_model->primaryKey}, true);

            $save = (array)$record;

            // set optional fields
            if (isset($optional_fields[0])) {
                foreach ($optional_fields as $field) {
                    $val = trim($request->input($field));
                    if ($val !== '') {
                        $save[$field] = $val;
                    }
                }
            }

            // set required fields
            if (isset($rules) && count($rules) > 0) {
                foreach ($rules as $key => $val) {
                    $save[$key] = $request->input($key);
                }
            }

            // set other fields
            $save['updated_at'] = $datestamp;

            // other fields
            $this->_model->set($record->{$this->_model->primaryKey}, $save);

            // get data
            $entity = $this->_model->getData($record->{$this->_model->primaryKey});


            // response data
            $data[$request->object_identifier] = $entity;

            // assign to output
            $this->_apiData['data'] = $data;

        }

        // call hook
        //$this->_apiData = $this->_hookData($this->_hook, __FUNCTION__, $request, $this->_apiData);

        return $this->__ApiResponse($request, $this->_apiData);

    }



    /**
     * get
     *
     * @return Response
     */
    public function delete(Request $request)
    {
        // validation rules
        $rules = array(
            $this->_model->primaryKey => 'required|integer|exists:' . $this->_model->table . ',' . $this->_model->primaryKey.',deleted_at,NULL'
        );
        // validations
        $validator = Validator::make($request->all(), $rules);


        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();


            // remove
            $this->_model->remove($request->{$this->_model->primaryKey});

            // get data
            $entity = $this->_model->getData($request->{$this->_model->primaryKey});

            // response data
            $data[$request->object_identifier] = $entity;

            // assign to output
            $this->_apiData['data'] = $data;

        }

        // call hook
        //$this->_apiData = $this->_hookData($this->_hook, __FUNCTION__, $request, $this->_apiData);

        return $this->__ApiResponse($request, $this->_apiData);

    }


    /**
     * virtualItems
     *
     * @return JSON
     */
    public function xxxgetAll(Request $request)
    {
        // init vars
        $def_types = array("image", "audio", "video", "xml");

        // get params
        $type = trim(strip_tags($request->type));
        $page_no = intval(trim(strip_tags($request->page_no)));


        if ($type != "" && (!in_array($type, $def_types))) {
            $this->_apiData['message'] = 'Invalid type';
        } else {
            // success response
            $this->_apiData['response'] = "success";
            // init output data array
            $this->_apiData['data'] = $data = array();

            // set initial array for records
            $data["assets"] = array();

            $query = $this->_model->select("asset_id");
            $query->orderBy("created_at", "ASC");
            if ($type != "") {
                $query->where("type", "=", $type);
            }
            $query->whereNull("deleted_at");
            $raw_records = $query->get();
            //$total_records = $raw_records->count();
            $total_records = count($raw_records);

            // offfset / limits / valid pages
            $total_pages = ceil($total_records / PAGE_LIMIT_API);
            $page_no = $page_no >= $total_pages ? $total_pages : $page_no;
            $page_no = $page_no <= 1 ? 1 : $page_no;
            $offset = PAGE_LIMIT_API * ($page_no - 1);


            $raw_records = $raw_records->splice($offset, PAGE_LIMIT_API);

            // set records
            if (isset($raw_records[0])) {
                //var_dump($raw_records); exit;
                foreach ($raw_records as $raw_record) {
                    $asset = $this->_model->getData($raw_record->asset_id);

                    $data["assets"][] = $asset;
                }
            }


            // set pagination response
            $data["page"] = array(
                "current" => $page_no,
                "total" => $total_pages,
                "next" => $page_no >= $total_pages ? 0 : $page_no + 1,
                "prev" => $page_no <= 1 ? 0 : $page_no - 1
            );

            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__ApiResponse($request, $this->_apiData);
    }


}
