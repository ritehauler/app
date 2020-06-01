<?php
namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Api\System\EntityController;
use App\Http\Controllers\Controller;
use App\Http\Models\SYSEntityType;
use App\Libraries\CustomHelper;
use Auth;
use Illuminate\Support\Facades\Session;
use View;
use DB;
use Validator;
use Illuminate\Http\Request;
use Redirect;
use Mail;
// models
use App\Http\Models\Admin;
use App\Http\Models\AdminModule;
use App\Http\Models\AdminModulePermission;
use App\Http\Models\Page;
use App\Http\Models\SYSModule;
use App\Http\Models\SYSRolePermissionMap;
use App\Http\Models\SYSPermission;
use App\Http\Models\SYSRolePermission;
use App\Libraries\Module;
use App\Libraries\OrderHelper;
//use Illuminate\Support\Facades\Crypt;
use Crypt;

class KanbanManagementController extends EntityController
{

    private $_module_identifier = "kanban";
    private $_object_identifier = "order_management";
    private $_object_identifier_module = "modules";
    //  private $_object_identifier_entity = "entity_id";
    //private $_attribute_pk = "category_id";
    private $_listing_fields = array();
    private $_check_box_checked = "checked='checked'";
    private $_check_box_Unchecked = "";
    private $_child_arrow = "--->";
    private $_api_dir = "";

    /**
     * CategoryController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        //$this->middleware('auth');
        // construct parent

        parent::__construct($request);

        // define default dir
        $this->_assignData["dir"] = config("panel.DIR") .$this->_module_identifier. '/'. $this->_object_identifier . '/';
        // assign meta from parent constructor
        $this->_assignData["_meta"] = $this->__meta;
        // assign request
        $this->_assignData["request"] = $request;
        //module
        $this->_assignData['module'] = 'Order Management';

        $this->_api_dir = DIR_API."wfs/";
        //model path

        // extra models

        $this->_entity_session_identifier = config("panel.SESS_KEY");
        $this->_assignData["_entity_session_identifier"] = $this->_entity_session_identifier;
    }

    /**
     * Return data to admin listing page
     *
     * @return type Array()
     */
    public function index(Request $request)
    {
       // $this->_assignData['module'] = $this->_object_identifier;

        $this->_assignData["route_action"] = strtolower(__FUNCTION__);

        $session_id = session()->getId();
        $crypt_session_id = Crypt::encrypt($session_id);
        $this->_assignData["crypt_session_id"] = $crypt_session_id;
        $this->_assignData["kanban_call_url"] = config('constants.KANBAN_DETAIL.url');

        $view = View::make($this->_assignData["dir"] . __FUNCTION__, $this->_assignData);
        return $view;
    }

    /**
     * @param Request $request
     */
    public function getMatrix(Request $request)
    {
        $data = $this->__internalCall($request, \URL::to($this->_api_dir) ."/getMatrix",'POST',$_POST,false);
        $data->data->delivery_slots = OrderHelper::getOrderDeliverySlots();

        return response()->json($data);
    }

    /**
     * @param Request $request
     */
    public function getMatrixData(Request $request)
    {
        $_POST['type'] = 'Order Management';
        $crypt_session_id = $request->token;
        $decrypted_crypt_session_id = Crypt::decrypt($crypt_session_id);
        $session_data = CustomHelper::getSessionDataById($decrypted_crypt_session_id);

        if($session_data === false || !isset($session_data[$this->_entity_session_identifier . "auth"])){
            $data['error'] = 1;
            $data['message'] = 'Session not defined';
            return response()->json($data);
        }
        $session_auth_data = $session_data[$this->_entity_session_identifier . "auth"];
        $session_department = $session_data[$this->_entity_session_identifier . "department"];

        $is_admin = 0;
        if($session_department == 'super_admin'){
            $is_admin = 1;
        }

        $_POST['user_id'] = $session_auth_data->entity_auth_id;
        $_POST['role_id'] = $session_auth_data->auth->role_id;
        $_POST['department_id'] = $session_auth_data->auth->parent_role_id;
        $_POST['is_admin'] = $is_admin;

        $this->_updateDefaultPostData($request);
        $_POST['session_department'] = $session_data[$this->_entity_session_identifier . "department"];

        $data = $this->__internalCall($request, \URL::to($this->_api_dir) ."/getMatrixData",'POST',$_POST,false);

        $data->session['name'] = $session_auth_data->attributes['first_name'];
        return response()->json($data);
    }

    /**
     * @param Request $request
     */
    public function assignUser(Request $request)
    {
        $crypt_session_id = $request->token;
        $decrypted_crypt_session_id = Crypt::decrypt($crypt_session_id);
        $session_data = CustomHelper::getSessionDataById($decrypted_crypt_session_id);

        if($session_data === false){
            $data['error'] = 1;
            $data['message'] = 'Session not defined';
            return response()->json($data);
        }
        $session_auth_data = $session_data[$this->_entity_session_identifier . "auth"];
        $session_department = $session_data[$this->_entity_session_identifier . "department"];
        $is_admin = 0;
        if($session_department == 'super_admin'){
            $is_admin = 1;
        }

        $_POST['user_id'] = $session_auth_data->entity_auth_id;
        $_POST['role_id'] = $session_auth_data->auth->role_id;
        $_POST['department_id'] = empty($session_auth_data->auth->parent_role_id) ? $_POST['role_id'] : $session_auth_data->auth->parent_role_id;
        $_POST['is_admin'] = $is_admin;
        //$data = [];

        $res = $this->__internalCall($request, \URL::to($this->_api_dir) ."/user/assign",'POST',$_POST,false);

        $this->_updateDefaultPostData($request);
        $_POST['session_department'] = $session_data[$this->_entity_session_identifier . "department"];

        $data = $this->__internalCall($request, \URL::to($this->_api_dir) ."/getMatrixData",'POST',$_POST,false);
        if($res->error)
            $data->message = $res->message;
        $data->session['name'] = $session_auth_data->attributes['first_name'];
        return response()->json($data);
    }

    /**
     * @param Request $request
     */
    public function updateUser(Request $request)
    {
        $crypt_session_id = $request->token;
        $decrypted_crypt_session_id = Crypt::decrypt($crypt_session_id);
        $session_data = CustomHelper::getSessionDataById($decrypted_crypt_session_id);

        if($session_data === false){
            $data['error'] = 1;
            $data['message'] = 'Session not defined';
            return response()->json($data);
        }
        $session_auth_data = $session_data[$this->_entity_session_identifier . "auth"];
        $session_department = $session_data[$this->_entity_session_identifier . "department"];
        $is_admin = 0;
        if($session_department == 'super_admin'){
            $is_admin = 1;
        }

        $_POST['user_id'] = $session_auth_data->entity_auth_id;
        $_POST['role_id'] = $session_auth_data->auth->role_id;
        $_POST['department_id'] = $session_auth_data->auth->parent_role_id;
        $_POST['state_id'] = (strtolower($_POST['state']) == 'yes') ? 2 : 3;
        $_POST['is_admin'] = $is_admin;
        $_POST['session_department'] = $session_department;

        $ret = $this->__internalCall($request, \URL::to($this->_api_dir) ."/user/update",'POST',$_POST,false);


        $this->_updateDefaultPostData($request);
        $data = $this->__internalCall($request, \URL::to($this->_api_dir) ."/getMatrixData",'POST',$_POST,false);
        if($ret->error)
            $data->message = $ret->message;
        $data->session['name'] = $session_auth_data->attributes['first_name'];
        return response()->json($data);
    }

    /**
     * @param Request $request
     */
    public function addComment(Request $request)
    {
        /*
         * order_message
         * visible_to_customer : 1/0
         * target_id
         * target_type
         * order_id
         * */


        $crypt_session_id = $request->token;
        $decrypted_crypt_session_id = Crypt::decrypt($crypt_session_id);
        $session_data = CustomHelper::getSessionDataById($decrypted_crypt_session_id);

        if($session_data === false){
            $data['error'] = 1;
            $data['message'] = 'Session not defined';
            return response()->json($data);
        }
        $session_auth_data = $session_data[$this->_entity_session_identifier . "auth"];
        $params['target_id'] = $session_auth_data->entity_auth_id;
        $params['target_type'] = "'".$session_auth_data->auth->role_id."'";
        //$params['department_id'] = $session_auth_data->auth->parent_role_id;

        $params['visible_to_customer'] = $request->visible_to_customer;
        $params['order_id'] = $request->cell_matrix_id;
        $params['order_message'] = $request->message;
        $params['target_type'] = $request->name;

        $entity_type_model = new SYSEntityType();
        $params['entity_type_id'] = $entity_type_model->getIdByIdentifier('order_discussion'); //31;

        $ret = $this->__internalCall($request, 'api/system/order/comment/add','POST',$params,false);

        $this->_updateDefaultPostData($request);

        $_POST['session_department'] = $session_data[$this->_entity_session_identifier . "department"];
        //$data = $this->__internalCall($request, \URL::to($this->_api_dir) ."/getMatrixData",'POST',$_POST,false);

        $order_id = [$params['order_id']];
        $order_discussion = OrderHelper::getOrderDiscussion($order_id);

        $data['error'] = 0;
        $data['message'] = 'Success';
        $data['data'] = isset($order_discussion[$params['order_id']])? $order_discussion[$params['order_id']] : [];


        if($ret->error)
            $data['message'] = $ret->message;
        $data['session']['name'] = $session_auth_data->attributes['first_name'];

        return response()->json($data);
    }

    /**
     * @param Request $request
     */
    public function getComment(Request $request)
    {
        /*
         * order_message
         * visible_to_customer : 1/0
         * target_id
         * target_type
         * order_id
         * */

        $crypt_session_id = $request->token;
        $decrypted_crypt_session_id = Crypt::decrypt($crypt_session_id);
        $session_data = CustomHelper::getSessionDataById($decrypted_crypt_session_id);

        if($session_data === false){
            $data['error'] = 1;
            $data['message'] = 'Session not defined';
            return response()->json($data);
        }
        $data['error'] = 0;
        $data['message'] = 'Success';

        $order_id = [$request->order_id];
        $order_discussion = OrderHelper::getOrderDiscussion($order_id);

        $data['data'] = isset($order_discussion[$request->order_id])? $order_discussion[$request->order_id] : [];

        return response()->json($data);
    }

    private function _updateDefaultPostData(Request $request)
    {
        if(!isset($_POST['delivery_date']) || empty($_POST['delivery_date']) || $_POST['delivery_date'] == 'null')
            $_POST['delivery_date'] = date('Y-m-d');

        //$_POST['delivery_date'] = '2018-03-07';

        if(!isset($_POST['delivery_start_time']) || empty($_POST['delivery_start_time']) || $_POST['delivery_start_time'] == 'null')
            $_POST['delivery_start_time'] = '00:00';

        if(!isset($_POST['delivery_end_time']) || empty($_POST['delivery_end_time']) || $_POST['delivery_end_time'] == 'null')
            $_POST['delivery_end_time'] = '23:59';


    }
}