<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Models\SYSEntity;
use App\Http\Models\SYSEntityAuth;
use App\Http\Models\SYSEntityType;
use App\Http\Models\SYSRole;
use App\Http\Models\SYSTableFlat;
use App\Libraries\CustomHelper;
use App\Libraries\EntityAuthTrigger;
use App\Libraries\System\Entity;
use Illuminate\Http\Request;
use View;
use Validator;
// load models
use App\Http\Models\Conf;
use App\Libraries\ApiCurl;
use App\Libraries\EntityTrigger;
use App\Libraries\StripeLib;
//use Twilio;

class EntityAuthController extends Controller
{
    protected $_assignData = array(
        'p_dir' => '',
        'dir' => DIR_API
    );
    protected $_apiData = array();
    protected $_layout = "";
    protected $_models = array();
    protected $_jsonData = array();
    private $_mobileJson = false;
    protected $_objectIdentifier, $_entityIdentifier = 'entity_auth';
    protected $_targetEntityModel;
    protected $_entityConfFile = 'pl_entity_auth';
    private $_entityTypeModel = "SYSEntityType";
    private $_entityTypeData = NULL;
    protected $_model;
    protected $_hook = "EntityAuth";
    public $_config_dir = "";
    private $_extHook = "";
    private $_StripeLib = NULL;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        // models
        // - conf
        $this->__models['conf_model'] = new Conf;

        // - main model
        $this->_model = new SYSEntityAuth();

        // - entity type
        $this->_entityTypeModel = $this->_modelPath . $this->_entityTypeModel;
        $this->_entityTypeModel = new $this->_entityTypeModel;

        // - target entity
        $this->_targetEntityModel = new SYSEntity();
        $this->_entityModel = $this->_targetEntityModel;
        $this->_config_dir = "panel";

        $this->_StripeLib = new StripeLib();

        $this->_mobileJson = (isset($request->mobile_json)) ? true : false;

        if (!isset($request->entity_type_id) && isset($request->entity_id)) {
            $entity = $this->_targetEntityModel->getBy("entity_id", trim($request->{$this->_targetEntityModel->primaryKey}));
            if (isset($entity->entity_type_id))
                $request->request->add(['entity_type_id' => $entity->entity_type_id]);
        }

        // accept entity_type as identifier too
        if (!is_numeric(trim($request->entity_type_id))) {
            $this->_entityTypeData = $this->_entityTypeModel->getBy("identifier", trim($request->{$this->_entityTypeModel->primaryKey}));
            // assign to request
            $t_id = isset($this->_entityTypeData->{$this->_entityTypeModel->primaryKey}) ?
                $this->_entityTypeData->{$this->_entityTypeModel->primaryKey} : 0;
            $request->merge(array($this->_entityTypeModel->primaryKey => $t_id));
        } else {
            $this->_entityTypeData = $this->_entityTypeModel->get(trim($request->{$this->_entityTypeModel->primaryKey}));
        }

        // identifiers
        $this->_objectIdentifier = $this->_entityIdentifier;

        if ($this->_mobileJson) {
            $this->_objectIdentifier = isset($this->_entityTypeData->identifier) ?
                $this->_entityTypeData->identifier : $this->_objectIdentifier; // default
        }

        $this->_model->_mobileJson = $this->_mobileJson;

        // error response by default
        $this->_apiData['kick_user'] = 0;
        $this->_apiData['response'] = "error";

        // validate (entity_type required for each request)
        if (!$this->_entityTypeData) {
            $this->_apiData['message'] = trans('api_errors.entity_is_required', array("entity" => "Entity type id"));
            $t = $this->__ApiResponse($request, $this->_apiData);
            exit(json_encode($t));
        }
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
     * Create User
     *
     * @return Response
     */
    public function post(Request $request)
    {
        // trim/escape all
        /* $request->merge(array_map('strip_tags', $request->all()));
         $request->merge(array_map('trim', $request->all()));*/

        // extra models
        $entityAttributeModel = $this->_modelPath . "SYSEntityAttribute";
        $entityAttributeModel = new $entityAttributeModel;

        // check entity-type related attribute exists
        $e_type_att_exists = 0;
        $listOfAttributeToBeValidate = $entityAttributeModel->getEntityAttributeValidationList($request->entity_type_id);
        $e_type_att_exists = isset($listOfAttributeToBeValidate[0]) ? 1 : 0;

        // other var defaults
        $email = trim(str_replace(" ", "", strip_tags($request->input('email', ''))));
        $mobile_no = trim(str_replace(" ", "", strip_tags($request->input('mobile_no', ''))));
        $fileIndex = "raw_image";
        $login_type = "email";

        // optional fields
        $optionalFields = array(
            //"entity_type_id",
            //"entity_id",
            //"first_name",
            //"last_name",
            //"type",
            //"name",
            //"dob",
            "country_id",
            "state_id",
            //"zip_code",
            //"gender",
            //"role_id",
            "name",
            "email",
            "mobile_no",
            "platform_type",
            "platform_id",
            "device_udid",
            "device_type",
            "device_token",
        );


        // defaults
        $prevent_process = 0;
        //$request->type = $request->input("type", "") == "" ? explode(",", config("constants.ALLOWED_ENTITY_TYPES"))[0] : $request->type;
        $request->platform_type = $request->input("platform_type", "");
        $request->platform_type = $request->platform_type == "" ? "custom" : $request->platform_type;
        $request->has_temp_password = intval($request->input("has_temp_password", 0)) > 0 ? 1 : 0;
        $request->platform_id = $request->input("platform_id", "");
       // $request->mobile_no = str_replace("+", "", $request->mobile_no);

        $request->request->add(['mobile_no' => str_replace("+", "", $request->mobile_no)]);

        $request->is_auth_exists = intval($request->is_auth_exists) > 0 ? 1 : 0;
        if ($request->device_type == "") {
            $request->merge(array("device_type" => explode(",", config("constants.DEVICE_TYPES"))[0]));
        }


        //Get Role IDs if entity type if there is no sub role ids
        if ($this->_entityTypeData->identifier != "business_user") {
            if(isset($request->entity_type_id) && !empty($request->entity_type_id)){
                //Get Role ID
                $role_model = new SYSRole();
                $role_raw = $role_model->getRoleIdByEntityType($request->entity_type_id);

                if($role_raw){
                    $role_id = $role_raw->role_id;
                    $request->request->add(['role_id' => $role_id]);
                }
            }
        }

        // check cases
        if ($request->is_auth_exists > 0) {
            // validations
            $validator = Validator::make($request->all(), array(
                $this->_entityTypeModel->primaryKey => 'required|integer|exists:' . $this->_entityTypeModel->table . "," . $this->_entityTypeModel->primaryKey . ",allow_auth,1,deleted_at,NULL",
                $this->_model->primaryKey => 'required|int|exists:' . $this->_model->table . "," . $this->_model->primaryKey . ",deleted_at,NULL",
//			'role_id' => 'required|int|exists:' . $ex2Model->table . "," . $ex2Model->primaryKey . ",deleted_at,NULL",
                //'first_name' => 'required',
                //'last_name' => 'required',
                //"dob" => "date_format:Y-m-d",
                "country_id" => "integer|exists:country,country_id",
                "state_id" => "integer|exists:state,state_id",
                //"zip_code" => "required",
                //'gender' => 'in:' . config("constants.ALLOWED_GENDERS"),
                'platform_type' => 'string|in:' . config("constants.SOCIAL_PLATFORM_TYPES"),
                'platform_id' => 'string',
                'device_type' => 'required|in:' . config("constants.DEVICE_TYPES"),
                //'device_udid' => 'required',
                $fileIndex => "mimes:jpg,jpeg,png",
            ));

            // test with another validator
            /*$row_type_exists = $this->_targetEntityModel
                ->select($this->_targetEntityModel->primaryKey)
                ->where($ex1Model->primaryKey,"=",$request->{$ex1Model->primaryKey})
                ->where($this->_entityModel->primaryKey,"=",$request->{$this->_entityModel->primaryKey})
                ->whereNull("deleted_at")
                ->get();*/

            $row_type_exists = $this->_model->entityQuery($request->entity_type_id)
                ->where("auth." . $this->_model->primaryKey, "=", $request->{$this->_model->primaryKey})
                ->select("entity." . $this->_targetEntityModel->primaryKey)
                //->where("auth.is_verified","=",1)
                ->get();

            $exists_id = isset($row_type_exists[0]) ? $row_type_exists[0]->{$this->_targetEntityModel->primaryKey} : 0;
            $prevent_process = $exists_id > 0 ? 1 : $prevent_process;


        } else {

            $rules = array(
                $this->_entityTypeModel->primaryKey => 'required|integer|exists:' . $this->_entityTypeModel->table . "," . $this->_entityTypeModel->primaryKey . ",allow_auth,1,deleted_at,NULL",
//			'role_id' => 'required|int|exists:' . $ex2Model->table . "," . $ex2Model->primaryKey . ",deleted_at,NULL",
                //"entity_id"=>"required|int",
                //'type' => 'string|in:' . config("constants.ALLOWED_ENTITY_TYPES"),
               // 'email' => 'email|required|unique:' . $this->_model->table . ',email,NULL,entity_auth_id,is_verified,1,deleted_at,NULL',
                'email' => 'email|required',
                'password' => 'required|min:6',
                'has_temp_password' => 'integer|in:0,1',
                'mobile_no' => 'string|required_without_all:email|min:6|max:13',
                'role_id' => 'required',
                //'first_name' => 'required',
                //'last_name' => 'required',
                //"dob" => "date_format:Y-m-d",
                "country_id" => "integer|exists:country,country_id",
                "state_id" => "integer|exists:state,state_id",
                //"zip_code" => "required",
                //'gender' => 'in:' . config("constants.ALLOWED_GENDERS"),
                'platform_type' => 'string|in:' . config("constants.SOCIAL_PLATFORM_TYPES"),
                'platform_id' => 'string',
                'device_type' => 'required|in:' . config("constants.DEVICE_TYPES"),
                //'device_udid' => 'required',
                $fileIndex => "mimes:jpg,jpeg,png",
            );

            $error_messages = array("role_id.required" => "Designation field is required");

            $validation_rules = $rules;

            //check if entity type is business user then validate role_parent
            if (in_array($this->_entityTypeData->identifier,array('business_user','driver'))) {
                $rules['email'] = 'email|required|unique:' . $this->_model->table . ',email,NULL,'. $this->_model->primaryKey .',deleted_at,NULL';
               // $rules['email'] = 'email|required';
                $rules['mobile_no'] = 'required|mobile|unique:' . $this->_model->table .',mobile_no,NULL,'. $this->_model->primaryKey .',deleted_at,NULL';

                $error_messages["mobile_no.unique"] = "The Mobile no already taken";

                if ($this->_entityTypeData->identifier == "business_user") {

                    $validation_rules = array_merge(array('parent_role_id' => "required"), $rules);

                    $error_messages["parent_role_id.required"] = "Department field is required";
                    //Generate password randomly
                     $request->request->add(['password' => str_random(8)]);
                   //  $request->request->add(['password' => '12345678']);
                }

            }

            // validations
            $validator = Validator::make($request->all(), $validation_rules, $error_messages);

            $query = $this->_model->entityQuery($request->{$this->_entityTypeModel->primaryKey})
                ->where("auth.is_verified", "=", 1);

            if ($request->email != "") {
                $e_query = $this->_model->entityQuery($request->{$this->_entityTypeModel->primaryKey})
                    ->where("auth.is_verified", "=", 1)
                    //->where("entity_type_id", "=", $request->entity_type_id)
                    ->where("auth.email", "=", $request->email);

                $prevent_process = $e_query->count() > 0 ? 1 : 0;
                $prevent_field = "Email";
                // add into main query too
                $query->where("auth.email", "=", $request->email);
                $login_type = "email";

            }

            if ($request->mobile_no != "") {
                if ($prevent_process == 0) {
                    $m_query = $this->_model->entityQuery($request->{$this->_entityTypeModel->primaryKey})
                        ->where("auth.is_verified", "=", 1)
                        //->where("entity_type_id", "=", $request->entity_type_id)
                        ->where("auth.mobile_no", "=", $request->mobile_no);
                    $prevent_process = $m_query->count() > 0 ? 1 : 0;
                    $prevent_field = "Mobile #";
                }
                // add into main query too
                $query->where("auth.mobile_no", "=", $request->mobile_no);
                $login_type = "mobile";
            }

            $row_type_exists = $query->get();

            $exists_id = isset($row_type_exists[0]) ? $row_type_exists[0]->{$this->_model->primaryKey} : 0;
        }


        $prevent_field = isset($prevent_field) ? $prevent_field : $this->_entityTypeData->title;
        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } elseif ($e_type_att_exists == 0) {
            $this->_apiData['message'] = trans('api_errors.entity_does_not_exists', array("entity" => "Entity type attributes"));
        } elseif ($request->mobile_no != "" && !preg_match("/-/", $request->mobile_no)) {
            $this->_apiData['message'] = trans('api_errors.entity_is_invalid', array("entity" => "Mobile no"));
        } else if ($request->platform_type != "custom" && $request->platform_id == "") {
            $this->_apiData['message'] = trans('api_errors.entity_is_required', array("entity" => "Platform id"));
        } else if (isset($e_query) && ($exists_id > 0 && $e_query->count() > 0)) {
            $this->_apiData['message'] = trans('api_errors.entity_already_exists', array("entity" => "Email"));
        } else if (isset($m_query) && ($exists_id > 0 && $m_query->count() > 0)) {
            $this->_apiData['message'] = trans('api_errors.entity_already_exists', array("entity" => "Mobile no"));
        } else if (($request->is_auth_exists == 0) && ($exists_id > 0 || $prevent_process > 0)) {
            $this->_apiData['message'] = trans('api_errors.entity_already_exists', array("entity" => $prevent_field));
        } else {


            if((isset($request->entity_type_id) && !empty($request->entity_type_id) && (isset($request->email) && !empty($request->email)))){
                $exist_department = $this->_model->checkUserInOtherDepartment($request->entity_type_id,$request->email);
                if($exist_department && isset($exist_department->title)){
                    $this->_apiData['message'] = trans('api_errors.email_already_exist_other_department',array('department' => $exist_department->title ));
                    return $this->__ApiResponse($request, $this->_apiData);
                }
               // print_r($exist_department); exit;
            }


            //After auth validation check entity attributes validation, if ok then proceed
            $exModel = $this->_targetEntityModel;

            $entityAttributeModel = $this->_modelPath . "SYSEntityAttribute";
            $entityAttributeModel = new $entityAttributeModel;
            $listOfAttributeToBeValidate = $entityAttributeModel->getEntityAttributeValidationList($request->entity_type_id, '');
            $response_validator = $this->_postValidator($request->all(), $listOfAttributeToBeValidate);
            if ($response_validator) {
                return $this->_apiData;
            }


            $func = CustomHelper::convertToCamel($this->_entityTypeData->identifier . '_verify_trigger');
            $obj = new EntityTrigger();
            if (method_exists($obj, "$func")) {
                $verify_response = $obj->$func($request->all());
                if ($verify_response['error'] == true) {
                    $this->_apiData['response'] = 'error';
                    $this->_apiData['message'] = $verify_response['message'];
                    return $this->__ApiResponse($request, $this->_apiData);
                }
            }

            //Check if entity auth id exist with department then show error
            if ($request->is_auth_exists > 0) {
                $ex1Model = $this->_entityTypeModel;

                $entity_id = $exModel->getEntityByAuthAndEntityType($request->{$this->_entityModel->primaryKey}, $request->{$ex1Model->primaryKey});
                if ($entity_id) {
                    $this->_apiData['message'] = trans('api_errors.auth_department_already_exist');
                    return $this->__ApiResponse($request, $this->_apiData);
                }
            }

            // SK logic STARTS
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // create auth data if not available
            if ($request->is_auth_exists == 0) {
                // init data
                $entity = array();
                // set data
                // optional params if available
                if (isset($optionalFields[0])) {
                    foreach ($optionalFields as $optionalField) {
                        if ($request->input($optionalField, "") != "") {
                            $entity[$optionalField] = $request->{$optionalField};
                        }
                    }
                }

                // set name
                $entity["name"] = $request->input("first_name");

                if(isset($request->last_name) && !empty($request->last_name)){
                    $entity["name"] .= ' '.$request->input("last_name");
                }

                $entity['email'] = $request->input("email");
                $entity['password'] = $request->input("password");
                $entity['mobile_no'] = str_replace("+", "", $request->input("mobile_no"));
                $entity['has_temp_password'] = $request->has_temp_password;

                $entity['status'] = $request->input("user_status", 0);
                if(intval($entity['status']) == 1){
                    $entity['status'] = 1;
                    $entity['is_verified'] = 1;
                    $entity['verified_at'] = date('Y-m-d');
                    $entity['verification_token'] = NULL;

                }

                // if has file
                if ($request->hasFile($fileIndex)) {
                    // path/file name
                    $dirPath = config($this->_entityConfFile . ".DIR_IMG");
                    $fileName = "t-" . str_replace(".", "-", microtime(true));
                    //$fileName .= "." . $request->file($fileName)->getClientOriginalExtension();
                    $fileName .= ".jpg";
                    $thumbName = "thumb-" . $fileName;

                    // save file in entity dir (create dir if not exists)
                    if (!is_dir($dirPath)) {
                        mkdir(@$dirPath, 0777, true);
                    }
                    //create file
                    $request->file($fileIndex)->move($dirPath, $fileName);

                    // if dp created successfully, create thumbnail
                    $thumbName = "thumb-" . $fileName;
                    $thumbData = file_get_contents(url("/") . "/" . "thumb/" . base64_encode($dirPath) . "/150x150/" . $fileName . "/" . $thumbName);

                    // set db data
                    $entity["image"] = $fileName;
                    $entity["thumb"] = $thumbName;
                }

                //$entity['status'] = 1; // temp
                // process signup
                $insert_data = $this->_model->signup($entity,$request->entity_type_id);
                unset($entity);

                $request->merge(array($this->_model->primaryKey => $insert_data->{$this->_model->primaryKey}));
            } else {
                //get entity auth data if using existing auth
                $insert_data = $this->_entityModel->get($request->{$this->_entityModel->primaryKey});
                $success_message = trans('api_errors.user_created_successfully');
            }

            // mobile call ?
            //$request->merge(array('mobile_json' => 0));
            if(isset($request->user_status)){
                $request->request->add(['status' => $request->user_status]);
            }else{
                $request->request->add(['status' => 0]);
            }


           // $apiCurl = new ApiCurl();
           // $ret = CustomHelper::internalCall($request, \URL::to(DIR_API) . '/system/entities', 'POST', $request->all());
           $entity_lib = new Entity();
            $ret = $entity_lib->apiPost($request->all());
            $ret = json_decode(json_encode($ret));

            if (isset($ret)) {

                if ($ret->error == "1") {
                    $this->_apiData['response'] = 'error';
                    $this->_apiData['message'] = $ret->message;
                } else {
                    //Insert entity role map
                    if (isset($ret->data->{$ret->data->identifier}->{$this->_targetEntityModel->primaryKey})) {

                        $entity_id = $ret->data->{$ret->data->identifier}->{$this->_targetEntityModel->primaryKey};
                        $role_map_model = $this->_modelPath . "SYSEntityRoleMap";
                        $role_map_model = new $role_map_model;

                        $role_map_model->InsertRoleEntity($request->input("role_id"), $entity_id);
                    }

                    // get entity data
                    $entity_lib = new Entity();
                    $entity = $entity_lib->getData($ret->data->{$ret->data->identifier}->entity_id, $request->all());
                    //$entity = $this->_targetEntityModel->getData($ret->data->{$ret->data->identifier}->entity_id);
                    /*$entity = $this->_model->getData(
                        $insert_data->{$this->_model->primaryKey},
                        $request->{$this->_entityTypeModel->primaryKey}
                    );*/

                    // msg
                    $this->_apiData['message'] = $ret->message;

                    // if entity newly created
                    if (isset($insert_data)) {
                        //$entity->sent_email_verification = $insert_data->sent_email_verification;
                        //$entity->sent_mobile_verification = $insert_data->sent_mobile_verification;

                        // ovverite msg
                        $this->_apiData['message'] = trans('api_errors.check_email_for_confirmation');
                    }

                    // response data
                    $data[$this->_objectIdentifier] = $entity;
                }
            }


            // assign to output
            $this->_apiData['data'] = $data;

            // SK logic ENDS

        }

        return $this->__ApiResponse($request, $this->_apiData);
    }


    /**
     * confirm Signup
     *
     * @return Response
     */
    public function confirmSignup(Request $request)
    {
        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));


        $verification_token = trim($request->input('verification_token', ''));
        $login_id = trim($request->input('login_id', ''));
        $verification_mode = preg_match("/@/", $login_id) ? "email" : "mobile_no";
        // validation rules
        $validation_rules = array('login_id' => 'required|string');
        $validation_rules["verification_token"] = 'required|string';


        // if sms signup enabled
        if (config($this->_config_dir . '.SMS_SIGNUP_ENABLED') && !preg_match("/@/", $login_id)) {
            // validations
            //$validation_rules["verification_token"] = 'required|string';
            $validator = Validator::make($request->all(), $validation_rules);
            $login_id = str_replace("+", "", $login_id);

            $row_type_exists = $this->_model
                ->where('auth.mobile_no', '=', $login_id)
                ->where('verification_token', '=', $verification_token)
                //->where('status', '=', 0)
                ->whereNull("deleted_at")
                ->get(array($this->_model->primaryKey));

        } else {
            // validations
            $validation_rules["login_id"] = 'required|email'; // should be email
            $validator = Validator::make($request->all(), $validation_rules);

            $row_type_exists = $this->_model
                ->where('email', '=', $login_id)
                ->where('verification_token', '=', $verification_token)
                //->where('status', '=', 0)
                ->whereNull("deleted_at")
                ->get(array($this->_model->primaryKey));
        }

        $exists_id = isset($row_type_exists[0]) ? $row_type_exists[0]->{$this->_model->primaryKey} : 0;
        $entity = $this->_model->get($exists_id);

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else if (!$entity) {
            $this->_apiData['message'] = trans('api_errors.invalid_record_request');
        } elseif ($entity->is_verified == 1) {
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.account_already_verified');
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // get data
            $entity = $this->_model->get($exists_id);


            // set data
            //$entity->status = 1;
            if (config($this->_config_dir . '.SMS_SIGNUP_ENABLED') && $verification_mode == "mobile_no") {
                $entity->is_mobile_verified = 1;
            } else {
                $entity->is_email_verified = 1;
            }

            /*// find other accounts with this mobile number/email, which are not verified (we assume those are junk accounts now)
            $query = $this->_model
                ->where("is_verified", "!=", 1)
                ->where($this->_model->primaryKey, "!=", $entity->{$this->_model->primaryKey})
                ->whereNull("deleted_at");
            if ($verification_mode == "email") {
                $query->where("email", "=", $login_id);
            } else {
                $query->where("mobile_no", "=", $login_id);
            }
            $raw_ids = $query->get();
            // if found, remove
            if (isset($raw_ids[0])) {
                foreach ($raw_ids as $raw_id) {
                    $this->_model->remove($raw_id->{$this->_model->primaryKey});
                }
            }*/
            $this->_model->removeUnverified($entity, $login_id, $verification_mode);

            //update entity type

            // welcome user
            $this->_model->welcome($entity);

            $this->_apiData['message'] = trans('api_errors.account_verification_success');

            //Call trigger for confirm signup
            $entity_auth_trigger = new EntityAuthTrigger();
            $trigger_func =  __FUNCTION__."AfterTrigger";
            if (method_exists($entity_auth_trigger, "$trigger_func")) {
                //prams for trigger
                $trigger_params['entity_auth_id'] = $exists_id;
                $trigger_params['entity_type_id'] = $request->entity_type_id;

                $trigger_response = $entity_auth_trigger->$trigger_func($trigger_params);

                if($trigger_response->error == 1){
                    $this->_apiData['message'] = $trigger_response->message;
                }
            }

            // response data
            $data[$this->_objectIdentifier] = $this->_model->getData($exists_id, true);

//            $this->_apiData['message'] = trans('api_errors.your_account_is_activated');

            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }


    /**
     * confirm forgot
     *
     * @return Response
     */
    public
    function xconfirmForgot(Request $request)
    {
        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));


        $verification_token = trim($request->input('verification_token', ''));
        $login_id = trim($request->input('login_id', ''));
        $verification_mode = preg_match("/@/", $login_id) ? "email" : "mobile_no";
        // validation rules
        $validation_rules = array('login_id' => 'required|string');
        $validation_rules["verification_token"] = 'required|string';

        // validations
        $validation_rules["login_id"] = 'required|email'; // should be email
        $validator = Validator::make($request->all(), $validation_rules);

        $row_type_exists = $this->_model
            ->where('email', '=', $login_id)
            ->where('verification_token', '=', $verification_token)
            //->where('status', '=', 0)
            ->whereNull("deleted_at")
            ->get(array($this->_model->primaryKey));

        $exists_id = isset($row_type_exists[0]) ? $row_type_exists[0]->{$this->_model->primaryKey} : 0;
        $entity = $this->_model->get($exists_id);

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else if (!$entity) {
            $this->_apiData['message'] = trans('api_errors.invalid_record_request');
        } elseif ($entity->is_verified == 1) {
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.account_already_verified');
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // get data
            $entity = $this->_model->get($exists_id);


            // set data
            //$entity->status = 1;
            if (config($this->_config_dir . '.SMS_SIGNUP_ENABLED') && $verification_mode == "mobile_no") {
                $entity->is_mobile_verified = 1;
            } else {
                $entity->is_email_verified = 1;
            }

            // find other accounts with this mobile number/email, which are not verified (we assume those are junk accounts now)
            $query = $this->_model
                ->where("is_verified", "!=", 1)
                ->where($this->_model->primaryKey, "!=", $entity->{$this->_model->primaryKey})
                ->whereNull("deleted_at");
            if ($verification_mode == "email") {
                $query->where("email", "=", $login_id);
            } else {
                $query->where("email", "=", $login_id);
            }
            $raw_ids = $query->get();
            // if found, remove
            if (isset($raw_ids[0])) {
                foreach ($raw_ids as $raw_id) {
                    $this->_model->remove($raw_id->{$this->_model->primaryKey});
                }
            }
            // welcome user
            $this->_model->welcome($entity);

            // response data
            $data[$this->_objectIdentifier] = $this->_model->getData($exists_id, true);

//            $this->_apiData['message'] = trans('api_errors.your_account_is_activated');
            $this->_apiData['message'] = trans('api_errors.account_verification_success');

            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * Login User
     *
     * @return Response
     */
    public function login(Request $request)
    {
        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));

        $identity = trim(str_replace(" ", "", strip_tags($request->input('login_id', ''))));

        $login_type = "email";

        // if found "@" sign
        if (config($this->_config_dir . '.SMS_SIGNUP_ENABLED') && !preg_match("/@/", $identity)) {
            $login_type = "mobile_no";
            $request->login_id = str_replace("+", "", $request->login_id);
            $field_name = 'Mobile Number';

            $error_messages = array(
                'login_id.required' => 'The '.$field_name.' field is required',
                'login_id.int' => 'The '.$field_name.' field is invalid',
                'login_id.exists' => 'The '.$field_name.' field does not exist',
            );

            // validations
            $validator = Validator::make($request->all(), array(
                $this->_entityTypeModel->primaryKey => "int|exists:" . $this->_entityTypeModel->table, "deleted_at,NULL",
                'login_id' => 'required',
                'password' => 'required',
                'platform_type' => 'custom',
                'device_type' => 'required|in:' . config("constants.DEVICE_TYPES")
            ),$error_messages);
            $auth = $this->_model->checkLogin($request->login_id, $request->password, $login_type, $request->entity_type_id);
        }
        else {

            $login_type = "email";
            $field_name = 'Email';

            $error_messages = array(
                'login_id.required' => 'The '.$field_name.' field is required',
                'login_id.email' => 'The '.$field_name.' must be a valid email address',
            );

            // validations
            $validator = Validator::make($request->all(), array(
                $this->_entityTypeModel->primaryKey => "int|exists:" . $this->_entityTypeModel->table, "deleted_at,NULL",
                'login_id' => 'required|email',
                'password' => 'required',
                'platform_type' => 'custom',
                'device_type' => 'required|in:' . config("constants.DEVICE_TYPES")
            ),$error_messages);
            $auth = $this->_model->checkLogin($request->login_id, $request->password, $login_type, $request->entity_type_id);
        }

        // optional fields
        $optionalFields = array(
            "device_udid",
            "device_token"
        );


        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } elseif (strlen($request->login_id) < 6 || strlen($request->password) < 6) {
            // message
            //$this->_apiData['message'] = trans('api_errors.invalid_entity_request', array("entity" => "Login"));
            $this->_apiData['message'] = trans('api_errors.entity_is_incorrect', array("entity" => str_replace("_", " ", ucfirst($login_type)) . " or Password"));
        } elseif ($auth === FALSE) {
            // kick user
            //$this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.entity_is_incorrect', array("entity" => str_replace("_", " ", ucfirst($login_type)) . " or password"));
        } elseif ($auth->status == 0) {
            // kick user
            //$this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.your_account_is_inactive', array("entity" => "inactive"));
        } elseif ($auth->deleted_at !== NULL || $auth->status > 1) {
            // kick user
            //$this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.your_account_is_baned_removed');
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // get entity id
            $raw_entity = $this->_model
                ->entityQuery($request->{$this->_entityTypeModel->primaryKey})
                ->where('auth.' . $this->_model->primaryKey, $auth->{$this->_model->primaryKey})
                ->select($this->_targetEntityModel->primaryKey)
                ->first();

            // set data
            /*// optional params if available
            if (isset($optionalFields[0])) {
                foreach ($optionalFields as $optionalField) {
                    if ($request->input($optionalField, "") != "") {
                        $entity[$optionalField] = $request->{$optionalField};
                    }
                }
            }*/
            $first_login = false;
            if($auth->last_login_at == ''){
                $first_login = true;
            }

            $auth->last_login_at = date("Y-m-d H:i:s");
            $auth->device_type = $request->device_type;
            $auth->device_token = $request->device_token;
            $this->_model->set($auth->{$this->_model->primaryKey}, (array)$auth);

            //if entity type is driver then do
            //Call trigger for confirm signup
            $entity_auth_trigger = new EntityAuthTrigger();
            $trigger_func =  __FUNCTION__."AfterTrigger";
            if (method_exists($entity_auth_trigger, "$trigger_func")) {

                $trigger_response = $entity_auth_trigger->$trigger_func($this->_entityTypeData,$raw_entity->{$this->_targetEntityModel->primaryKey},$first_login);

                if($trigger_response->error == 1){
                    $this->_apiData['message'] = $trigger_response->message;
                }
            }

            $entity = $this->_targetEntityModel->getData($raw_entity->{$this->_targetEntityModel->primaryKey},$request->all());

            // get data into array
            $update_entity = array("entity_auth_id"=>$entity->auth->entity_auth_id);

            if(isset($entity->auth->email) && !empty($entity->auth->email)){
                //if($entity->is_email_verified==1){
                if(empty($entity->auth->customer_id)){
                    $CustomerPara['entity_auth_id'] = $entity->entity_auth_id;
                    $CustomerPara['email'] = $entity->auth->email;

                    $StripeService = $this->_StripeLib->addCustomer($CustomerPara);
                    if(isset($StripeService['response']['id'])){
                        $update_entity['customer_id']  = $StripeService['response']['id'];
                    }
                }
             /*   if(empty($entity->auth->account_id)){
                    $StripeService = $this->_StripeLib->addAccount($entity->entity_id, $entity->auth->email);

                    if (isset($StripeService['response']['id'])) {
                        $account_id = $StripeService['response']['id'];
                        $update_entity['account_id']= $account_id;
                    }
                }*/
                //}
            }


           // $entity = $update_entity;
            // set other params
            $update_entity["last_login_at"] = date("Y-m-d H:i:s");
            $update_entity["deleted_at"] = NULL; // reactivate

            // process signup
            $this->_model->set($entity->auth->entity_auth_id, $update_entity);

            // load / init models
            $entity_history_model = $this->_modelPath . "SYSEntityHistory";
            $entity_history_model = new $entity_history_model;
            // set data for history
            $actor_entity = $this->_entityIdentifier;
            $actor_id = $entity->{$this->_targetEntityModel->primaryKey};
            $identifier = "signin";
            // other data
            $other_data["navigation_type"] = $identifier;

            //$entity_history_model->putEntityHistory($actor_entity, $actor_id, $identifier, $other_data, $this->_plugin_identifier);

            // response data
            $data[$this->_objectIdentifier] = $entity;

            // generate and assign new oAuth Token, remove old tokens
            // load / init models
            $api_token_model = $this->_modelPath . "ApiToken";
            $api_token_model = new $api_token_model;
            $data["client_token"] = $api_token_model->generate($request->{$this->_entityTypeModel->primaryKey}, $actor_id, true);

            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }


    /**
     * forgot password
     *
     * @return Response
     */
    public function forgotPasswordRequest(Request $request)
    {
        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));

        $identity = trim(str_replace(" ", "", strip_tags($request->input('login_id', ''))));

        // validations
        $validator = Validator::make($request->all(), array(
            //'type' => 'string|in:' . config("constants.ALLOWED_ENTITY_TYPES"),
            'entity_type_id' => 'required|int|exists:' . $this->_entityTypeModel->table
                . ',' . $this->_entityTypeModel->primaryKey . ',deleted_at,NULL',
            'entity_id' => 'required_without:entity_type_id|int|exists:'
                . $this->_targetEntityModel->table . ','
                . $this->_targetEntityModel->primaryKey . ',deleted_at,NULL',
            'login_id' => 'required|string'
        ));
        // default entity data
        $entity = NULL;
        // $request->type = $request->input("type", "") == "" ? explode(",", config("constants.ALLOWED_ENTITY_TYPES"))[0] : $request->type;

        // id type
        $id_type = "email";

        // if entity type is not provided
        if (!$request->input('entity_type_id', null)) {
            $entity = $this->_targetEntityModel->get($request->entity_id);
            // if we get valid entity
            if ($entity)
                $request->merge(['entity_type_id' => $entity->entity_type_id]);
        }

        if (!is_numeric(trim($request->entity_type_id))) {
            $exModel = $this->_entityTypeModel;
            $entityTypeData = $exModel->getBy('identifier', $request->entity_type_id);
            if ($this->_entityTypeData) $request->entity_type_id = $entityTypeData->entity_type_id;
        }


        // if "@" sign not found
        if (config($this->_config_dir . '.SMS_SIGNUP_ENABLED') && !preg_match("/@/", $identity)) {

            $identity = str_replace("+", "", $identity);

           /* $query = $this->_model->where("mobile_no", "=", $identity)
                ->where("entity_type_id", "=", $request->entity_type_id)
                ->where("is_verified", "=", 1)
                ->whereNull("deleted_at");
            $raw_id = $query->first();*/

            $row_type_exists = $this->_model->entityQuery($request->entity_type_id)
                ->select('auth.'.$this->_model->primaryKey)
                ->where('auth.mobile_no', $identity)
                ->where('auth.is_verified',1)
                ->where('auth.entity_auth_id','>', 0)
                ->whereNull('auth.deleted_at')
                ->whereNull('entity.deleted_at')
                ->get();

            $raw_id = isset($row_type_exists[0]) ? $row_type_exists[0]->{$this->_model->primaryKey} : 0;
          //  $raw_id = isset($raw_id->{$this->_model->primaryKey}) ? $raw_id->{$this->_model->primaryKey} : 0;

            $entity = $this->_model->get($raw_id);

            // id type
            $id_type = "mobile_no";

        }

        // if found "@" sign
        if (config($this->_config_dir . '.EMAIL_SIGNUP_ENABLED') && preg_match("/@/", $identity)) {
            // validations
        /*    $raw_id = $this->_model->where("email", "=", $identity)
                //->where("type", "=", $request->type)
                ->where("is_verified", "=", 1)
                ->whereNull("deleted_at")
                ->first();*/

            $row_type_exists = $this->_model->entityQuery($request->entity_type_id)
                ->select('auth.'.$this->_model->primaryKey)
                ->where('auth.email', "$identity")
                ->where('auth.is_verified',1)
                ->where('auth.entity_auth_id','>', 0)
                ->whereNull('auth.deleted_at')
                ->whereNull('entity.deleted_at')
                ->get();

            $raw_id = isset($row_type_exists[0]) ? $row_type_exists[0]->{$this->_model->primaryKey} : 0;
            //$raw_id = isset($raw_id->{$this->_model->primaryKey}) ? $raw_id->{$this->_model->primaryKey} : 0;
            $entity = $this->_model->get($raw_id);
            // id type
            $id_type = "email";
        }


        // validate
        /*if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else*/
        if (!$entity) {
            // kick user
            //$this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.entity_does_not_exists', array("entity" => str_replace("_", " ", ucfirst($id_type))));
        } /*elseif ($entity->status == 0) {
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.your_account_is_inactive', array("entity" => "inactive"));
        }*/ elseif ($entity->status > 1) {
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.your_account_is_baned_removed');
        }
        elseif ($entity->platform_type == 'facebook') {
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = 'Sorry, your account is registered from facebook';
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // generate forgot password token
            $entity = $this->_model->forgotPasswordRequest($entity, $id_type, $request->entity_type_id);


           /* $entity_data = array(
                "email" => $entity->email,
                "is_email_verified" => $entity->is_email_verified,
                "mobile_no" => $entity->mobile_no,
                "is_mobile_verified" => $entity->is_mobile_verified,
                //"forgot_password_token" => $entity->forgot_password_token,
                //$this->_model->primaryKey => $entity->{$this->_model->primaryKey}
                "verification_token" => $entity->verification_token,
                "sent_email_verification" => $entity->sent_email_verification,
                "sent_mobile_verification" => $entity->sent_mobile_verification
            );*/


            // send entity data
            //$data[$this->_objectIdentifier] = $entity_data;
            $data[$this->_objectIdentifier] = $entity;

            $this->_apiData['message'] = trans('api_errors.check_email_for_confirmation');

            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }

    public function verifyForgotCode(Request $request)
    {

        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));


        $login_id = $request->input('login_id', '');
        $verification_token = trim($request->input('verification_token', ''));

        $verification_type = preg_match("/@/", $login_id) ? "email" : "mobile_no";

        // validations
        $validator = Validator::make($request->all(), array(
            'login_id' => 'required',
            'verification_token' => 'required',
            'new_password' => 'required|string|min:6'
        ));


        $row_type_exists = $this->_model->checkUser($login_id, $verification_type, $verification_token);
        $exists_id = isset($row_type_exists[0]) ? $row_type_exists[0]->{$this->_model->primaryKey} : 0;

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else if ($exists_id == 0) {

            $this->_apiData['message'] = trans('api_errors.invalid_record_request');
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // set data
            $entity = (array)$this->_model->get($exists_id);
            // entity_id
            $entity_id = $entity[$this->_model->primaryKey];

            // set entity data
            $entity["verification_token"] = NULL;
            $entity["password"] = $request->new_password;


            // set new password
            $new_password = $this->_model->changePassword($entity);


            // load / init models
            $entity_history_model = $this->_modelPath . "SYSEntityHistory";
            $entity_history_model = new $entity_history_model;
            // set data for history
            $actor_entity = $this->_entityIdentifier;
            $actor_id = $entity_id;
            $identifier = "forgot_password_success";
            // other data
            $other_data["navigation_type"] = $identifier;

            //$entity_history_model->putEntityHistory($actor_entity, $actor_id, $identifier, $other_data, $this->_plugin_identifier);

            // msg
            $this->_apiData['message'] = trans("api_errors.check_email_for_new_password", array("ex_text" => ""));

            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);

    }

    public function resetPassword(Request $request)
    {

        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));

        $verification_token = $request->verification_token;
        // validations
        $validator = Validator::make($request->all(), array(
            //"mobile_no" => "required|string",
            'verification_token' => 'required|string',
            'new_password' => 'required|string|min:6',
        ));

        $query = $this->_model
            //->where('mobile_no', '=', $request->mobile_no)
            ->where('verification_token', '=', $request->verification_token)
            ->where('is_verified', '=', 1)
            ->whereNull("deleted_at")
            ->get();
        $exists_id = $query->first();

        $entity_id = isset($exists_id->{$this->_model->primaryKey}) ? $exists_id->{$this->_model->primaryKey} : 0;
        $entity = $this->_model->get($entity_id);


        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } elseif (!$entity) {
            // kick user
            //$this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.invalid_entity_request', array("entity" => $this->_objectIdentifier));
        } elseif ($entity->status == 0) {
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.your_account_is_inactive', array("entity" => "inactive"));
        } elseif ($entity->status > 1) {
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.your_account_is_baned_removed');
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // new password
            $entity->password = $this->_model->saltPassword($request->new_password);
            // reset token
            $entity->forgot_password_token = $entity->verification_token = NULL;
            $entity->mobile_verification_code = NULL;
            $entity->forgot_password_token_created_at = NULL;

            // update user data
            $this->_model->set($entity->{$this->_model->primaryKey}, (array)$entity);
            // unset
            unset($entity);

            // load / init models
            $entity_history_model = $this->_modelPath . "SYSEntityHistory";
            $entity_history_model = new $entity_history_model;
            // set data for history
            $actor_entity = $this->_entityIdentifier;
            $actor_id = $entity_id;
            $identifier = "reset_password";
            // other data
            $other_data["navigation_type"] = $identifier;

            //$entity_history_model->putEntityHistory($actor_entity, $actor_id, $identifier, $other_data, $this->_plugin_identifier);

            // get data
            $entity = $this->_model->getData($entity_id, true);

            // send entity data
            $data[$this->_entityIdentifier] = $entity;


            $this->_apiData['message'] = trans('api_errors.check_email_for_confirmation');

            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * Change Password
     *
     * @return Response
     */
    public function changePassword(Request $request)
    {    // trim/escape all


        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));

        //
        // ex model
        $exModel = $this->_modelPath . "SYSEntity";
        $exModel = new $exModel;

        // validations
        $validator = Validator::make($request->all(), array(
            $exModel->primaryKey => "required|exists:" . $exModel->table . "," . $exModel->primaryKey,
            "current_password" => "required|min:6",
            "new_password" => "required|min:6|max:32",
            "confirm_password" => "required|min:6|max:32|same:new_password",
        ));

        // get data
        $entity = $this->_targetEntityModel->where($exModel->primaryKey, "=", $request->{$exModel->primaryKey})->whereNull("deleted_at")->first();

        $exists = $this->_model->where($this->_model->primaryKey, "=", $entity->{$this->_model->primaryKey})
            ->where("password", "=", $this->_model->saltPassword($request->current_password))
            ->whereNull("deleted_at")
            ->count();

        $entity_exists = $this->_targetEntityModel->getData($entity->{$this->_targetEntityModel->primaryKey}, $request->{$this->_entityTypeModel->primaryKey}, true);

        // validations
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else if ($request->{$exModel->primaryKey} == 0) {
            $this->_apiData['message'] = trans('api_errors.pls_enter_entity_id', array("entity" => $this->_objectIdentifier));
        } else if ($entity == "") {

            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.your_account_is_inactive', array("entity" => "inactive"));
        } else if ($exists === FALSE) {

            $this->_apiData['message'] = trans('api_errors.invalid_record_request');
        } elseif ($entity_exists->auth->status == 0) {
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.your_account_is_inactive', array("entity" => "inactive"));
        } elseif ($entity_exists->auth->status > 1) {
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.your_account_is_baned_removed');
        } elseif ($exists == 0) {
            // message
            $this->_apiData['message'] = trans('api_errors.entity_is_invalid', array("entity" => "Current password"));
        } else {
            // init models
            // init models
            //$this->__models['predefined_model'] = new Predefined;

            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // exlude eloquent data
            //$entity = json_decode(json_encode($entity_exists->auth));
            $entity = (object)array();
            // entity_id
            $entity_id = $entity_exists->auth->{$this->_model->primaryKey};

            $entity->password = $this->_model->saltPassword($request->new_password);
            // update user data
            $this->_model->set($entity_exists->auth->{$this->_model->primaryKey}, (array)$entity);

            $entity = $this->_targetEntityModel->getData($entity_exists->{$this->_targetEntityModel->primaryKey}, $request->all());

            // load / init models
            $entity_history_model = $this->_modelPath . "SYSEntityHistory";
            $entity_history_model = new $entity_history_model;
            // set data for history
            $actor_entity = $this->_entityIdentifier;
            $actor_id = $entity_exists->{$this->_targetEntityModel->primaryKey};
            $identifier = "change_password";
            // other data
            $other_data["navigation_type"] = $identifier;

            //$entity_history_model->putEntityHistory($actor_entity, $actor_id, $identifier, $other_data, $this->_plugin_identifier);

            // get user data
            $data[$this->_objectIdentifier] = $entity;
            // generate and assign new oAuth Token, remove old tokens
            // load / init models
            $api_token_model = $this->_modelPath . "ApiToken";
            $api_token_model = new $api_token_model;
            $data["client_token"] = $api_token_model->generate($request->{$this->_entityTypeModel->primaryKey}, $entity_exists->{$this->_targetEntityModel->primaryKey}, true);


            // message
            $this->_apiData['message'] = trans('api_errors.success');

            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__ApiResponse($request, $this->_apiData);
    }


    /**
     * User Listing
     *
     * @return Response
     */
    public function listing(Request $request)
    {
        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));

        // success response
        $this->_apiData['response'] = "success";
        $this->_apiData['data'] = $data = array();
        $data[$this->_objectIdentifier] = array();

        $page = trim($request->input("page_no"));
        $page = is_numeric($page) ? $page : 1;
        $limit = PAGE_LIMIT_API;
        $offset = (($page - 1) * $limit);
        // get user data
        $users = $this->_model
            ->whereNull("deleted_at")
            ->where("is_verified", "=", 1)
            ->limit($limit)
            ->offset($offset)
            ->orderBy('created_at', 'DESC')
            ->get();

        foreach ($users as $user) {
            $data[$this->_objectIdentifier][] = $this->_model->getData($user->entity_auth_id, true);
        }
        $data['page']['page_limit'] = PAGE_LIMIT_API;
        $data['page']['current_page'] = (int)$page;
        $data['page']['total_records'] = $this->_model
            ->whereNull("deleted_at")
            ->where("is_verified", "=", 1)
            ->count();


        //$data[$this->_objectIdentifier] = $this->_model->getData($entity->{$this->_model->primaryKey}, true);

        // message
        $this->_apiData['message'] = trans('api_errors.success');

        // assign to output
        $this->_apiData['data'] = $data;

        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * User Social Login
     *
     * @return Response
     */

    public function socialLogin(Request $request)
    {
        // trim/escape all
     /*   $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));*/


        $email = trim(str_replace(" ", "", strip_tags($request->input('email', ''))));

        // optional fields
        $optionalFields = array(
            "email",
            "image",
            "name",
            "device_udid",
            "device_token"
        );

        // validations
        $validator = Validator::make($request->all(), array(
            $this->_entityTypeModel->primaryKey => "int|exists:" . $this->_entityTypeModel->table, "deleted_at,NULL",
            'platform_type' => 'required|in:' . config("constants.SOCIAL_PLATFORM_TYPES"),
            'platform_id' => 'required|min:6',
            'email' => "email",
            //'gender' => 'in:' . config("constants.ALLOWED_GENDERS"),
            'device_type' => 'required|in:' . config("constants.DEVICE_TYPES"),
        ));

        // defaults
        //$request->type = $request->input("type", "") == "" ? explode(",", config("constants.ALLOWED_ENTITY_TYPES"))[0] : $request->type;
        $entity = (object)array();
        // validations
       // print_r($request->platform_id);
        $row_type_exists = $this->_model
            //->where("type", "=", $request->type)
            ->where('platform_type', '=', $request->platform_type)
            ->where('platform_id', '=', $request->platform_id)
            ->first();

      //  print_r($row_type_exists); exit;
        $exists_id = isset($row_type_exists) ? $row_type_exists->{$this->_model->primaryKey} : 0;

        if ($exists_id != 0) {

            $entity = $this->_targetEntityModel
                //->where("type", "=", $request->type)
                ->where('entity_type_id', '=', $request->{$this->_entityTypeModel->primaryKey})
                ->where('entity_auth_id', '=', $exists_id)
                ->whereNull('deleted_at')
                ->first();

            $entity_auth = $this->_model->getData($exists_id, $request->{$this->_entityTypeModel->primaryKey}, TRUE);
        }
        /*if (count($entity) != 0) {
            //$entity = $this->_model->get($exists_id);
            $entity = $this->_targetEntityModel->getData($entity->{$this->_targetEntityModel->primaryKey}, $request->{$this->_entityTypeModel->primaryKey});
        }*/

        //print_r($entity); exit;
        //$entity->auth = $entity_auth;
        // validations
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } /*elseif ($entity === FALSE) {
            // kick user
            //$this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.invalid_entity_request', array("entity" => $this->_objectIdentifier));
        }*/ else if ($entity && (isset($entity_auth) && $entity_auth->status != 1)) {
            // kick user
           // $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.your_account_is_baned_removed');
        } else {

            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // datestamp
            $date_stamp = date("Y-m-d H:i:s");

            // add attributes fields data
            $api_method_field_model = $this->_modelPath . "ApiMethodField";
            $api_method_field_model = new $api_method_field_model;
            $listfields = $api_method_field_model->getEntityAttributeList($request->entity_type_id);

            $_att_dara["entity_type_id"] = $request->entity_type_id;
            $_att_dara["_token"] = $request->_token;
            $save = array(
                /* "entity_type_id" => $request->entity_type_id, */
                "social_email" => $request->email,
                "email" => $request->email,
                "name" => $request->name,
                "platform_type" => $request->platform_type,
                "platform_id" => $request->platform_id,
                "device_type" => $request->device_type,
                "device_token" => $request->input("device_token", ""),
                "created_at" => $date_stamp,
                "status" => 1,
                "is_verified" => 1
            );

           // print_r($entity);
            if ($exists_id == 0) {
               // print_r($entity); exit;
                $_att_dara["created_at"] = date("Y-m-d H:i:s");


                foreach ($listfields as $_field) {
                    if (isset($request->{$_field->attribute_code})) {
                        $_att_dara[$_field->attribute_code] = $request->{$_field->attribute_code};
                    }
                }
                //print_r($_att_dara);die;

                if (isset($ret)) {
                    if ($ret->error == "1") {
                        $this->_apiData['message'] = $ret->message;

                    } else {
                        $entity_id = $ret->data->{$ret->data->identifier}->entity_id;
                    }
                }

                if ($exists_id) {
                    $entity = $this->_model->set($exists_id, $save);
                    $id = $exists_id;

                } else {

                    $id = $this->_model->put($save);

                    $entity_auth = $this->_targetEntityModel->getBy('entity_auth_id', $id);
                  //  echo "<pre>"; print_r( $entity_auth);exit;


                }


               $_att_dara['entity_auth_id'] = $id;
                $_att_dara['user_status'] = 1;
                $_att_dara['is_notify'] = 1;
                $_att_dara['system_notify'] = 1;

                $entity_lib = new Entity();
                $entity_response = $entity_lib->apiPost($_att_dara);
                $ret = json_decode(json_encode($entity_response));
                //print_r($ret); exit;
              // $ret = CustomHelper::internalCall($request, \URL::to(DIR_API) . '/system/entities', 'POST', $_att_dara);

               //$entity_auth = $this->_targetEntityModel->getBy('entity_auth_id', $id);

                $entity = $this->_targetEntityModel->getData($ret->data->{$ret->data->identifier}->{$this->_targetEntityModel->primaryKey},
                    $request->all());

                $roleModel = $this->_modelPath . "SYSRole";
                $roleModel = new $roleModel;
                $role_id = $roleModel->getRoleByEntityType($request->{$this->_entityTypeModel->primaryKey});
                if ($role_id != 0) {
                    $role_map_model = $this->_modelPath . "SYSEntityRoleMap";
                    $role_map_model = new $role_map_model;

                    $role_map_model->InsertRoleEntity($role_id, $id);
                }
                $entity_identifier = $ret->data->{$ret->data->identifier};
                // get entity record
                //$entity = $this->_entityModel->get($id);
                //$entity = $this->_model->getData($id,$ret->data->{$ret->data->identifier}->entity_type_id, true);
                // load / init models
                $entity_history_model = $this->_modelPath . "SYSEntityHistory";
                $entity_history_model = new $entity_history_model;
                // set data for history
                $actor_entity = $this->_entityIdentifier;
                $actor_id = $entity->{$this->_model->primaryKey};
                $identifier = "social_signup";
                // other data
                $other_data["navigation_type"] = $identifier;

                //$entity_history_model->putEntityHistory($actor_entity, $actor_id, $identifier, $other_data, $this->_plugin_identifier);

            } else {

                $save = array(
                    /* "entity_type_id" => $request->entity_type_id, */
                    "device_type" => $request->device_type,
                    "device_token" => $request->input("device_token", ""),
                    "updated_at" => date('Y-m-d H:i:s'),
                );

                $this->_model->set($exists_id, $save);

                // get data into array
                $entity = $this->_targetEntityModel->getData($entity->{$this->_targetEntityModel->primaryKey}, $request->all());
                // set data
                // optional params if available
                if (isset($optionalFields[0])) {
                    foreach ($optionalFields as $optionalField) {
                        if ($request->input($optionalField, "") != "") {
                            if ($optionalField == "image") {
                                // upload social user image
                                $user_img_dir = config($this->_entityConfFile . ".DIR_IMG");
                                $filename = "t-" . str_replace(".", "-", microtime(true));
                                $filename .= ".jpg";
                                $upload_path = $user_img_dir . $filename;
                                $getFile = @file_get_contents($request->input($optionalField));
                                @file_put_contents($upload_path, $getFile);

                                // create thumb
                                //creating thumb image
                                $thumb = "thumb-" . $filename;
                                $thumbData = file_get_contents(url("/") . "/" . "thumb/" . base64_encode($user_img_dir) . "/150x150/" . $filename . "/" . $thumb);

                                // set db data
                                $entity["image"] = $filename;
                                $entity["thumb"] = $thumb;
                            } else {
                                //$entity->optionalField = $request->{$optionalField};
                            }
                        }
                    }
                }
            }
            // set other params
            /*$entity->last_login_at = $date_stamp;
            $entity->deleted_at = NULL; // reactivate*/

            // process signup
            //$this->_model->set($entity->auth->{$this->_model->primaryKey}, $entity_auth);


            // get data into array
            $update_entity = array("entity_auth_id"=>$entity->auth->entity_auth_id);

            if(isset($entity->auth->email) && !empty($entity->auth->email)){
                //if($entity->is_email_verified==1){
                if(empty($entity->auth->customer_id)){
                    $CustomerPara['entity_auth_id'] = $entity->entity_auth_id;
                    $CustomerPara['email'] = $entity->auth->email;

                    $StripeService = $this->_StripeLib->addCustomer($CustomerPara);
                    if(isset($StripeService['response']['id'])){
                        $update_entity['customer_id']  = $StripeService['response']['id'];
                    }
                }
             /*   if(empty($entity->auth->account_id)){
                    $StripeService = $this->_StripeLib->addAccount($entity->entity_id, $entity->auth->email);

                    if (isset($StripeService['response']['id'])) {
                        $account_id = $StripeService['response']['id'];
                        $update_entity['account_id']= $account_id;
                    }
                }*/
                //}
            }


           // $entity = $update_entity;
            // set other params
            $update_entity["last_login_at"] = $date_stamp;
            $update_entity["deleted_at"] = NULL; // reactivate

            // process signup
            $this->_model->set($entity->auth->entity_auth_id, $update_entity);

            // load / init models
            $entity_history_model = $this->_modelPath . "SYSEntityHistory";
            $entity_history_model = new $entity_history_model;
            // set data for history
            $actor_entity = $this->_entityIdentifier;

            /* $actor_id = $entity->{$this->_targetEntityModel->primaryKey};*/
            $identifier = "social_login";
            // other data
            $other_data["navigation_type"] = $identifier;

            //$entity_history_model->putEntityHistory($actor_entity, $actor_id, $identifier, $other_data, $this->_plugin_identifier);

            // response data
            $data[$this->_objectIdentifier] = $entity;
            // generate and assign new oAuth Token, remove old tokens
            // load / init models
            $api_token_model = $this->_modelPath . "ApiToken";
            $api_token_model = new $api_token_model;

            $data["client_token"] = $api_token_model->generate($request->{$this->_entityTypeModel->primaryKey}, $entity->auth->{$this->_model->primaryKey}, true);
            $this->_apiData['message'] = trans('api_errors.success');

            // assign to output
            $this->_apiData['data'] = $data;
            $this->_apiData = CustomHelper::hookData($this->_extHook, __FUNCTION__, $request, $this->_apiData);

        }

        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * Edit Profile
     *
     * @return Response
     */
    public function editProfile(Request $request)
    {

        // trim/escape all
        @$request->merge(array_map('strip_tags', $request->all()));
        @$request->merge(array_map('trim', $request->all()));


        $email = trim(str_replace(" ", "", strip_tags($request->input('email', ''))));
        $fileIndex = "raw_image";

        // optional fields
        $optionalFields = array(
            //"first_name",
            //"last_name",
            "name",
            "email",
            //"dob",
            "country_id",
            "state_id",
            //"zip_code",
            //"gender",
            "device_type",
            "device_udid",
            "device_token",
            //"mobile_no"
        );


        // ex model
        $exModel = $this->_modelPath . "SYSEntity";
        $exModel = new $exModel;

        // validations
        $validator = Validator::make($request->all(), array(
            $exModel->primaryKey => "required|exists:" . $exModel->table . "," . $exModel->primaryKey,
            'email' => 'email',
            //'gender' => 'in:' . config("constants.ALLOWED_GENDERS"),
            //'password' => 'required|min:6',
            //'first_name' => 'required',
            //'last_name' => 'required',
            //"dob" => "date_format:Y-m-d",
            "country_id" => "exists:country,country_id",
            "state_id" => "exists:state,state_id",
            //"zip_code" => "required",
            'device_type' => 'in:' . config("constants.DEVICE_TYPES"),
            //'device_udid' => 'required',
            $fileIndex => "mimes:jpg,jpeg,png",
        ));

        // get data
        $entity = $this->_targetEntityModel->getData($request->{$exModel->primaryKey});

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else if ($entity === FALSE) {
            $this->_apiData['message'] = trans('api_errors.invalid_record_request');
        } elseif ($entity->auth->status == 0) {
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.your_account_is_inactive', array("entity" => "inactive"));
        } elseif ($entity->auth->status > 1) {
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.your_account_is_baned_removed');
        } else {

            // add attributes fields data
            $api_method_field_model = $this->_modelPath . "ApiMethodField";
            $api_method_field_model = new $api_method_field_model;
            $listfields = $api_method_field_model->getEntityAttributeList($entity->entity_type_id);

            $_att_dara["entity_id"] = $entity->entity_id;
            $_att_dara["updated_at"] = date("Y-m-d H:i:s");
            //$_att_dara["identifier"] = $request->email;
            $_att_dara["entity_type_id"] = $request->entity_type_id;
            $_att_dara["_token"] = $request->_token;

            foreach ($listfields as $_field) {
                if (isset($request->{$_field->attribute_code})) {
                    $_att_dara[$_field->attribute_code] = $request->{$_field->attribute_code};
                }
            }
            $roleMapModel = $this->_modelPath . "SYSEntityRoleMap";
            $roleMapModel = new $roleMapModel;

            $role_id = $roleMapModel->getRoleByEntity($entity->{$this->_model->primaryKey});
            $_att_dara["role_id"] = $role_id;

            if (isset($request->login_entity_id) && !empty($request->login_entity_id)) {
                $_att_dara["login_entity_id"] = $request->login_entity_id;
            }

            if(isset($request->attachment_id)){
                $_att_dara['gallery_items'] = $request->attachment_id;
            }


            //print_r($_att_dara);die;
            $entity_lib = new Entity();
            $ret = $entity_lib->apiUpdate($_att_dara);
            $ret = json_decode(json_encode($ret));
           // $ret = CustomHelper::internalCall($request, \URL::to(DIR_API) . '/system/entities/update', 'POST', $_att_dara);
          // print_r($ret); exit;
            if (isset($ret)) {
                if ($ret->error == "1") {
                    $this->_apiData['message'] = $ret->message;
                } else {
                    // success response
                    $this->_apiData['response'] = "success";

                    // init output data array
                    $this->_apiData['data'] = $data = array();

                    $entity_id = $ret->data->{$this->_targetEntityModel->primaryKey};
                    // treat as array
                    $entity = array();
                    // entity_id


                    // set data
                    // optional params if available
                    if (isset($optionalFields[0])) {
                        foreach ($optionalFields as $optionalField) {
                            if ($request->input($optionalField, "") != "") {
                                $entity[$optionalField] = $request->{$optionalField};
                            }
                        }
                    }
                    // set name
                    if ($request->input("name", "") == "" && ($request->input("first_name", "") != "" || $request->input("last_name", "") != "")) {
                        $entity["name"] = $request->input("first_name", "") . " " . $request->input("last_name", "");
                    } else {
                        $entity["name"] = $request->input("name", "");
                    }
                    // required params
                    //$entity['email'] = $request->input("email");
                    //$entity['password'] = $request->input("password");

                    // if has file
                   /* if ($request->hasFile($fileIndex)) {
                        // path/file name
                        $dirPath = config($this->_entityConfFile . ".DIR_IMG");
                        $fileName = "t-" . str_replace(".", "-", microtime(true));
                        //$fileName .= "." . $request->file($fileName)->getClientOriginalExtension();
                        $fileName .= ".jpg";

                        // save file in entity dir (create dir if not exists)
                        if (!is_dir($dirPath)) {
                            mkdir(@$dirPath, 0777, true);
                        }

                        //create file
                        $request->file($fileIndex)->move($dirPath, $fileName);

                        // if dp created successfully, create thumbnail
                        $thumbName = "thumb-" . $fileName;
                        $thumbData = file_get_contents(url("/") . "/" . "thumb/" . base64_encode($dirPath) . "/150x150/" . $fileName . "/" . $thumbName);

                        // set db data
                        $entity["image"] = $fileName;
                        $entity["thumb"] = $thumbName;
                    }*/

                    // process signup
                    $this->_model->set($ret->data->auth->{$this->_model->primaryKey}, $entity);


                    // load / init models
                    $entity_history_model = $this->_modelPath . "SYSEntityHistory";
                    $entity_history_model = new $entity_history_model;
                    // set data for history
                    $actor_entity = $this->_entityIdentifier;
                    $actor_id = $entity_id;
                    $identifier = "edit_profile";
                    // other data
                    $other_data["navigation_type"] = $identifier;

                    //$entity_history_model->putEntityHistory($actor_entity, $actor_id, $identifier, $other_data, $this->_plugin_identifier);

                    // response data
                    $data[$this->_objectIdentifier] = $this->_targetEntityModel->getData($entity_id,$request->all());

                    $this->_apiData['message'] = trans('api_errors.success');

                    // assign to output
                    $this->_apiData['data'] = $data;
                }
            }
        }
        return $this->__ApiResponse($request, $this->_apiData);
    }


    /**
     * Save Token
     *
     * @return Response
     */
    public function saveToken(Request $request)
    {
        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));


        // optional fields
        $optionalFields = array();


        // ex model
        $exModel = $this->_modelPath . "SYSEntity";
        $exModel = new $exModel;

        $entity_lib = new Entity();

        // validations
        $validator = Validator::make($request->all(), array(
            $exModel->primaryKey => "required|exists:" . $exModel->table . "," . $exModel->primaryKey,
            'device_type' => 'required|in:' . config("constants.DEVICE_TYPES"),
           // 'device_udid' => 'required',
            'device_token' => 'required',
        ));

        // get data
        $entityData = $entity_lib->getData($request->{$exModel->primaryKey});
        //$entity = $this->_model->getDataByEntityID($request->{$exModel->primaryKey});
        $entity = $entityData->auth;

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else if ($entity === FALSE) {
            $this->_apiData['message'] = trans('api_errors.invalid_record_request');
        } elseif ($entity->status == 0) {
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.your_account_is_inactive', array("entity" => "inactive"));
        } elseif ($entity->deleted_at !== NULL || $entity->status > 1) {
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.your_account_is_baned_removed');
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();
            $entity = (array)$entity;
            // entity_id
            $entity_id = $entity[$this->_model->primaryKey];

            // set data
            // optional params if available
            if (isset($optionalFields[0])) {
                foreach ($optionalFields as $optionalField) {
                    if ($request->input($optionalField, "") != "") {
                        $entity[$optionalField] = $request->{$optionalField};
                    }
                }
            }
            // required params
            $entity['device_type'] = $request->input("device_type");

            if(isset($request->device_udid))
            {
                $entity['device_udid'] = $request->input("device_udid");
            }

            $entity['device_token'] = $request->input("device_token");
            // process signup
            $this->_model->set($entity_id, $entity);

            // load / init models
            $entity_history_model = $this->_modelPath . "SYSEntityHistory";
            $entity_history_model = new $entity_history_model;
            // set data for history
            $actor_entity = $this->_entityIdentifier;
            $actor_id = $entity_id;
            $identifier = "save_mobile_token";
            // other data`
            $other_data["navigation_type"] = $identifier;

            //$entity_history_model->putEntityHistory($actor_entity, $actor_id, $identifier, $other_data, $this->_plugin_identifier);

            // response data
            $data[$this->_objectIdentifier] = $entity_lib->getData($entityData->entity_id,$request->all());

            $this->_apiData['message'] = trans('api_errors.success');

            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * Delete user
     *
     * @return Response
     */
    public function delete(Request $request)
    {


        // ex model
        $exModel = $this->_modelPath . "SYSEntity";
        $exModel = new $exModel;
        // get params
        $entity_id = intval(trim(strip_tags($request->input($exModel->primaryKey, 0))));

        // get user data
        $entity = $this->_model->getDataByEntityID($entity_id);


        // validations
        if ($entity === FALSE) {
            $this->_apiData['message'] = 'Invalid user request';
        } /*elseif ($entity !== FALSE && $entity->status == 0) {
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.your_account_is_inactive', array("entity" => "inactive"));
        }*/ elseif ($entity !== FALSE && $entity->status > 1) {
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.your_account_is_baned_removed');
        } else {
            // success response
            $this->_apiData['response'] = "success";
            // init output data array
            $this->_apiData['data'] = $data = array();

            // put deleted date
            //$entity->deleted_at = date("Y-m-d H:i:s");
            //$this->__models[$this->_objectIdentifier.'_model']->set($entity->{$this->_model->primaryKey},(array)$entity);
            // remove user account and related tasks
            $this->_model->remove($entity->{$this->_model->primaryKey});

            // response data
            //$data[$this->_entityIdentifier] = $this->_model->getData($entity->{$this->_model->primaryKey});

            // message
            $this->_apiData['message'] = trans('api_errors.success');

            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }


    /**
     * Verify Phone
     *
     * @return Response
     */
    public function resendCode(Request $request)
    {
        // trim/escape all
        //$request->merge(array_map('strip_tags', $request->all()));
        // $request->merge(array_map('trim', $request->all()));


        $identity = trim(str_replace(array(" ", "+"), "", strip_tags($request->mobile_no)));
        // $identity = $request->mobile_no;

        $exModel = $this->_modelPath . "SYSEntity";
        $exModel = new $exModel;

        // validations
        $validator = Validator::make($request->all(), array(
            'mobile_no' => 'required|string',
            'new_login_id' => 'string|required_without_all:mobile_no,login_id',
            'login_id' => 'string|required_without_all:mobile_no,new_login_id',
            'entity_id' => 'int|required_without_all:entity_type_id|exists:' . $this->_targetEntityModel->table . ',' . $this->_targetEntityModel->primaryKey . ',deleted_at,NULL',
            'entity_type_id' => 'int|required_without:entity_id|exists:' . $this->_entityTypeModel->table . ',' . $this->_entityTypeModel->primaryKey . ',deleted_at,NULL',
            'mode' => 'string|in:signup,change_mobile_no,forgot'
        ));

        $mode = $request->input('mode', 'signup');

        // handle cases
        // - forgot
        if ($mode == 'forgot') {
            // get entity type ID
            $request->merge(['login_id' => $identity]);
            return $this->forgotPasswordRequest($request);
        }
        // - signup
        if ($mode == 'change_mobile_no') {
            $request->merge(['new_login_id' => $identity]);
            return $this->changeIDRequest($request);
        }

        // validations
        /*$row_type_exists = $this->_model->where('mobile_no', '=', $identity)
            ->whereNull("deleted_at")
            ->get();*/
        $row_type_exists = $this->_model->entityQuery($request->entity_type_id)
            ->select('auth.'.$this->_model->primaryKey)
            ->where('auth.mobile_no', $identity)
            ->where('auth.is_verified',0)
            ->where('auth.entity_auth_id','>', 0)
            ->whereNull('auth.deleted_at')
            ->whereNull('entity.deleted_at')
            ->get();


        $exists_id = isset($row_type_exists[0]) ? $row_type_exists[0]->{$this->_model->primaryKey} : 0;
        $entity = $this->_model->getData($exists_id);

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else if (!$entity) {
            $this->_apiData['message'] = trans('api_errors.invalid_record_request');
        } /*elseif ($entity->status == 0) {
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.your_account_is_inactive', array("entity" => "inactive"));
        }*/ elseif ($entity->is_verified == 1) {
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.account_already_verified');
        } else {
            // success response
            $this->_apiData['response'] = "success";
            // init output data array
            $this->_apiData['data'] = $data = array();

            //$this->_model->verifyPhone($identity);

            // send sms code (if not in sandbox mode)
            if (!config($this->_config_dir . '.SMS_SANDBOX_MODE')) {
                $this->_model->sendSMS($entity, "", "resend");
            }

            // get data
            $entity_auth = $this->_model->getData($exists_id);
            $entity_id = $this->_model->entityQuery($request->entity_type_id)
                ->select('entity.' . $this->_targetEntityModel->primaryKey)
                ->where('auth.entity_auth_id', $entity_auth->{$this->_model->primaryKey})
                ->get();

            $data[$this->_objectIdentifier] = $this->_targetEntityModel->getData(
                $entity_id[0]->{$this->_targetEntityModel->primaryKey},
                $request->all()
            );

            // message
            $this->_apiData['message'] = trans('api_errors.success');

            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }


    /**
     * Verify Phone
     *
     * @return Response
     */
    public function verifyPhone(Request $request)
    {
        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));


        if (!is_numeric(trim($request->entity_type_id))) {
            $exModel = $this->_modelPath . "SYSEntityType";
            $exModel = new $exModel;
            $entityTypeData = $exModel->getEntityTypeByName($request->entity_type_id);
            if ($this->_entityTypeData) $request->entity_type_id = $entityTypeData->entity_type_id;
        }

        // validations
        $validator = Validator::make($request->all(), array(
            "verification_token" => "string",
            "mobile_no" => "required|string",
            "authy_code" => "required|string",
            "verification_mode" => "required|string|in:signup,forgot,change_mobile_no",
        ));

        // defaults
        //$request->type = $request->input("type", "") == "" ? explode(",", config("constants.ALLOWED_ENTITY_TYPES"))[0] : $request->type;
        $request->mobile_no = str_replace("+", "", $request->mobile_no);

        // validations
        $row_type_exists = $this->_model
            ->where('verification_token', '=', $request->verification_token)
            //->where('type', '=', $request->type)
            ->whereNull("deleted_at");
        if ($request->verification_mode != "change_mobile_no") {
            $row_type_exists->where('mobile_no', '=', $request->mobile_no);
        }

        $row_type_exists = $row_type_exists->get(array($this->_model->primaryKey));

        $exists_id = isset($row_type_exists[0]) ? $row_type_exists[0]->{$this->_model->primaryKey} : 0;
        $entity = $this->_model->get($exists_id);

        // override mobile number with the given one
        if ($request->verification_mode == "change_mobile_no") {
            return $this->resetID($request);
        }

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else if (!$entity) {
            $this->_apiData['message'] = trans('api_errors.invalid_record_request');
        } elseif ($request->verification_mode != "signup" && $entity->status == 0) {
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.your_account_is_inactive', array("entity" => "inactive"));
        } elseif ($entity->status == 3) {
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.your_account_is_baned_removed');
        } else {


            // check sms sandbox mode
            if (!config($this->_config_dir . '.SMS_SANDBOX_MODE')) {
                // init models
                $conf_model = new Conf;

                // twilio configurations
                $config = $conf_model->getBy("key", "twilio_config");
                $twilio = json_decode($config->value);
                /*// override mobile number with the given one
                if($request->verification_mode == "change_mobile_no") {
                    $entity->mobile_no = $request->mobile_no;
                }*/
                $number_data = explode("-", $entity->mobile_no);
                $country_code = str_replace("+", "", $number_data[0]);
                $mobile_no = str_replace(array("+" . $country_code), "", $number_data[1]);

                // verify with authy
                $authy_api = new \Authy\AuthyApi($twilio->api_key);

                $verify = $authy_api->phoneVerificationCheck($mobile_no, $country_code, $request->authy_code);

                $authy_verified = $verify->ok() ? TRUE : FALSE;
            } else {
                $authy_verified = TRUE;
            }

            // check code
            if ($authy_verified) {
                // success response
                $this->_apiData['response'] = "success";

                // init output data array
                $this->_apiData['data'] = $data = array();

                // if case is signup
                if ($request->verification_mode == "change_mobile_no") {
                    // over-write request
                    //$request->new_login_id = $request->mobile_no;
                    //return $this->resetID($request, $request);

                    // set/reset new data
                    $entity->verification_token = NULL;
                    $entity->verified_at = date("Y-m-d H:i:s");
                    $entity->sent_email_verification = 0;
                    $entity->sent_mobile_verification = 0;
                    $entity->mobile_no = $request->new_login_id;


                    // update user data
                    $this->_model->set($entity->{$this->_model->primaryKey}, (array)$entity);

                    // remove junk ids associated with new id
                    $this->_model->removeUnverified($entity, $entity->mobile_no, "mobile_no");
                    // send data
                    $data[$this->_objectIdentifier] = $this->_model->getData($entity->{$this->_model->primaryKey});
                    // message
                    $this->_apiData['message'] = trans('api_errors.entity_updated_successfully', array("entity" => ucfirst("mobile_no")));

                } else if ($request->verification_mode == "forgot") {
                    // generate new token
                    $this->_model->forgotPasswordVerify($entity, "mobile_no");

                    // get data
                    $entity = $this->_model->getData($entity->{$this->_model->primaryKey});
                    $data[$this->_objectIdentifier] = $entity;

                    // api message
                    $this->_apiData['message'] = trans('api_errors.check_email_for_confirmation');
                } else {
                    // history identifier
                    $history_identifier = "signup_confirm";
                    // mobile verified
                    $entity->is_mobile_verified = 1;
                    // welcome user
                    $this->_model->welcome($entity);

                    // find other accounts with this mobile number, which are not verified (we assume those are junk accounts now)
                    $raw_ids = $this->_model
                        ->select('auth.*','e.entity_id','e.entity_type_id')
                        ->from($this->_model->table.' As auth')
                        ->leftJoin('sys_entity AS e', 'e.entity_auth_id', '=','auth.'.$this->_model->primaryKey)
                        ->where("auth.mobile_no", "=", $entity->mobile_no)
                        ->where("auth.is_verified", "!=", 1)
                        ->where('auth.'.$this->_model->primaryKey, "!=", $entity->{$this->_model->primaryKey})
                        ->whereNull("auth.deleted_at")
                        ->get();


                    // if found, remove
                    if (isset($raw_ids[0])) {
                        foreach ($raw_ids as $raw_id) {

                           // echo "<pre>"; print_r($raw_id);
                            $this->_model->remove($raw_id->{$this->_model->primaryKey});
                            // delete data to sys_entity table
                            $this->_targetEntityModel->remove($raw_id->entity_id);
                            $this->_targetEntityModel->deleteEntityData($raw_id->entity_id);

                            if ($this->_entityTypeData->use_flat_table == "1") {
                                // remove from flat
                                if (\Schema::hasTable($this->_entityTypeData->identifier . '_flat')) {
                                    // remove from flat table
                                    //$this->_entityModel->table($entity_type->identifier . '_flat')->remove($record->entity_id);
                                    $flat_obj = new SYSTableFlat($this->_entityTypeData->identifier);
                                    // delete data to flat table
                                    //  $id = $flat_obj->remove($record->entity_id);
                                    if (SOFT_DELETE === TRUE) {
                                        $flat_obj->where($this->_entityModel->primaryKey, $raw_id->entity_id)
                                            ->update(array('deleted_at' => date("Y-m-d H:i:s")));
                                    } else {
                                        $flat_obj->where($this->_entityModel->primaryKey, $raw_id->entity_id)
                                            ->delete();
                                    }
                                }
                            }
                        }
                    }

                    // get data
                    //$data[$this->_objectIdentifier] = $this->_model->getData($exists_id, true);

                    $entity_auth_data = $this->_model->entityQuery($request->entity_type_id)
                        ->where("auth." . $this->_model->primaryKey, "=", $exists_id)
                        ->select("entity." . $this->_targetEntityModel->primaryKey)
                        //->where("auth.is_verified","=",1)
                        ->first();


                    // api message
                    $this->_apiData['message'] = trans('api_errors.account_verification_success');

                    //Call trigger for verify Phone
                    $entity_auth_trigger = new EntityAuthTrigger();
                    $trigger_func =  __FUNCTION__."AfterTrigger";
                    if (method_exists($entity_auth_trigger, "$trigger_func")) {
                        //prams for trigger
                        $trigger_params['entity_auth_id'] = $exists_id;
                        $trigger_params['entity_type_id'] = $request->entity_type_id;

                        $trigger_response = $entity_auth_trigger->$trigger_func($trigger_params);

                        if($trigger_response->error == 1){
                            $this->_apiData['message'] = $trigger_response->message;
                        }
                    }

                    $return_data = $this->_targetEntityModel->getData($entity_auth_data->entity_id, $request->all());

                    $data[$this->_entityTypeData->identifier] = $return_data;


                }

                // assign to output
                $this->_apiData['data'] = $data;
            } else {

                $this->_apiData['message'] = trans('api_errors.entity_is_invalid', array("entity" => "Authentication code"));
            }

        }

        return $this->__ApiResponse($request, $this->_apiData);
    }


    /**
     * change ID Request
     *
     * @return Response
     */
    public function changeIDRequest(Request $request)
    {
        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));

        $identity = trim(str_replace(" ", "", strip_tags($request->input('new_login_id', ''))));
        $identity = str_replace("+", "", $identity);

        // defaults
        //$request->type = $request->input("type", "") == "" ? explode(",", config("constants.ALLOWED_ENTITY_TYPES"))[0] : $request->type;


        // ex model
        $exModel = $this->_modelPath . "SYSEntity";
        $exModel = new $exModel;


        // validations
        $validator = Validator::make($request->all(), array(
//            'type' => 'string|in:' . config("constants.ALLOWED_ENTITY_TYPES"),
            $exModel->primaryKey => "required|exists:" . $exModel->table . "," . $exModel->primaryKey,
            "new_login_id" => "required|string"
        ));

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else {
            // get data
            $entity = $this->_targetEntityModel->getData($request->{$exModel->primaryKey});
            // default entity data
            if (isset($entity->auth)) {
                $entity = $entity->auth;
            }

            // if "@" sign not found
            if (config($this->_config_dir . '.SMS_SIGNUP_ENABLED') && !preg_match("/@/", $identity)) {

                $do_exists = $this->_model->where("mobile_no", "=", $identity)
                    ->where("is_verified", "=", 1)
//                ->where("type", "=", $request->type)
                    ->whereNull("deleted_at")
                    ->count();

                // id type
                $id_type = "mobile_no";
            }

            // if found "@" sign
            if (config($this->_config_dir . '.EMAIL_SIGNUP_ENABLED') && preg_match("/@/", $identity)) {
                $do_exists = $this->_model->where("email", "=", $identity)
                    ->where("is_verified", "=", 1)
//                ->where("type", "=", $request->type)
                    ->whereNull("deleted_at")
                    ->count();

                // id type
                $id_type = "email";
            }

            if (!$entity) {
                // kick user
                //$this->_apiData['kick_user'] = 1;
                // message
                $this->_apiData['message'] = trans('api_errors.invalid_entity_request', array("entity" => $this->_objectIdentifier));
            } elseif ($entity->deleted_at !== NULL) {
                // kick user
                $this->_apiData['kick_user'] = 1;
                // message
                $this->_apiData['message'] = trans('api_errors.invalid_entity_request', array("entity" => $this->_objectIdentifier));
            } elseif ($entity->status > 1) {
                // kick user
                $this->_apiData['kick_user'] = 1;
                // message
                $this->_apiData['message'] = trans('api_errors.your_account_is_baned_removed');
            } elseif ($do_exists > 0) {
                $this->_apiData['message'] = trans('api_errors.entity_already_exists', array("entity" => (preg_match("/@/", $identity) ? "Email" : "Mobile no")));
            } elseif ($id_type == "mobile_no" && !preg_match(MOBILE_NO_PATTREN, $identity)) {
                $this->_apiData['message'] = trans('api_errors.invalid_entity_request', array("entity" => "login ID"));
            } else {

                // success response
                $this->_apiData['response'] = "success";

                // init output data array
                $this->_apiData['data'] = $data = array();

                // generate forgot password token
                $entity_auth = $this->_model->changeIDRequest($entity, $identity, $id_type);
                $entity = $this->_targetEntityModel->getData($request->{$exModel->primaryKey},$request->all());

                if (isset($entity->{$this->_entityTypeModel->primaryKey})) {
                    $entity_type_data = $this->_entityTypeModel->get(trim($entity->{$this->_entityTypeModel->primaryKey}));
                    $this->_entityIdentifier = $entity_type_data->identifier;
                }

                //$entity = $this->_targetEntityModel->getData($request->{$exModel->primaryKey});
                /*$entity_data = array(
                    "email" => $entity->email,
                    "is_email_verified" => $entity->is_email_verified,
                    "mobile_no" => $entity->mobile_no,
                    "is_mobile_verified" => $entity->is_mobile_verified,
                    //"forgot_password_token" => $entity->forgot_password_token,
                    //$this->_model->primaryKey => $entity->{$this->_model->primaryKey}
                    "verification_token" => $entity->verification_token,
                    "sent_email_verification" => $entity->sent_email_verification,
                    "sent_mobile_verification" => $entity->sent_mobile_verification
                );

                // send entity data
                //$data[$this->_entityIdentifier] = $entity_data;*/

                /*  if ($id_type == "email") {
                      $entity->email = $identity;
                      $entity->is_email_verified = 0;
                  } else {
                      $entity->mobile_no = "+" . $identity;
                      $entity->is_mobile_verified = 0;
                  }*/

                // if case is mobile, send new_mobile_no
                if ($id_type == "mobile_no") {
                    $entity->auth->new_mobile_no = '+' . $identity;
					    $this->_apiData['message'] = trans('api_errors.check_phone_for_confirmation');
                }
				else{
						$this->_apiData['message'] = trans('api_errors.check_email_for_confirmation');
				}

                $data[$this->_entityIdentifier] = $entity;


              
                // assign to output
                $this->_apiData['data'] = $data;
            }
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }


    /**
     * reset ID
     *
     * @return Response
     */
    public function resetID(Request $request, $new_request = NULL)
    {
        // override new request
        if ($new_request !== NULL) {
            $request->merge($new_request->all());
        }
        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));

        //$identity = trim(str_replace(array(" ", "+"), "", strip_tags($request->input('new_login_id', ''))));
        $identity = trim(str_replace(array(" ", "+"), "", strip_tags($request->new_login_id)));
        $identity = $request->new_login_id == "" ? trim(str_replace(array(" ", "+"), "", strip_tags($request->mobile_no))) : $identity;
        //$authy_code = trim(str_replace(" ", "", strip_tags($request->input('authy_code', ''))));


        // validations
        $validator = Validator::make($request->all(), array(
            //'type' => 'string|in:' . config("constants.ALLOWED_ENTITY_TYPES"),
            'verification_token' => 'required|string|exists:' . $this->_model->table . ",verification_token",
            //'mobile_no' => 'optional',
            "new_login_id" => "string|required_without:mobile_no",
            'authy_code' => 'string',
        ));


        // defaults
        //$request->type = $request->input("type", "") == "" ? explode(",", config("constants.ALLOWED_ENTITY_TYPES"))[0] : $request->type;

        // check code exists
        $code_exists = $this->_model
            // ->where("type", "=", $request->type)
            ->where("verification_token", "=", $request->verification_token)
            ->whereNull("deleted_at")
            ->get();
        $entity = isset($code_exists[0]) ? json_decode(json_encode($code_exists[0])) : NULL;

        // detect mode of validation
        if (preg_match("/@/", $identity)) {
            $verification_mode = "email";
        } else {
            $verification_mode = "mobile_no";
        }

        // validate email
        $validate_email = Validator::make($request->all(), array(
            'new_login_id' => 'required|email',
        ));

        $email_exists = $this->_model->where("email", "=", $identity)
            ->where("is_verified", "=", 1)
            // ->where("type", "=", $request->type)
            ->whereNull("deleted_at")
            ->count();

        // validate number
        $valid_mobile = preg_match(MOBILE_NO_PATTREN, $identity);
        $mobile_exists = $this->_model->where("mobile_no", "=", $identity)
            ->where("is_verified", "=", 1)
            // ->where("type", "=", $request->type)
            ->whereNull("deleted_at")
            ->count();


        // validate
        if ($validator->fails()) {

            $this->_apiData['message'] = $validator->errors()->first();
        } elseif (!$entity) {
            $this->_apiData['message'] = trans('api_errors.invalid_entity_request', array("entity" => "verification code"));
        } elseif ($verification_mode == "email" && $validate_email->fails()) {
            $this->_apiData['message'] = $validate_email->errors()->first();
        } elseif ($verification_mode == "email" && $email_exists > 0) {
            $this->_apiData['message'] = trans('api_errors.entity_already_exists', array("entity" => "Email"));
        } elseif ($verification_mode == "mobile_no" && !$valid_mobile) {

            $this->_apiData['message'] = trans('api_errors.entity_is_invalid', array("entity" => ucfirst($verification_mode)));
        } elseif ($verification_mode == "mobile_no" && $mobile_exists > 0) {
            $this->_apiData['message'] = trans('api_errors.entity_already_exists', array("entity" => ucfirst($verification_mode)));
//        } elseif ($verification_mode == "mobile_no" && $authy_code == "") {
//            $this->_apiData['message'] = trans('api_errors.entity_is_required', array("entity" => "Authy code"));
        } elseif ($entity->status == 0) {
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.your_account_is_inactive', array("entity" => "inactive"));
        } elseif ($entity->status > 1) {
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.your_account_is_baned_removed');
        } else {

            // init output data array
            $this->_apiData['data'] = $data = array();


            if ($verification_mode == "mobile_no") {

                // check sms sandbox mode
                if (!config($this->_config_dir . '.SMS_SANDBOX_MODE')) {
                    // init models
                    $conf_model = new Conf;

                    // twilio configurations
                    $config = $conf_model->getBy("key", "twilio_config");
                    $twilio = json_decode($config->value);

                    $number_data = explode("-", $identity);
                    $country_code = str_replace("+", "", $number_data[0]);
                    $mobile_no = str_replace(array("+" . $country_code), "", $number_data[1]);

                    $authy_api = new \Authy\AuthyApi($twilio->api_key);

                    $verify = $authy_api->phoneVerificationCheck($mobile_no, $country_code, $request->authy_code);

                    $authy_verified = $verify->ok() ? TRUE : FALSE;
                } else {
                    $authy_verified = true;
                }

                if ($authy_verified) {
                    // success response
                    $this->_apiData['response'] = "success";
                    // set/reset new data
                    $entity->verification_token = NULL;
                    $entity->verified_at = date("Y-m-d H:i:s");
                    $entity->sent_email_verification = 0;
                    $entity->sent_mobile_verification = 0;
                    $entity->mobile_no = $identity;
                    $entity->new_mobile_no = NULL;

                    // update user data
                    $this->_model->set($entity->{$this->_model->primaryKey}, (array)$entity);

                    // remove junk ids associated with new id
                    $this->_model->removeUnverified($entity, $identity, $verification_mode);


                    // $entity = $this->_model->getData($entity->{$this->_model->primaryKey});

                    $row_type_exists = $this->_model->entityQuery($request->entity_type_id)
                        ->where("auth." . $this->_model->primaryKey, "=", $entity->{$this->_model->primaryKey})
                        ->select("entity." . $this->_targetEntityModel->primaryKey)
                        //->where("auth.is_verified","=",1)
                        ->get();

                    $exists_id = isset($row_type_exists[0]) ? $row_type_exists[0]->{$this->_targetEntityModel->primaryKey} : 0;
                    $entity = $this->_targetEntityModel->getData($exists_id,$request->all());

                    // send data
                    $data[$this->_objectIdentifier] = $entity;


                    // generate and assign new oAuth Token, remove old tokens
                    // load / init models
                    $api_token_model = $this->_modelPath . "ApiToken";
                    $api_token_model = new $api_token_model;
                    $data["client_token"] = $api_token_model->generate($request->{$this->_entityTypeModel->primaryKey}, $exists_id, true);


                    // message
                    $this->_apiData['message'] = trans('api_errors.entity_updated_successfully', array("entity" => ucfirst($verification_mode)));

                } else {
                    // error msg
                    $this->_apiData['message'] = trans('api_errors.entity_is_invalid', array("entity" => "Authy code"));
                }

            } else {
                // success response
                $this->_apiData['response'] = "success";
                // set/reset new data
                $entity->verification_token = $entity->mobile_verification_token = $entity->forgot_password_token = NULL;
                $entity->verified_at = date("Y-m-d H:i:s");
                $entity->sent_email_verification = 0;
                $entity->sent_mobile_verification = 0;
                $entity->email = $identity;

                // update user data
                $this->_model->set($entity->{$this->_model->primaryKey}, (array)$entity);

                // remove junk ids associated with new id
                $this->_model->removeUnverified($entity, $identity, $verification_mode);

                $entity = $this->_model->getData($entity->{$this->_model->primaryKey}, $request->{$this->_entityTypeModel->primaryKey}, true);

                // send data
                $data[$this->_objectIdentifier] = $entity;

                // generate and assign new oAuth Token, remove old tokens
                // load / init models
                $api_token_model = $this->_modelPath . "ApiToken";
                $api_token_model = new $api_token_model;
                $data["client_token"] = $api_token_model->generate($request->{$this->_entityTypeModel->primaryKey}, $entity->entity->{$this->_targetEntityModel->primaryKey}, true);


                // message
                $this->_apiData['message'] = trans('api_errors.entity_updated_successfully', array("entity" => ucfirst($verification_mode)));
            }


            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);
    }


    /**
     * user logout
     *
     * @return Response
     */

    public function logout(Request $request)
    {
        // trim/escape all
        @$request->merge(array_map('strip_tags', $request->all()));
        @$request->merge(array_map('trim', $request->all()));

        // validations
        $validator = Validator::make($request->all(), array(
            $this->_targetEntityModel->primaryKey => 'required|integer|exists:' . $this->_targetEntityModel->table . ',' . $this->_targetEntityModel->primaryKey . ',' . $this->_entityTypeModel->primaryKey . ',' . $this->_entityTypeData->{$this->_entityTypeModel->primaryKey} . ',deleted_at,NULL',
        ));

        // get data
        $entity = $this->_targetEntityModel->getData($request->{$this->_targetEntityModel->primaryKey});
        // validations
        /*if (!in_array("user/get", $this->_plugin_config["webservices"])) {
            $this->_apiData['message'] = 'You are not authorized to access this service.';
        } else*/
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } /*elseif($entity->{$this->_model->primaryKey} == 0) {
            $this->_apiData['message'] = trans('api_errors.invalid_entity_request', array("entity" => "auth"));
        } elseif ($entity->status == 0) {
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.your_account_is_inactive', array("entity" => "inactive"));
        } elseif ($entity->deleted_at !== NULL || $entity->status > 1) {
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.your_account_is_baned_removed');
        }*/ else {
            //get entity auth
            if(isset($entity->auth)){

                // success response
                $this->_apiData['response'] = "success";

                $this->_apiData['data'] = $data = array();

              $auth =  $this->_model->get($entity->auth->{$this->_model->primaryKey});
                $auth->device_token = '';
                // update user data
                $this->_model->set($entity->{$this->_model->primaryKey}, (array)$auth); // to be set in attributes

                $entity_type = false;
                if(isset($request->entity_type_id)){
                    $entity_type_model = new SYSEntityType();
                    $entity_type_data =  $entity_type_model->get($request->entity_type_id);
                    $entity_type = $entity_type_data;
                }

                $entity_auth_trigger = new EntityAuthTrigger();
                $trigger_func =  __FUNCTION__."AfterTrigger";
                if (method_exists($entity_auth_trigger, "$trigger_func")) {

                    $trigger_response = $entity_auth_trigger->$trigger_func($entity->{$this->_targetEntityModel->primaryKey},$entity_type);

                    if($trigger_response->error == 1){
                        $this->_apiData['message'] = $trigger_response->message;
                    }
                }

                $this->_apiData['message'] = "Token successfully cleared";
                // assign to output
                $this->_apiData['data'] = $data;

            }
            else{
                $this->_apiData['message'] = "Invalid entity id";
            }

        }

        return $this->__ApiResponse($request, $this->_apiData);
    }

    /**
     * user Profile
     *
     * @return Response
     */
    public function getProfile(request $request)
    {

        @$request->merge(array_map('strip_tags', $request->all()));
        @$request->merge(array_map('trim', $request->all()));

        // ex model
        $exModel = $this->_modelPath . "SYSEntity";
        $exModel = new $exModel;

        // validations
        $validator = Validator::make($request->all(), array(
            $exModel->primaryKey => "required|exists:" . $exModel->table . "," . $exModel->primaryKey,
        ));

        // get data
        $entity = $this->_model->getDataByEntityID($request->entity_id);
        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else if ($entity === FALSE) {
            $this->_apiData['message'] = trans('api_errors.invalid_record_request');
        } elseif ($entity->status == 0) {
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = trans('api_errors.your_account_is_inactive', array("entity" => "inactive"));
        } else {
            // success response
            $this->_apiData['response'] = "success";
            $this->_apiData['data']['user_profile'] = $entity;
            // assign to output
            //$this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);

    }

    /**
     * Forgot Password Reset
     * @param Request $request
     * @return \App\Http\Controllers\Response
     */
    public function ForgotResetPassword(Request $request)
    {

        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));


        // validations
        $validator = Validator::make($request->all(), array(
            'new_password' => 'required|string|min:6'
        ));

        $row_type_exists = $this->_model->entityQuery($request->entity_type_id)
            ->where("auth." . $this->_model->primaryKey, "=", $request->{$this->_model->primaryKey})
            ->select("entity." . $this->_targetEntityModel->primaryKey)
            //->where("auth.is_verified","=",1)
            ->get();
        $exists_id = isset($row_type_exists[0]) ? $row_type_exists[0]->{$this->_targetEntityModel->primaryKey} : 0;

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else if (count($exists_id) == 0) {

            $this->_apiData['message'] = trans('api_errors.invalid_record_request');
        } else {

            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();
            $entity_data = $this->_targetEntityModel->getData($exists_id, $request->entity_type_id, true);
            // set data
            $entity = (array)$entity_data->auth;
            unset($entity->entity_auth_id);
            // entity_id
            //$entity_id = $entity[$this->_entity_pk];

            // set new password
            $entity['verification_token'] = NULL;
            $entity['password'] = $this->_model->saltPassword($request->new_password);

            // update user data
            $this->_model->set($request->{$this->_model->primaryKey}, (array)$entity);


            // load / init models
            $entity_history_model = $this->_modelPath . "SYSEntityHistory";
            $entity_history_model = new $entity_history_model;
            // set data for history
            $actor_entity = $this->_entityIdentifier;
            $actor_id = $exists_id;
            $identifier = "forgot_reset_password_success";
            // other data
            $other_data["navigation_type"] = $identifier;

            //$entity_history_model->putEntityHistory($actor_entity, $actor_id, $identifier, $other_data, $this->_plugin_identifier);

            // msg
            $this->_apiData['message'] = trans("api_errors.forgot_password_reset_successfully", array("ex_text" => ""));

            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request, $this->_apiData);

    }

    /**
     * @param $request_params
     * @param $listOfAttributeToBeValidate
     * @return bool
     */
    private function _postValidator($request_params, $listOfAttributeToBeValidate)
    {
        //print_r($listOfAttributeToBeValidate); die;
        $is_error = false;
        $rules = array(
            'entity_type_id' => 'required|integer|exists:' . $this->_entityTypeModel->table . "," . $this->_entityTypeModel->primaryKey,
            // 'identifier' => 'string', //required|unique:' . $this->_entityModel->table . ',identifier
            //  $SYSEntityAuthModel->primarKey => 'integer|exists:' . $SYSEntityAuthModel->table . "," . $SYSEntityAuthModel->primaryKey . ",deleted_at,NULL",
        );
        $attributes_error = 1;
        if (isset($listOfAttributeToBeValidate[0])) {
            foreach ($listOfAttributeToBeValidate as $result) {
                $rules[$result->attribute_code] = $result->validation;
            }
            $attributes_error = 0;
        }


        $validator = Validator::make($request_params, $rules);

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
            $is_error = true;
        } else if ($attributes_error > 0) {
            $this->_apiData['message'] = "No attributes defined";
            $is_error = true;
        }
        return $is_error;
    }
}

