<?php
namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Api\System\EntityController;
use App\Http\Controllers\Controller;
use App\Http\Models\Custom\OrderFlat;
use App\Http\Models\FlatTable;
use App\Http\Models\SYSAttribute;
use App\Http\Models\SYSEntityAttribute;
use App\Http\Models\SYSEntityNotification;
use App\Http\Models\SYSEntityRoleMap;
use App\Http\Models\SYSEntityType;
use App\Http\Models\SYSRole;
use App\Http\Models\SYSRolePermission;
use App\Http\Models\SYSTableFlat;
use App\Libraries\CustomHelper;
use App\Libraries\EntityDriver;
use App\Libraries\EntityHelper;
use App\Libraries\EntityNotification;
use App\Libraries\Fields;
use App\Libraries\Module;
use App\Libraries\OrderHelper;
use App\Libraries\OrderStatus;
use App\Libraries\ProductHelper;
use App\Libraries\System\Entity;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use View;
use DateTime;
use DateTimeZone;
use DB;
use Validator;
use Illuminate\Http\Request;
use Redirect;
use Mail;
use Illuminate\Support\Facades\Input;
use Response;
// models
use App\Http\Models\Admin;
use App\Http\Models\AdminModule;
use App\Http\Models\AdminModulePermission;
use App\Http\Models\Page;
use App\Http\Models\ApiMethodField;
use File;
use App\Libraries\OrderProcess;
use App\Libraries\DataImportExport;


class EntityBackController extends EntityController
{

    protected $_model_path = "\App\Http\Models\\";
    protected $_object_identifier = "entities";
    protected $_object_identifier_list = "entity";
    protected $_attribute_pk = "entity_id";
    protected $_listing_fields = array();
    protected $_entity_controller = false;
    protected $_segment_controller = '';
    protected $_segment_action = '';
    protected $_attribute_fields = array();
    protected $_requested_route_params = array();
    protected $_extHook = "EntityBack"; // hook
    protected $_apiMethodFieldModel = '';
    private $_entity_session_identifier;

    public function __construct(Request $request)
    {
 
        //$this->middleware('auth');
        // construct parent
        parent::__construct($request);

        $this->_entity_session_identifier = config("panel.SESS_KEY");

        $this->_apiMethodFieldModel = new ApiMethodField();
        $_data = $this->load_params('system/' . $this->_object_identifier . '/listing', 'GET');
        $this->_listing_fields = $_data['records'];

        // define default dir
        $this->_assignData["dir"] = config("panel.DIR") . $this->_object_identifier . '/';
        // assign meta from parent constructor
        $this->_assignData["_meta"] = $this->__meta;
        // assign request
        $this->_assignData["request"] = $request;
        //module
        $this->_assignData['module'] = $this->_object_identifier;
        // extra models
        $prefix = trim($request->route()->getPrefix(),'/');
        $parameters = $request->route()->parameters();
        if(!empty($parameters)){
            foreach($parameters as $key => $parameter){
                $prefix = str_replace('{'.$key.'}',$parameter,$prefix);
            }

            $out_segment = explode('/',trim(substr(strstr($request->path(), $prefix), strlen($prefix)),'/'));
            $this->_requested_route_params = $out_segment;

            $this->_assignData['segment_controller'] = $this->_segment_controller = isset($out_segment[1]) ? $out_segment[1] : "";
             $this->_segment_action = isset($out_segment[2]) ? $out_segment[2] : "";
            $this->_segment_id = isset($out_segment[3]) ? $out_segment[3] : "";


            if (!empty($this->_segment_controller)) {
                $ex2Model = $this->_model_path . "SYSEntityType";
                $ex2Model = new $ex2Model;

                //This is for
                $convert_identifier  = $this->_customizeRequestedIdentifier($request,$this->_segment_controller);
                $this->_segment_controller = ($convert_identifier) ? $convert_identifier :  $this->_segment_controller;

                $check_exists = $ex2Model
                    ->where("identifier", "=", $this->_segment_controller)
                    ->whereNull("deleted_at")
                    ->first();

                if ($check_exists) {
                    $this->_entity_controller = $check_exists;
                    if(!isset( $this->_assignData['s_title'] )){
                        $this->_assignData['s_title'] = $this->_entity_controller->title;
                    }

                   // $ApiMethodField = new ApiMethodField();
                  //  $this->_attribute_fields = $ApiMethodField->getEntityAttributeList($this->_entity_controller->entity_type_id);

                }
            }
        }
        //model path
    }

    /**
     * auto controller
     *
     * @return type Array()
     */
    public function index(Request $request)
    {
        if ($this->_entity_controller) {
            if (method_exists($this, $this->_segment_action)) {
                return call_user_func(array($this, $this->_segment_action), $request);
            } else {
                if(isset($this->_entity_controller->template) && (strpos($this->_entity_controller->template, 'one_time_add') !== false)){
                    return call_user_func(array($this, 'addUpdate'), $request);
                }else{
                    return call_user_func(array($this, 'listing'), $request);
                }
            }
        } else {
            //redirect to dashboard
        }
    }

    /**
     * Listing Entities
     * @param Request $request
     * @return mixed|View
     */
    public function listing(Request $request)
    {
        $this->_assignData['module'] = $this->_object_identifier . "/" . $this->_entity_controller->identifier;
        $this->_assignData['module_identifier'] = $this->_entity_controller->title;

        $this->_assignData['columns']['ids'] = '<div class="checkbox-t"><input type="checkbox" id="check_all" name="check_all" /><label for="check_all"></label></div>';

        $this->_assignData['entity_data'] = (object)$this->_entity_controller['attributes'];

        if($this->_entity_controller->show_gallery  == 1) {

           $this->_assignData['columns']['image'] = 'Image';
        }

        //check if request from notification then update is read
        $entity_notification = new EntityNotification();
        $entity_notification->updateNotificationRead($request->all());

        $api_method_fields_model = new ApiMethodField();
        $list_attribute_fields = $api_method_fields_model->getEntityAttributeList($this->_entity_controller->entity_type_id,true);

        if (!empty($list_attribute_fields)) {
            foreach ($list_attribute_fields as $key => $listing_field) {

                if (!empty($listing_field->attribute_title)) $listing_field->frontend_label = $listing_field->attribute_title;

                if ($listing_field->entity_attr_show_in_list == "1") {

                    /* check if entity attribute has front label then display it as field title otherwise attribute field*/
                    if(isset($listing_field->entity_attr_frontend_label) && !empty($listing_field->entity_attr_frontend_label)){
                        $field_title = $listing_field->entity_attr_frontend_label;
                    }
                    else{
                        $field_title = $listing_field->frontend_label;
                    }

                    $this->_assignData['columns'][$listing_field->attribute_code] = $field_title;
                    $this->_assignData['listing_columns'][] = $listing_field;
                }

                /* Display auth columns after first two columns if entity type is user management*/
                if($this->_assignData['entity_data']->allow_auth == 1 || $this->_entity_controller->allow_backend_auth == 1){

                    if($key == 1){

                        if($this->_entity_controller->identifier == "business_user"){
                            $this->_assignData['columns']['parent_role_id'] = 'Department';
                            $this->_assignData['columns']['role_id'] = 'Designation';
                        }

                        $this->_assignData['columns']['email'] = 'Email';
                        $this->_assignData['columns']['mobile_no'] = 'Contact #';
                    }
                }

                if ($listing_field->show_in_search == "1") {
                    $this->_assignData['search'][$listing_field->attribute_code] = $listing_field->frontend_label;
                }


                if($key == 0 && $this->_entity_controller->identifier == "order"){
                    $this->_assignData['columns']['mobile_no'] = 'Contact Number';
                }

            }
        }

        if($this->_entity_controller->identifier == "item"){

            if(isset($request->is_other))
                $this->_assignData['is_other'] = $request->is_other;

            if($request->is_other == 1){
                $this->_assignData['columns']['other_item_count'] = 'Other Item Count';
            }
        }

        if (!empty($this->_listing_fields)) {
            foreach ($this->_listing_fields as $listing_field) {
                if ($listing_field->name == "created_at" || $listing_field->name == "updated_at") {
                    $this->_assignData['columns'][$listing_field->name] = $listing_field->description;
                }
            }
        }
        if($this->_entity_controller->identifier == "order"){
            $this->_assignData['columns']['created_at'] = 'Order Date';
        }
        else{
            $this->_assignData['columns']['created_at'] = 'Created On';
        }
        $this->_assignData['entity_data'] = (object)$this->_entity_controller['attributes'];

        $this->_assignData['entity_data']->identifier = $this->_entity_controller->identifier;
        $checkPermission = \DB::table('sys_entity_type')->select('add_permission', 'delete_permission', 'update_permission', 'view_permission','import_permission','export_permission')->where('entity_type_id', $this->_assignData['entity_data']->entity_type_id)->first();

        if ($checkPermission->add_permission == 1 || $checkPermission->update_permission == 1 || $checkPermission->view_permission == 1) {
            $this->_assignData['columns']['options'] = 'Options';
        }

        //        if($request->segment(3)){
        //            $this->_assignData['entity_type_id'] = $request->segment(3);
        //        }

        $this->_assignData['add_permission']    = $checkPermission->add_permission;
        $this->_assignData['delete_permission'] = $checkPermission->delete_permission;
        $this->_assignData['update_permission'] = $checkPermission->update_permission;
        $this->_assignData['import_permission'] = $checkPermission->import_permission;
        $this->_assignData['export_permission'] = $checkPermission->export_permission;

        if($this->_entity_controller->identifier == "item" && $request->is_other == 1) {
            $this->_assignData['add_permission'] = "";
        }

        //if listing customer whose are blacklisted then hide delete button
        if($this->_entity_controller->identifier == "customer"){

            if(isset($request->is_blacklist)){
                $this->_assignData['is_blacklist'] = $request->is_blacklist;
                $this->_assignData['delete_permission'] = "";
            }

            if(isset($request->user_status))
                $this->_assignData['user_status'] = $request->user_status;

        }



        $view_file = $this->_getListView( __FUNCTION__);

        if(isset($this->_assignData['template'])){

            if ($request->do_post == 1) {
                if(isset($_POST['bulk_entity_raw'])){
                    return $this->_addBulkEntity($request);
                }
                else{
                    return $this->_add($request);
                }
            }
            else{
                $this->add($request);
            }
        }

       if($request->do_export == 1){
            $this->export($request);
        }


        $this->_assignData['allow_export'] = $checkPermission->export_permission == 1 ?array($this->_entity_controller->identifier) : [];
        $this->_assignData['allow_import'] = $checkPermission->import_permission == 1 ?array($this->_entity_controller->identifier) : [];

        (empty($view_file)) ? $view_file = $this->_assignData["dir"] . __FUNCTION__ : $view_file = $view_file;
        $view = View::make($view_file, $this->_assignData);
        return $view;
    }


    public function getTitle($attObj)
    {
        if (isset($attObj->name)) {
            $attObj = $attObj->name;
        } elseif (isset($attObj->title)) {
            $attObj = $attObj->title;
        } elseif (isset($attObj->option)) {
            $attObj = $attObj->option;
        }elseif (isset($attObj->value)) {
            $attObj = $attObj->value;
        }
        return $attObj;
    }

    /**
     * Ajax Listing
     *
     * @return json
     */
    public function ajaxListing(Request $request)
    {
        $this->_assignData['module'] = $this->_object_identifier . "/" . $this->_entity_controller->identifier;

        // datagrid params : sorting/order
        //$search_value = isset($_REQUEST['search']['value']) ? $_REQUEST['search']['value'] : '';
       // $search_value = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : '';
        $dg_order = isset($_REQUEST['order'][0]['column']) ? $_REQUEST['order'][0]['column'] : '';
        $dg_sort = isset($_REQUEST['order'][0]['dir']) ? $_REQUEST['order'][0]['dir'] : '';
        $dg_columns = isset($_REQUEST['columns']) ? $_REQUEST['columns'] : '';

        $search_columns = isset($_REQUEST['search_columns']) ? $_REQUEST['search_columns'] : array();

       /* if (count($_REQUEST['search_columns']) > 0) {
            if (isset($_REQUEST['search_columns']) && is_array($_REQUEST['search_columns'])) {
                foreach ($_REQUEST['search_columns'] as $columns) {
                   // $search_columns[$columns] = $search_value;
                }
            }
        }*/

        // default ordering
        if ($dg_order == "" && $dg_sort == "") {
            $dg_order = "entity_id";
            $dg_sort = "desc";
        } else {
            // fix invalid column
            $dg_order = $dg_order == 0 ? 1 : $dg_order;
            // get column field name
            $dg_order = $dg_columns[$dg_order]["data"];
            // fix joined column name
            $dg_order = str_replace("|", ".", $dg_order);
        }

        // perform select actions
        //$this->_selectActions($request);
        // init output
        $records = array();
        $records["data"] = array();

        if ($request->select_action == 'delete') {

            //check delete permission
            $return = $this->_checkActionPermission($this->_entity_controller->identifier,'delete',$request->all());
            if(isset($return['error']) && $return['error'] == 1){
                $records["message"] = $return['message'];
            }
            else{
                $this->_selectActions($request);
            }

        }

        $dg_limit = intval($_REQUEST['length']);
        $dg_limit = $dg_limit < 0 ? $total_records : $dg_limit;
        $dg_start = intval($_REQUEST['start']);
        $dg_draw = intval($_REQUEST['draw']);
        $dg_end = $dg_start + $dg_limit;

        $search_columns['limit']     = $dg_limit;
        $search_columns['offset']    = $dg_start;
        $search_columns['order_by']  = $dg_order;
        $search_columns['sorting']   = $dg_sort;

        $search_columns['entity_type_id'] = $request->entity_type_id;

        if(isset($search_columns['from_date']) && isset($search_columns['to_date'])){

            $fromDate = $search_columns['from_date'];
            $toDate   = $search_columns['to_date'];
            $search_columns['where_condition'] = " AND Date(created_at) BETWEEN '$fromDate' AND '$toDate' ";
        }


       /* if entity type is item then list records by item type*/
        if(trim($this->_entity_controller->identifier) == "item"){
            //update the url for update, view  and delete
            $search_columns['is_other'] = $request->is_other;

            $requested_identifier = ProductHelper::getRequestedIdentifierByType($request->is_other);
            $this->_assignData['module'] = $this->_object_identifier . "/" . $requested_identifier;
        }


      /*  if($this->_entity_controller->identifier == "customer"){
            if(isset($request->user_status) && $request->user_status == 3){
                $search_columns['user_status'] = 3;
                $requested_identifier = 'blacklist_customer';
                $this->_assignData['module'] = $this->_object_identifier . "/" . $requested_identifier;
              //  $this->_assignData['requested_identifier'] = $requested_identifier;
            }

        }*/

       /* if customer entity type check for status only blacklist*/
        $sub_link = "";
       // echo "<pre>"; print_r($search_columns); exit;
       // $data = $this->__internalCall($request,\URL::to(DIR_API) . '/system/' . $this->_object_identifier . '/listing', 'GET', $search_columns,false);
        $data = (object)$this->_pLib->apiList($search_columns);
        $data = json_decode(json_encode($data));

        $total_records = 0;
        if (isset($data->data->page->total_records) && $data->data->page->total_records > 0) {

           // print_r($data);exit;
            $this->_assignData['records'] = $data->data->{$this->_object_identifier_list . '_listing'};

            $api_method_fields_model = new ApiMethodField();
            $list_attribute_fields = $api_method_fields_model->getEntityAttributeList($this->_entity_controller->entity_type_id,true);

            // get total records count

            $total_records = $data->data->page->total_records; // total records
            //$total_records = count($query->get()); // total records
            // datagrid settings
            $dg_end = $dg_end > $total_records ? $total_records : $dg_end;

            $paginated_ids = $this->_assignData['records'];

            // if records
            if (isset($paginated_ids[0])) {
                // Check Permissions
                //$perm_update = $this->_assignData["admin_module_permission_model"]->checkAccess($this->_module, "update", \Session::get($this->_entity_session_identifier.'auth')->admin_group_id);
                //$perm_del = $this->_assignData["admin_module_permission_model"]->checkAccess($this->_module, "delete", \Session::get($this->_entity_session_identifier.'auth')->admin_group_id);
                // collect records
                $i = 0;
                if($this->_entity_controller->identifier == "order"){
                    $order_status_lib = new OrderHelper();
                    $status_completed_id = $order_status_lib->getOrderStatusIdByKeyword('completed');
                }



                $checkPermissionOnButtons = \DB::table('sys_entity_type')->select('delete_permission', 'update_permission', 'view_permission')->where('entity_type_id', $request->entity_type_id)->first();

                foreach ($paginated_ids as $paginated_id) {

                    //$id_record = $this->_model->get($paginated_id->{$this->_pk});

                    // status html
                    $status = "";
                   $is_update = $this->_checkUpdateOption($paginated_id);
                   $is_delete = $this->_checkDeleteOption($paginated_id);
                    if ($checkPermissionOnButtons->update_permission || $checkPermissionOnButtons->delete_permission) {
                        // options html
                        $options = '<div class="btn-group">';
                    } else {
                        $options = '';
                    }
                    // selectbox html
                    $checkbox = '<div class="checkbox-t">';
                    // manage options
                    // - update

                    if ($checkPermissionOnButtons->update_permission && $is_update) {
                        $options .= '<a class="btn btn-xs btn-default mr5" type="button" href="' . \URL::to($this->_panelPath . $this->_assignData['module'] . '/update/' . $paginated_id->{$this->_attribute_pk}.$sub_link) . '" data-toggle="tooltip" title="Update" data-original-title="Update"><i class="fa fa-pencil"></i></a>';
                        // $options .= '<a class="btn btn-xs btn-default mr5 updateBtn" type="button" ><i class="fa fa-pencil"></i></a>';
                       // $options .= '<a id="view_popup" class="btn btn-sm btn-default active-animation item-checked" data-effect="mfp-slideDown" data-toggle="tooltip" title="Update" data-original-title="Assign"><i class="fa fa-forward"></i></a>';

                    }

                    if($this->_entity_controller->identifier == "order"){

                        if($paginated_id->attributes->order_status->id != $status_completed_id){
                            $options .= '<a id="view_popup" class="btn btn-xs mr5 btn-default" data-order-id="'.$paginated_id->entity_id.'"  data-toggle="modal" title="Update Order Status" data-original-title="Update Order Status"><i class="fa fa-forward"></i></a>';

                        }
                        $options .= '<a href="'. \URL::to($this->_panelPath . $this->_assignData['module'] . "/order-history/" . $paginated_id->{$this->_attribute_pk}.$sub_link) .'" id="view_popup" class="btn btn-xs btn-default" data-order-id="'.$paginated_id->entity_id.'"  data-toggle="modal" title="Order History" data-original-title="Order History"><i class="fa fa-history"></i></a>';
                    }

                    if(in_array($this->_entity_controller->identifier,array('driver'))){
                        $options .= '<a id="" class="btn btn-xs btn-default mr5 view_stats" data-driver-id="'.$paginated_id->entity_id.'"  data-toggle="modal" title="Order Statistics" data-original-title="Order Statistics"><i class="fa fa-shopping-cart"></i></a>';
                    }

                    if($this->_entity_controller->identifier == "item" && $request->input('is_other') == 1){
                        $options .= '<a id="move_item" class="btn btn-xs btn-default mr5 move_item" data-order-id="'.$paginated_id->entity_id.'" title="Move Item" data-original-title="Move Item"><i class="fa fa-arrows"></i></a>';
                    }
                    if ($checkPermissionOnButtons->delete_permission && empty($sub_link) && $is_delete) {
                        $options .= '<a data-module_url="delete" class="btn btn-xs btn-default grid_action_del delete_action mr5" type="button" data-toggle="tooltip" title="" data-original-title="Delete"><i class="fa fa-times"></i></a>';
                    }
                    if ($checkPermissionOnButtons->view_permission) {
                        $options .= '<a class="btn btn-xs btn-default mr5" type="button" href="' . \URL::to($this->_panelPath . $this->_assignData['module'] . '/view/' . $paginated_id->{$this->_attribute_pk}.$sub_link) . '" data-toggle="tooltip" title="View" data-original-title="View"><i class="fa fa-eye"></i></a>';
                     }


                    $checkbox .= '<input type="checkbox" id="check_id_' . $paginated_id->{$this->_attribute_pk} . '" name="check_ids[]" value="' . $paginated_id->{$this->_attribute_pk} . '" />';
                    $checkbox .= '<label class="deleted_btn" for="check_id_' . $paginated_id->{$this->_attribute_pk} . '"></label>';
                    $options .= '</div>';
                    $checkbox .= '</div>';

                    $list["ids"] = $checkbox;

                    $time_zone = \App\Libraries\Datetime::getTimeZone();

                    // collect data
                    if (!empty($list_attribute_fields)) {
                        foreach ($list_attribute_fields as $listing_field) {

                            if ($listing_field->entity_attr_show_in_list == "1") {

                                $att_code = $listing_field->attribute_code;
                                $list[$listing_field->attribute_code] = ($paginated_id->attributes->{$att_code} == '') ? '' : $paginated_id->attributes->{$att_code};
                                //Get Attribute value to display
                                $list[$listing_field->attribute_code] = EntityHelper::parseAttributeToDisplay($list[$listing_field->attribute_code],$listing_field);

                                //check if type is date
                                if(isset($listing_field->data_type_identifier) && $listing_field->data_type_identifier == "date"){

                                    //if($this->_entity_controller->identifier == "order" && $listing_field->attribute_code == 'pickup_date_cst'){
                                        switch($listing_field->attribute_code){
                                            case"pickup_date":
                                                try {
                                                    $IST = new DateTime($list[$listing_field->attribute_code] . " " . $paginated_id->attributes->pickup_time);$IST->setTimezone(new DateTimeZone(APP_TIMEZONE));
                                                    $date = $IST->format(DATE_FORMAT_ADMIN);

                                                   /* $date = \App\Libraries\Datetime::convertUTCToLocal($list[$listing_field->attribute_code] . " " . $paginated_id->attributes->pickup_time,$time_zone);*/
                                                }catch (\Exception $exception){

                                                }
                                        break;
                                            default:
                                                try {
                                                    $date = (!empty($list[$listing_field->attribute_code])) ? date(DATE_TIME_FORMAT_ADMIN, strtotime($list[$listing_field->attribute_code])) : $list[$listing_field->attribute_code];
                                                    $IST = new DateTime($date);
                                                    $IST->setTimezone(new DateTimeZone(APP_TIMEZONE));
                                                    $date = $IST->format(DATE_TIME_FORMAT_ADMIN);

                                                    //$date =  \App\Libraries\Datetime::convertUTCToLocal($date,$time_zone);
                                                }catch (\Exception $exception){

                                                }
                                            break;
                                        }
                                       
                                    //}else{
                                    //    $date = (!empty($list[$listing_field->attribute_code])) ? date(DATE_FORMAT_ADMIN, strtotime($list[$listing_field->attribute_code])) : $list[$listing_field->attribute_code];
                                    //}

                                    $list[$listing_field->attribute_code] = $date;
                                }
  
                                if(isset($listing_field->data_type_identifier) && $listing_field->data_type_identifier == "time"){
                                    try {
                                        $IST = new DateTime($list[$listing_field->attribute_code]);
                                        $IST->setTimezone(new DateTimeZone(APP_TIMEZONE));
                                        $list[$listing_field->attribute_code] = $IST->format(TIME_FORMAT_ADMIN);

                                        /*$list[$listing_field->attribute_code] =  \App\Libraries\Datetime::convertUTCToLocal($list[$listing_field->attribute_code],$time_zone);*/
                                        // $list[$listing_field->attribute_code] = (!empty($list[$listing_field->attribute_code])) ? date(TIME_FORMAT_ADMIN, strtotime($list[$listing_field->attribute_code])) : $list[$listing_field->attribute_code];
                                    }catch (\Exception $exception){

                                    }
                                }
                            }
                        }
                    }
                   
                    if (!empty($this->_listing_fields)) {
                        foreach ($this->_listing_fields as $listing_field) {
                            switch ($listing_field->name) {
                                case "created_at":
                                case "updated_at":
                                    $IST = new DateTime($paginated_id->{$listing_field->name});    
                                    $IST->setTimezone(new DateTimeZone(APP_TIMEZONE));
                                    ////date(DATE_FORMAT_ADMIN, strtotime($paginated_id->{$listing_field->name}))
                                    $list[$listing_field->name] = $IST->format(DATE_FORMAT_ADMIN);
                                    break;
                                default:
                                    //$list[$listing_field->name] = empty($paginated_id->{$listing_field->name}) ? '' : $paginated_id->{$listing_field->name};
                                    break;
                            }
                        }
                    }
 
                    if (isset($paginated_id->created_at)) {

                        $IST = new DateTime($paginated_id->created_at);    
                        $IST->setTimezone(new DateTimeZone('US/Central'));
                        // date(DATE_FORMAT_ADMIN, strtotime($paginated_id->created_at));
                        $list['created_at'] = $IST->format(DATE_FORMAT_ADMIN);
                    }

                    $list["options"] = $options;

                    //display column as entity type requirement
                    $list = $this->_entityListColumns($paginated_id,$list);
                    $records["data"][] = $list;
                    // increament
                    $i++;
                }
            }
        }

        $records["draw"] = $dg_draw;
        $records["recordsTotal"] = $total_records;
        $records["recordsFiltered"] = $total_records;


        echo json_encode($records);
    }

    /**
     * Add
     *
     * @return view
     */
    public function add(Request $request)
    {
        //Checking module Authentication
        // page action
        $this->_assignData["dir"] = config("panel.DIR") . $this->_object_identifier . '/';
        if($this->_entity_controller->allow_auth == 1 && $this->_entity_controller->allow_backend_auth == 1){
            $data = $this->load_params('entity_auth', 'post');
        }
        else{
            $data = $this->load_params('system/' . $this->_object_identifier, 'post');

            $dependEntityTypeData = array();
            if (isset($this->_entity_controller->depend_entity_type) && is_numeric(trim($this->_entity_controller->depend_entity_type))) {

                $ex2Model = $this->_model_path . "SYSEntityType";
                $ex2Model = new $ex2Model;
                $dependEntityTypeData = $ex2Model->getEntityTypeById($this->_entity_controller->depend_entity_type);


            }

            $this->_assignData['depend_entity_type_data'] = $dependEntityTypeData;

        }

        $this->_assignData['records'] = $data['records'];

        $this->_assignData["page_action"] = ucfirst(__FUNCTION__);
        $this->_assignData["route_action"] = strtolower(__FUNCTION__);

        // validate post form
        if(!empty($this->_entity_controller->template)){
            if ($request->do_post == 1) {

                if(isset($_POST['bulk_entity_raw'])){
                    return $this->_addBulkEntity($request);
                }
                else{
                    return $this->_add($request);
                }
            }
        }


        $this->_assignData['entity_data'] = (object)$this->_entity_controller['attributes'];
        $this->_assignData["entity_data"]->identifier = $this->_entity_controller->identifier;

        $view_file = $this->_assignData["dir"] . __FUNCTION__;

        /*Check if any template exist with entity type then render file from template dir*/
        if(!empty($this->_entity_controller->template)){
            if(View::exists($this->_assignData["dir"] .$this->_entity_controller->template.'/'. __FUNCTION__)){
                $view_file = $this->_assignData["dir"] .$this->_entity_controller->template.'/'. __FUNCTION__;
            }

        }

        $this->_assignData['add_view'] = $view_file;
        // call hook
        $hook_data = $this->_assignData;
        $hook_data['records'] = $data['records'];
        $this->_assignData = CustomHelper::hookData($this->_extHook, __FUNCTION__, $request, $hook_data);

        if(!empty($this->_entity_controller->template)){
            $view = View::make($view_file, $this->_assignData);
            return $view;
        }

    }

    /**
     * Add (private)
     *
     * @return view
     */
    private function _add(Request $request)
    {
       $return = $this->_checkActionPermission($this->_entity_controller->identifier,'add',$request->all());
       if(isset($return['error']) && $return['error'] == 1){
           return $return;
       }

        $_POST["created_at"] = date("Y-m-d H:i:s");

        foreach ($_POST as $key => $value) {
            if (is_array($value) && $key != "depend_entity") {
                $sm_values = "";
                foreach ($value as $_data) {
                    if(!is_array($_data))
                        $sm_values .= (($sm_values != "") ? ',' : '') . $_data;
                }
                $_POST[$key] = $sm_values;
            }
        }
        //if user management is called then call to auth create
        if($this->_entity_controller->allow_auth == 1 && $this->_entity_controller->allow_backend_auth == 1){
            //send extra parameters with request
            $request->request->add(['entity_type_id'=>$this->_entity_controller->entity_type_id,'is_auth_exists' => 0]);
            $ret = $this->__internalCall($request, \URL::to(DIR_API) ."/entity_auth/","post",$request->all());

        }
        else{
            $ret = (object)$this->_pLib->apiPost($request->all());
           // $ret =  $this->__internalCall($request,\URL::to(DIR_API) . '/system/' . $this->_object_identifier, 'POST', $request->all());
        }

         $this->_addCustomRedirect($request);

        if ((isset($ret->error) && $ret->error == "1") || (isset($ret->response) && $ret->response == "error")) {

            $assignData['error'] = 1;
            $assignData['message'] = $ret->message;
            return $assignData;

        } else {
            //redirect
            //if custom redirection is defined then move to that page
            if(isset($this->_assignData['custom_redirect'])){
                $redirect = $this->_assignData['custom_redirect'];
            }else{
                $redirect = \URL::to($this->_panelPath . $this->_object_identifier . "/" . $this->_entity_controller->identifier);
            }
            $assignData['error'] = 0;
            $assignData['message'] = $ret->message;
            $assignData['redirect'] = $redirect;
            return $assignData;
        }
    }

    /**
     * Update
     *
     * @return view
     */
    public function update(Request $request)
    {
        $checkPermission = \DB::table('sys_entity_type')->select('add_permission', 'delete_permission', 'update_permission', 'view_permission')->where('entity_type_id', $this->_entity_controller['attributes']['entity_type_id'])->first();
        
        $this->_assignData['modulePermission'] = $checkPermission;

        $array = explode('/',$request->url());
        end($array);
        $this->_assignData["uri_method"] =  prev($array);

        // page action
        if($this->_assignData["uri_method"] != 'view'){
            $this->_assignData["page_action"] = ucfirst(__FUNCTION__);
            $this->_assignData["route_action"] = strtolower(__FUNCTION__);
        }else{
            $this->_assignData["page_action"] = ucfirst($this->_assignData["uri_method"]);
            $this->_assignData["route_action"] = ucfirst($this->_assignData["uri_method"]);

            //check if request from notification then update is read
            $entity_notification = new EntityNotification();
            $entity_notification->updateNotificationRead($request->all());
        }

        // validate post form
        if (isset($request->do_post)) {

            if(isset($_POST['bulk_entity'])){
                return $this->_updateBulkEntity($request);
            }
            else{
                if (method_exists($this, "_".$this->_entity_controller->identifier."Update")) {
                    $func = "_".$this->_entity_controller->identifier."Update";
                    return $this->$func($request);
                }
                else{
                    return $this->_update($request);
                }

            }

        }

        $getData['entity_id'] = $this->_segment_id;
        $getData['entity_type_id'] = $this->_entity_controller['attributes']['entity_type_id'];

        if($this->_entity_controller->identifier == "order"){
            $hook[] = 'order_pickup';
            $hook[] = 'order_dropoff';
            if($this->_assignData["uri_method"] == 'view'){

                $flatTable = new FlatTable;
                $getDriverTrackingLocation = $flatTable->getDriverLocation($getData['entity_id']);
                $location = array();

                if(count($getDriverTrackingLocation)){
                    $location = $getDriverTrackingLocation->driver_location;
                }
                $this->_assignData["location"] = $location;
            }
        }

        //Get depend entity type data
        $depend_entity = false;

        if (isset($this->_entity_controller->depend_entity_type) && is_numeric(trim($this->_entity_controller->depend_entity_type)))
        {
            $ex2Model = $this->_model_path . "SYSEntityType";
            $ex2Model = new $ex2Model;
            $depend_entity_type_data = $ex2Model->getEntityTypeById($this->_entity_controller->depend_entity_type);
            $this->_assignData['depend_entity_type_data'] = $depend_entity_type_data;

            if(isset($depend_entity_type_data->identifier)){

                $depend_entity = true;
                $hook[] = $depend_entity_type_data->identifier;
            }

        }

        if(isset($hook)){
            $getData['hook'] = implode(',',$hook);
        }

       // $entity_data = $this->__internalCall($request,\URL::to(DIR_API) . '/system/' . $this->_object_identifier.'/listing' , 'GET', $getData);
        $data = (object)$this->_pLib->apiList($getData);
        $entity_data = json_decode(json_encode($data));

   //  echo "<pre>"; print_r($entity_data); exit;
        if ($entity_data) {
            $this->_assignData["update"] = $entity_data->data->entity_listing[0];
            $this->_assignData["update"]->identifier = $this->_entity_controller->identifier;


            if($depend_entity && isset($this->_assignData["update"]->{$depend_entity_type_data->identifier})){
                $this->_assignData['depend_update'] = $this->_assignData["update"]->{$depend_entity_type_data->identifier};
                //echo "<pre>"; print_r( $this->_assignData['depend_update']); exit;

            }

            if(isset($this->_assignData["update"]->auth)){

                if(isset($this->_assignData["update"]->auth->entity_auth_id)){

                    if($this->_assignData["update"]->auth->entity_auth_id > 0){

                        $roleMapModel = new SYSEntityRoleMap();
                        $role = $roleMapModel->getRoleInfoByEntity($this->_assignData["update"]->entity_id);
                        if ($role) {
                            if(isset($role->role_id)){
                                $this->_assignData["update"]->auth->role_id = isset($role->role_id) ? $role->role_id : "";
                                $this->_assignData["update"]->auth->parent_role_id = isset($role->parent_id) ? $role->parent_id : "";
                            }
                        }
                    }
                }

            }
        }
      //  echo "<pre>"; print_r( $this->_assignData["update"]); exit;

        $this->_assignData["dir"] = config("panel.DIR") . $this->_object_identifier . '/';

        if($this->_entity_controller->allow_auth == 1 && $this->_entity_controller->allow_backend_auth == 1){
            $data = $this->load_params('entity_auth', 'post');
        }
        else{
            $data = $this->load_params('system/' . $this->_object_identifier . '/update', 'post');
        }

        $this->_assignData['records'] = $data['records'];


        $this->_assignData['entity_data'] = (object)$this->_entity_controller['attributes'];
        $view_file = $this->_assignData["dir"] . __FUNCTION__;
        $this->_assignData['form_template_dir'] = "template/";
        if($this->_entity_controller->identifier == "order"){
            $this->_assignData['form_template_dir'] = $this->_entity_controller->template;
        }

        /*Check if any template exist with entity type then render file from template dir*/
        if(!empty($this->_entity_controller->template)){
            if(View::exists($this->_assignData["dir"] .$this->_entity_controller->template.'/'. __FUNCTION__)){
                $view_file = $this->_assignData["dir"] .$this->_entity_controller->template.'/'. __FUNCTION__;

            }

        }
        else{
            if($this->_entity_controller->show_gallery){
                $view_file = $this->_assignData["dir"] .'advance/'. __FUNCTION__;
            }

        }
        // call hook
        $hook_data = $this->_assignData;
        $hook_data['update_data'] = $getData;
        $hook_data['records'] = $data['records'];
        $hook_data['update'] = isset($this->_assignData["update"]) ? $this->_assignData["update"] : false;
        $this->_assignData = CustomHelper::hookData($this->_extHook, __FUNCTION__, $request, $hook_data);

        $view = View::make($view_file, $this->_assignData);
        return $view;
    }

    /**
     * Update (private)
     *
     * @return view
     */
    private function _update(Request $request)
    {
        $return = $this->_checkActionPermission($this->_entity_controller->identifier,'update',$request->all());
        if(isset($return['error']) && $return['error'] == 1){
            return $return;
        }

        if(isset($_POST['bulk_entity_raw'])){
            $this->_assignData["update"]->bulk_entity_raw = $_POST['bulk_entity_raw'];
            unset($_POST['bulk_entity_raw']);

        }

       /* foreach ($_POST as $key => $value) {
            if (is_array($value) && $key != "depend_entity") {
                $sm_values = "";
                foreach ($value as $_data) {
                    $sm_values .= (($sm_values != "") ? ',' : '') . $_data;
                }
                $_POST[$key] = $sm_values;
            }

        }*/

            /* if customer entity type check for status only blacklist*/
            if($this->_entity_controller->identifier == "customer") {
                if (isset($request->is_blacklist)) {
                    unset($request->is_blacklist);
                    $custom_redirect = \URL::to($this->_panelPath . $this->_object_identifier . "/" . $this->_entity_controller->identifier."/blacklist");
                }
            }

        $request->request->add(['entity_type_id'=>$this->_entity_controller->entity_type_id,'entity_id' => $this->_segment_id]);
       // $_POST['entity_id'] = $this->_segment_id;
        //$_POST['entity_type_id'] = $this->_entity_controller->entity_type_id;

       // $this->_assignData["update"] = $this->__internalCall($request,\URL::to(DIR_API) . '/system/' . $this->_object_identifier . '/update', 'POST', $_POST);
        $this->_assignData["update"]  = (object)$this->_pLib->apiUpdate($request->all());

        if ($this->_assignData["update"]->error == "1") {

            $assignData['error'] = 1;
            $assignData['message'] = $this->_assignData["update"]->message;
            return $assignData;

        } else {
            \Session::put(ADMIN_SESS_KEY . 'success_msg', $this->_assignData["update"]->message);
            //redirect
            $this->_addCustomRedirect($request);
            //if custom redirection is defined then move to that page
            if(isset($this->_assignData['custom_redirect'])){
                $redirect = $this->_assignData['custom_redirect'];
            }
            else if(isset($custom_redirect)){
                $redirect = $custom_redirect;
            }
            else{  //move to update page
                $redirect = \URL::to($this->_panelPath . $this->_object_identifier . "/" . $this->_entity_controller->identifier);
            }

            $assignData['error'] = 0;
            $assignData['message'] = $this->_assignData["update"]->message;
            $assignData['redirect'] = $redirect;
            return $assignData;

        }

    }

    public function view(Request $request)
    {
        //Get entity Attributes
        $attribute_fields = $this->_apiMethodFieldModel->getEntityAttributeList($this->_entity_controller->entity_type_id);

        // page action
        $this->_assignData["page_action"] = ucfirst(__FUNCTION__);
        $this->_assignData["route_action"] = strtolower(__FUNCTION__);

        // validate post form
        if (isset($request->do_post)) {
            return $this->_update($request);
        }

        //check if request from notification then update is read
        $entity_notification = new EntityNotification();
        $entity_notification->updateNotificationRead($request->all());

        $getData['entity_id'] = $this->_segment_id;
        $getData['entity_type_id'] = $this->_entity_controller['attributes']['entity_type_id'];

        //Get columns labels of entity those have to list on view page
        if (!empty($attribute_fields)) {
            foreach ($attribute_fields as $listing_field) {

                if($listing_field->view_at == 3 ) continue;

                if (!empty($listing_field->attribute_title)) $listing_field->frontend_label = $listing_field->attribute_title;

                /* check if entity attribute has front label then display it as field title otherwise attribute field*/
                if(isset($listing_field->entity_attr_frontend_label) && !empty($listing_field->entity_attr_frontend_label)){
                    $field_title = $listing_field->entity_attr_frontend_label;
                }
                else{
                    $field_title = $listing_field->frontend_label;
                }


               // if ($listing_field->show_in_list == "1") {
                    $this->_assignData['columns'][$listing_field->attribute_code] = $field_title;
                    $this->_assignData['data_type'][$listing_field->attribute_code] = $listing_field->data_type_identifier;

                $this->_assignData['entity_fields'][$listing_field->attribute_code] = $listing_field;
              // }
            }
        }

     //   print_r( $this->_assignData['columns']); exit;

        if($this->_entity_controller->identifier == "order"){
            $getData['hook'] = $this->_assignData['dependent_entity_type'] = "order_item";
        }
        else if($this->_entity_controller->identifier == "inventory"){
            $getData['hook'] = "inventory_item_relation";
        }
        else if($this->_entity_controller->depend_entity_type > 0){

            $entity_type_model = new SYSEntityType();
            $depend_entity_type = $entity_type_model->get($this->_entity_controller->depend_entity_type);
            $getData['hook'] = $this->_assignData['dependent_entity_type'] = $depend_entity_type->identifier;
        }


       // $entity_data = $this->__internalCall($request,\URL::to(DIR_API) . '/system/' . $this->_object_identifier.'/listing', 'GET', $getData);
        $data = (object)$this->_pLib->apiList($getData);
        $entity_data = json_decode(json_encode($data));

        if ($entity_data) {
            $this->_assignData["update"] = $entity_data->data->entity_listing[0];
        }

       /* $this->_assignData["dir"] = config("panel.DIR") . $this->_object_identifier . '/';
        $data = $this->load_params('system/' . $this->_object_identifier . '/update', 'post');

        $this->_assignData['records'] = $data['records'];*/

        $this->_assignData['entity_data'] = (object)$this->_entity_controller['attributes'];

        $view_file = $this->_assignData["dir"] . __FUNCTION__;

        /*Check if any template exist with entity type then render file from template dir*/
        if(!empty($this->_entity_controller->template)){
            if(View::exists($this->_assignData["dir"] .$this->_entity_controller->template.'/'. __FUNCTION__)){
                $view_file = $this->_assignData["dir"] .$this->_entity_controller->template.'/'. __FUNCTION__;
            }
            else{
                if($this->_entity_controller->depend_entity_type > 0){

                    if(View::exists($this->_assignData["dir"] .'view-dependent')){

                        //Get entity Attributes
                        $dependent_attribute_fields = $this->_apiMethodFieldModel->getEntityAttributeList($this->_entity_controller->depend_entity_type);

                        //Get columns labels of entity those have to list on view page
                        if (count($dependent_attribute_fields) > 0) {
                            foreach ($dependent_attribute_fields as $listing_field) {

                                if($listing_field->view_at == 3 || $listing_field->data_type_identifier == 'hidden'){
                                    continue;
                                }

                                if (!empty($listing_field->attribute_title)) $listing_field->frontend_label = $listing_field->attribute_title;

                                /* check if entity attribute has front label then display it as field title otherwise attribute field*/
                                if(isset($listing_field->entity_attr_frontend_label) && !empty($listing_field->entity_attr_frontend_label)){
                                    $field_title = $listing_field->entity_attr_frontend_label;
                                }
                                else{
                                    $field_title = $listing_field->frontend_label;
                                }


                                // if ($listing_field->show_in_list == "1") {
                                $this->_assignData['dependent_columns'][$listing_field->attribute_code] = $field_title;
                                $this->_assignData['dependent_data_type'][$listing_field->attribute_code] = $listing_field->data_type_identifier;
                                // }
                            }
                        }

                        $view_file = $this->_assignData["dir"] .'view-dependent';
                    }

                }
            }
        }


        // call hook
        $hook_data = $this->_assignData;
        $hook_data['update_data'] = $getData;
        $this->_assignData = CustomHelper::hookData($this->_extHook, __FUNCTION__, $request, $hook_data);

        $view = View::make($view_file, $this->_assignData);
        return $view;
    }
    

    /**
     * Select Action
     *
     * @return query
     */
    private function _selectActions($request)
    {
        $request->select_action = trim($request->select_action);
        $request->checked_ids = is_array($request->checked_ids) ? $request->checked_ids : array();

        if ($request->select_action != "" && isset($request->checked_ids[0])) {
            foreach ($request->checked_ids as $checked_id) {
                $id = $this->_attribute_pk;
                $postData[$id] = $checked_id;
                $data = $this->__internalCall($request,\URL::to(DIR_API) . '/system/' . $this->_object_identifier . '/delete', 'POST', $postData,false);

            }
        }
    }

    /**
     * image browser
     *
     * @return view
     */
    public function imageBrowser(Request $request)
    {
        //Checking module Authentication
        // $this->_assign_data["admin_module_permission_model"]->checkModuleAuth($this->_module, $request->referrer_action, \Session::get($this->_entity_session_identifier . 'auth')->admin_group_id);
        // page action
        $this->_assign_data["page_action"] = ucfirst(__FUNCTION__);
        $this->_assign_data["route_action"] = strtolower(__FUNCTION__);
        $result = File::makeDirectory(config('constants.IMAGES_UPLOAD_PATH') . $this->_entity_controller->identifier, 0775, true, true);
        // view file
        $view_file = strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', __FUNCTION__));

        $view = View::make($this->_assignData["dir"] . $view_file, $this->_assign_data);
        return $view;
    }


    function uploadGallery()
    {
        $input = Input::all();

        $rules = array(
            'file' => 'image|max:3000',
        );

        $validation = Validator::make($input, $rules);

        if ($validation->fails()) {
            return Response::make($validation->errors->first(), 400);
        }

        $destinationPath = 'uploads'; // upload path
        $extension = Input::file('file')->getClientOriginalExtension(); // getting file extension
        $fileName = rand(11111, 99999) . '.' . $extension; // renameing image
        $upload_success = Input::file('file')->move($destinationPath, $fileName); // uploading file to given path

        if ($upload_success) {
            return Response::json('success', 200);
        } else {
            return Response::json('error', 400);
        }
    }


    public function uploadCSV(Request $request)
    {
        if ($request->hasFile('csvimport')) {
            $path = $request->file('csvimport')->getRealPath();
            $data = \Excel::load($path)->get();
            $success = 0;
            if ($data->count()) {
                foreach ($data as $key => $value) {
                    $fname = $value->first_name;
                    $lname = $value->last_name;
                    $name = $fname . ' ' . $lname;
                    $email = $value->email;
                    $password = $value->password;
                    $ch = curl_init();
                    $url = "http://localhost/cubix3/api/entity_auth";
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, 1);  //0 for a get request
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $arr[] = ['email' => $email, 'password' => $password, 'entity_type_id' => 5, 'first_name' => $value->first_name, 'last_name' => $value->last_name, 'name' => $name, 'location' =>
                        $value->location, 'is_notify' => $value->is_notify, 'about_me' => $value->about_me, 'device_type' => 'ios']);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    $success = 1;
                }
                if ($success == 1) {
                    \Session::flash('message', "Success. User's Added.");
                    return Redirect::back();
                } else {
                    \Session::flash('message', "Error. Try Again");
                    return Redirect::back();
                }
            }
        } else {

            \Session::flash('message', 'Request data does not have any files to import.');
            return Redirect::back();
        }
    }


    /**
     * @param Request $request
     * @return mixed
     */
    private function _addBulkEntity(Request $request)
    {

        $_POST["created_at"] = date("Y-m-d H:i:s");

        foreach ($_POST as $key => $value) {
            if (is_array($value)) {
                $sm_values = "";
                foreach ($value as $_data) {
                    if(!is_array($_data))
                        $sm_values .= (($sm_values != "") ? ',' : '') . $_data;
                }
                $_POST[$key] = $sm_values;
            }
        }


        $add_entity = true;
        $update_entity = false;
        if(isset($_POST['is_new_package']) && $_POST['is_new_package'] == 0){
            $add_entity = false;
            $update_entity = true;
        }

        if(isset($_POST['bulk_entity_raw'])){
            $bulk_entity_raw = $_POST['bulk_entity_raw'];
            unset($_POST['bulk_entity_raw']);

        }

        if(isset($_POST['is_new_package'])){
            unset($_POST['is_new_package_'.$bulk_entity_raw]);
            $package_id = $_POST['package_id'];
            unset($_POST['package_id']);
            unset($_POST['is_new_package']);
        }


        if($add_entity){
            //print_r($_POST); exit;
            $ret = $this->__internalCall($request,\URL::to(DIR_API) . '/system/' . $this->_object_identifier, 'POST', $_POST,false);
        }

        if($update_entity){
            $_POST['entity_id'] = $package_id;
            unset($_POST['title']);

            // print_r($_POST); exit;
            $ret = $this->__internalCall($request,\URL::to(DIR_API) . '/system/' . $this->_object_identifier . '/update', 'POST', $_POST,false);
        }

        if ((isset($ret->error) && $ret->error == "1") || (isset($ret->response) && $ret->response == "error")) {
            $assignData['error'] = 1;
            $assignData['message'] = $ret->message;
        } else {
            $assignData['error'] = 0;
            $assignData['message'] = $ret->message;
        }

        if($this->_entity_controller->identifier == "package"){
            //Get Item data
            $package_item_id = $_POST['package_item_id'];
            $item_entity_type_id = $_POST['item_entity_type_id'];
            $requested_entity_type = isset($this->_requested_route_params[3]) ? $this->_requested_route_params[3] : "";

            $ex2Model = $this->_model_path . "SYSEntity";
            $ex2Model = new $ex2Model;

            $post = array();
            $post['entity_type_id'] = $item_entity_type_id;
            $post['entity_id'] = $package_item_id;
            $post['mobile_json'] = 1;
            $post['_lang'] = $request->_lang;

            $data = $ex2Model->getEntityData((object)$post);

            $item_attributes = Fields::setEntityAttributes($data[$requested_entity_type]->attributes);

            $assignData['data'] = array(
                'bulk_entity_raw' => $bulk_entity_raw,
                'total_inventory' => isset($item_attributes['total_inventory']) ? $item_attributes['total_inventory'] : 0,
                'redirect' => \URL::to($this->_panelPath . $this->_object_identifier . "/" . $this->_entity_controller->identifier)
            );

            if( $assignData['error'] == 0){
                $assignData['message'] = trans('backend.bulk_entity_added', array("entity" => $request->title));
            }
        }

        return $assignData;
    }


    /**Setting page for update
     * @param Request $request
     * @return View
     */
    public function updateSetting(Request $request)
    {
        // validate post form
        if (isset($request->do_post)) {
            $this->_segment_id = $request->entity_id;
            $this->_assignData['custom_redirect'] = \URL::to($this->_panelPath . $this->_object_identifier . "/" . $this->_entity_controller->identifier . '/updateSetting');
            return $this->_update($request);
        }

        //Get General setting entity id to update setting
        $search_columns['limit'] = 1;
        $search_columns['entity_type_id'] = $this->_entity_controller->entity_type_id;

        $data = $this->__internalCall($request,\URL::to(DIR_API) . '/system/' . $this->_object_identifier . '/listing', 'GET', $search_columns,false);

        if (isset($data->data->page->total_records) && $data->data->page->total_records > 0) {
            $records = $data->data->{$this->_object_identifier_list . '_listing'};

            if(isset($records[0])){
                $this->_segment_id = $records[0]->entity_id;
                $this->_assignData['_segment_id'] =  $this->_segment_id;
            }
        }
        //after getting entity id redirect to update
        return $this->update($request);
    }


    /**
     * listing view check templates
     * @param $listing
     * @return string
     */
    private function _getListView($listing)
    {
        $view_file = "";
        /*Check if any template exist with entity type then render file from template dir*/
        if(!empty($this->_entity_controller->template)){

            if(View::exists($this->_assignData["dir"] .$this->_entity_controller->template.'/'.$listing)){
                $view_file = $this->_assignData["dir"] .$this->_entity_controller->template.'/'.$listing;

                $this->_assignData['template'] = $this->_entity_controller->template;
            }

        }
        /*Check if any template exist with entity type then render file from template dir*/
        else if(empty($this->_entity_controller->template)){

            if(View::exists($this->_assignData["dir"] .'advance/'.$listing)){
                $view_file = $this->_assignData["dir"] .'advance/'.$listing;

            }
            $this->_assignData['template'] = 'advance';

        }
        else{
            $view_file = $this->_assignData["dir"] .$listing;
        }

        return $view_file;
    }

    /**
     * Update entity for dependent
     * @param Request $request
     * @return mixed
     */
    private function _updateBulkEntity(Request $request)
    {

        $return = $this->_checkActionPermission($this->_entity_controller->identifier,'update',$request->all());
        if(isset($return['error']) && $return['error'] == 1){
            return $return;
        }

        $_POST["created_at"] = date("Y-m-d H:i:s");

    /*    foreach ($_POST as $key => $value) {
            if (is_array($value)) {
                $sm_values = "";
                foreach ($value as $_data) {
                    if(!is_array($_data) && $key != "depend_entity")
                        $sm_values .= (($sm_values != "") ? ',' : '') . $_data;
                }
                $_POST[$key] = $sm_values;
            }
        }*/
            if(!isset($_POST['entity_id'])){
                $_POST['entity_id'] = $this->_segment_id;
            }

        if(isset($_POST['bulk_entity_raw'])){
            $assignData['bulk_entity_raw'] = $_POST['bulk_entity_raw'];
            unset($_POST['bulk_entity_raw']);

        }

        if(isset($_POST['bulk_entity'])){
            unset($_POST['bulk_entity']);

        }

         $ret = $this->__internalCall($request,\URL::to(DIR_API) . '/system/' . $this->_object_identifier . '/update', 'POST', $_POST,false);

        //delete depend entity ids if requested
         if(isset($request->delete_depend_entity_id) && !empty($request->delete_depend_entity_id)){

             $depend_entity_ids = explode(',',$request->delete_depend_entity_id);

                 foreach ($depend_entity_ids as $checked_id) {
                     $id = $this->_attribute_pk;
                     $delete_entity['entity_type_id'] = $this->_entity_controller->depend_entity_type;
                     $delete_entity[$id] = $checked_id;
                     $ret = (object)$this->_pLib->apiDelete($delete_entity);

             }
         }
        //echo "<pre>"; print_r($ret); exit;

        if ((isset($ret->error) && $ret->error == "1") || (isset($ret->response) && $ret->response == "error")) {
            $assignData['error'] = 1;
            $assignData['message'] = $ret->message;
        } else {
            $assignData['error'] = 0;
            $assignData['message'] = $ret->message;
        }

        $assignData['redirect'] = \URL::to($this->_panelPath . $this->_object_identifier . "/" . $this->_entity_controller->identifier);

        return $assignData;
    }

    /**
     * List black list customer
     * @param Request $request
     * @return mixed|View
     */
    public function blacklist(Request $request)
    {
        $request->request->add(['is_blacklist'=>1]);
        return $this->listing($request);
    }

    /**
     * @param $entity_data
     * @param $list
     * @return mixed
     */
    private function _entityListColumns($entity_data,$list)
    {
       // echo "<pre>"; print_r($entity_data); exit;
        /* if entity type is user management then get few columns of auth*/
        if($this->_entity_controller->allow_auth == 1 || $this->_entity_controller->allow_backend_auth == 1){

            if($this->_entity_controller->identifier != "customer"){
                $list["role_id"] = '';
            }

            $list["email"] = '';
            $list["mobile_no"] = '';

            if(isset($entity_data->auth)){

                /* Get role title if user has assigned role*/
                if($this->_entity_controller->identifier == "business_user"){

                    $list["parent_role_id"] = "";
                    $list["role_id"] = "";

                    //get designation title
                    $roleMapModel = new SYSEntityRoleMap();
                    $role = $roleMapModel->getRoleInfoByEntity($entity_data->entity_id);
                    if ($role) {
                       if(isset($role->role_id)){
                           $list["role_id"] = isset($role->title) ? $role->title : "";

                           //get department title
                           $role_model = new SYSRole();
                           $list["parent_role_id"] =  $role_model->getRoleTitleById($role->parent_id);
                       }
                    }
                }

                $list["email"] = isset($entity_data->auth->email) ? $entity_data->auth->email : "";
                $list["mobile_no"] = isset($entity_data->auth->mobile_no) ? $entity_data->auth->mobile_no : "";

            }


        }

        if($this->_entity_controller->show_gallery == 1) {
            //Get image path for entity
            $gallery = isset($entity_data->gallery) ? $entity_data->gallery : false;
            $list["image"] = Fields::getGalleryImagePath($gallery,$this->_entity_controller->identifier,'thumb');
        }

        if($this->_entity_controller->identifier == "order"){

            $list['mobile_no'] = '';
            if(isset($entity_data->attributes->customer_id->detail->auth->mobile_no))
                $list['mobile_no'] = $entity_data->attributes->customer_id->detail->auth->mobile_no;
        }

        if($this->_entity_controller->identifier == "item" && $entity_data->attributes->is_other == 1){
            $list['other_item_count'] = $entity_data->attributes->other_item_count;
        }


        return $list;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function _orderUpdate(Request $request)
    {
        $return = $this->_checkActionPermission($this->_entity_controller->identifier,'update',$request->all());
        if(isset($return['error']) && $return['error'] == 1){
            return $return;
        }

        $post_param = $request->all();
        if(isset($request['depend_entity'])){

            $order_id = $this->_segment_id;
           $params = $request->all();
            $depend_params = $params['depend_entity'];
            unset($params['depend_entity']);
            $params['entity_id'] = $order_id;

           $entity_helper = new EntityHelper();
            //Validate Order
           $entity_validate = $entity_helper->validateEntity($params,true);
            if($entity_validate['error'] == 1){
                $assignData['error'] = 1;
                $assignData['message'] = $entity_validate['message'];
                return $assignData;
            }
            else{
                //Validate Order Items
                if(count($depend_params) > 0){

                    $existing_depend_entity_ids = array();

                    foreach($depend_params as $depend_param){

                        if(isset($depend_param['entity_id'])){
                            if(!empty($depend_param['entity_id']))
                                $existing_depend_entity_ids[] = $depend_param['entity_id'];
                        }

                        $entity_depend_validate = $entity_helper->validateEntity($depend_param);
                        if($entity_depend_validate['error'] == 1){
                            $assignData['error'] = 1;
                            $assignData['message'] = $entity_depend_validate['message'];
                            return $assignData;
                        }
                    }
                }
            }

            //Calculate Order statistic to update order
            $order_process_obj = new OrderProcess();
            $order_process_response = $order_process_obj->processRequest($order_id,$request->all());
          //  echo "<pre>"; print_r($order_process_response);
            if(isset($order_process_response['order'])){

                $entity_lib = new Entity();
                $order_process_response['order']['entity_id'] = $order_id;
                $order_param = $order_process_response['order'];

                $order_param['add_revision'] = 1;
                //Post order params to update
                $i = 0;
                //echo "<pre>"; print_r( $order_param);exit;
             //  print_r($order_param);
                $ret = (object)$entity_lib->apiUpdate($order_param);

               // $ret = $this->__internalCall($request,\URL::to(DIR_API) . '/system/' . $this->_object_identifier. '/update', 'POST',$order_param ,false);
               // $request->replace($post_param);

                if ((isset($ret->error) && $ret->error == "1") || (isset($ret->response) && $ret->response == "error")) {
                    $assignData['error'] = 1;
                    $assignData['message'] = $ret->message;
                } else {
                    //if successfully updated order then update order items
                   if(count($order_process_response['depend_entity']) > 0){
                       //First Delete order items
                       $order_item_ids = $order_process_obj->getOrderItems($order_id);
                      // print_r($existing_depend_entity_ids);

                      // print_r($order_item_ids);exit;

                       if(count($order_item_ids) > 0){
                           foreach($order_item_ids as $order_item_id){
                               if(!in_array($order_item_id,$existing_depend_entity_ids)){
                                   $delete_entity['entity_type_id'] = 16;
                                   $delete_entity['entity_id'] = $order_item_id;
                                  // $ret = $this->__internalCall($request,\URL::to(DIR_API) . '/system/' . $this->_object_identifier. '/delete', 'POST',$delete_entity ,false);
                                   $ret = (object)$entity_lib->apiDelete($delete_entity);
                                  //echo "<pre>"; print_r($ret);
                               }

                           }
                       }
                        //Now add order items
                       foreach($order_process_response['depend_entity'] as $depend_entity){

                            $assignData = $this->_addDependItems($request,$depend_entity);
                           if($assignData['error'] == 1){
                               $assignData['redirect'] = \URL::to($this->_panelPath . $this->_object_identifier . "/" . $this->_entity_controller->identifier);
                              break;
                           }
                           else{
                               $i++;
                           }
                       }
                   }

                }

                if($i == count($order_process_response['depend_entity'])){
                    \Session::put(ADMIN_SESS_KEY . 'success_msg',"success");
                    $assignData['redirect'] = \URL::to($this->_panelPath . $this->_object_identifier . "/" . $this->_entity_controller->identifier);
                }

                return $assignData;

            }
            // echo "<pre>"; print_r($order_process_response); exit;
        }

    }

    public function orderAssign(Request $request)
    {

    }

    /**
     * @param Request $request
     * @param $depend_entity
     * @return mixed
     */
    private function _addDependItems(Request $request,$depend_entity)
    {
        $entity_lib = new Entity();
        $post_param = $request->all();
       // if(isset($depend_entity['entity_id'])){
           // unset($depend_entity['entity_id']);
            if($depend_entity['entity_id'] > 0){
               // $ret_item = $this->__internalCall($request,\URL::to(DIR_API) . '/system/' . $this->_object_identifier.'/update', 'POST',$depend_entity ,false);
              //  $request->replace($post_param);
                $ret_item = (object)$entity_lib->apiUpdate($depend_entity);
               // echo "<pre>"; print_r($ret_item);
            }
               else{
                   unset($depend_entity['entity_id']);
                  // echo "<pre>"; print_r($depend_entity);
                   $ret_item = $entity_lib->apiPost($depend_entity);
                   $ret_item = json_decode(json_encode($ret_item));
                  // $ret_item = $this->__internalCall($request,\URL::to(DIR_API) . '/system/' . $this->_object_identifier, 'POST',$depend_entity ,false);
                  // $request->replace($post_param);
               }
       // }



        if(isset($ret_item)){
            if ((isset($ret_item->error) && $ret_item->error == "1") || (isset($ret_item->response) && $ret_item->response == "error")) {
                $assignData['error'] = 1;
                $assignData['message'] = $ret_item->message;
            } else {
                $assignData['error'] = 0;
                $assignData['message'] = $ret_item->message;
            }
        }
        else{
            $assignData['error'] = 1;
            $assignData['message'] = "Sorry item cannot update";
        }

        return $assignData;
    }

    /**
     * If only single page where have to add/update entity
     * @param Request $request
     * @return View
     */
    public function addUpdate(Request $request)
    {
        // validate post form
        if (isset($request->do_post)) {
           // echo "<pre>"; print_r( $request->all()); exit;

            if(isset($request->entity_action) && $request->entity_action == "update"){

                $this->_segment_id = $request->entity_id;
                $this->_assignData['custom_redirect'] = \URL::to($this->_panelPath . $this->_object_identifier . "/" . $this->_entity_controller->identifier);
                unset($request->entity_action);
                return $this->_update($request);

            }else{
                unset($request->entity_action);
                $this->_assignData['custom_redirect'] = \URL::to($this->_panelPath . $this->_object_identifier . "/" . $this->_entity_controller->identifier);
                return $this->_add($request);

            }

        }

        //check if request from notification then update is read
        $entity_notification = new EntityNotification();
        $entity_notification->updateNotificationRead($request->all());
        //Get General setting entity id to update setting
        $search_columns['limit'] = 1;
        $search_columns['entity_type_id'] = $this->_entity_controller->entity_type_id;

       // $data = $this->__internalCall($request,\URL::to(DIR_API) . '/system/' . $this->_object_identifier . '/listing', 'GET', $search_columns,false);

        $data = (object)$this->_pLib->apiList($search_columns);
        $data = json_decode(json_encode($data));

        if (isset($data->data->page->total_records) && $data->data->page->total_records > 0) {
            $records = $data->data->{$this->_object_identifier_list . '_listing'};

            if(isset($records[0])
            ){
                $this->_segment_id = $records[0]->entity_id;
                $this->_assignData['_segment_id'] =  $this->_segment_id;
            }

            //after getting entity id redirect to update
            return $this->update($request);
        }
        else{
            return $this->add($request);
        }

    }

    /**Export to Excel
     * @param Request $request
     */
    public function export(Request $request)
    {
        $requested_params = $request->all();
        unset($requested_params['do_export']);

        //Get entity Attributes
        $attribute_fields = $this->_apiMethodFieldModel->getEntityAttributeList($this->_entity_controller->entity_type_id);

        $import_export_lib = new DataImportExport();
        $entity_listing = $import_export_lib->exportEntityData($this->_entity_controller,$attribute_fields,$requested_params);

        if($entity_listing->error == 0){

            if(count($entity_listing->data)){
                //Export to Excel
                $data = $entity_listing->data;
                $file_name = $this->_entity_controller->title.'-list-'.time();
                Excel::create($file_name, function($excel) use($data) {
                    $excel->sheet('Sheet1', function($sheet) use($data) {
                      //  $sheet->fromArray($data);
                        $sheet->fromArray($data, null, 'A1', false, false);
                    });

                })->export('xls');
            }
            else{
                \Session::put(ADMIN_SESS_KEY . 'error_msg', "There is no data in List");
                $redirect = \URL::to($this->_panelPath . $this->_object_identifier . "/" . $this->_entity_controller->identifier);
                Redirect::to($redirect);
            }

           // \Session::put(ADMIN_SESS_KEY . 'error_msg', $entity_listing->message);
        }
        else{
            \Session::put(ADMIN_SESS_KEY . 'error_msg', $entity_listing->message);
            $redirect = \URL::to($this->_panelPath . $this->_object_identifier . "/" . $this->_entity_controller->identifier);
            Redirect::to($redirect);
        }

    }

    /**
     * @param Request $request
     */
    public function importTemplate($request)
    {
        //Get entity Attributes
        $attribute_fields = $this->_apiMethodFieldModel->getEntityAttributeList($this->_entity_controller->entity_type_id);

        if (!empty($attribute_fields)) {
            foreach ($attribute_fields as $listing_field) {
                $data[] = $listing_field->attribute_code;
            }
        }
       // echo "<pre>"; print_r($data); exit;

        if(count($data) > 0){
            $export_data[] = $data;
            //Export to Excel
                $file_name = $this->_entity_controller->title.'-import-template';

                Excel::create($file_name, function($excel) use($data) {
                    $excel->sheet('Sheet1', function($sheet) use($data) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                    });

                })->export('xls');

        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function import(Request $request)
    {
        $this->_assignData["page_action"] = ucfirst(__FUNCTION__);
        $this->_assignData["route_action"] = strtolower(__FUNCTION__);

       // echo "<pre>";  print_r($request->all()); exit;
        $this->_assignData['entity_data'] = (object)$this->_entity_controller['attributes'];

        if (isset($request->do_post)) {
            if (isset($request->download_template)) {
               // echo "<pre>"; print_r($request->all()); exit;
                $this->importTemplate($request->all());
            }
            else{
                $this->_import($request->all());
            }

        }

        $view_file = $this->_assignData["dir"] . __FUNCTION__;
        $view = View::make($view_file, $this->_assignData);
        return $view;
    }

    /**
     * @param $request
     */
    public function _import($request)
    {
        $request = is_object($request) ? $request : (object)$request;
        if(isset($request->import_file) && !empty($request->import_file)){

            //Get entity Attributes
            $attribute_fields = $this->_apiMethodFieldModel->getEntityAttributeList($this->_entity_controller->entity_type_id);

            //Get File from attachment
            $import_export_lib = new DataImportExport();
            $return = $import_export_lib->importEntityData($request,$this->_entity_controller,$attribute_fields);
         // echo "<pre>"; print_r( $return); exit;

            if($return->error == 0){
              //  \Session::put(ADMIN_SESS_KEY . '_POST_DATA', $request);
                \Session::put(ADMIN_SESS_KEY . 'success_msg', $return->message);
                $redirect = \URL::to($this->_panelPath . $this->_object_identifier . "/" . $this->_entity_controller->identifier.'/import');
                Redirect::to($redirect);
            }
            else{
               // echo "<pre>"; print_r($request); exit;
                \Session::put(ADMIN_SESS_KEY . '_POST_DATA', $request);
                \Session::put(ADMIN_SESS_KEY . 'error_msg', $return->message);
                \Session::save();
               $redirect = \URL::to($this->_panelPath . $this->_object_identifier . "/" . $this->_entity_controller->identifier.'/import');
                Redirect::to($redirect)->with('import_file', $request->import_file);
            }

        }
    }

    /**
     * Check action permission for advance template
     * where add action is calling inside listing action
     * @param $page
     * @param $action
     * @return mixed
     */
    private function _checkActionPermission($page,$action,$request_params = false)
    {
      /*  if($request_params && $page == 'item'){
            $request_params = is_array($request_params) ? (object)$request_params : $request_params;
            if(isset($request_params->item_type))
                $page  = ProductHelper::getRequestedIdentifierByType($request_params->item_type);
        }*/

        $module_lib = new Module();
        return $module_lib->checkActionPermission($page,$action);
    }


    /**
     * Define custom redirection
     * when add action is completed
     * @param $request
     */
    private function _addCustomRedirect($request)
    {
        /* if customer entity type is item*/
      /*  if($this->_entity_controller->identifier == "item") {

            $requested_identifier = ProductHelper::getRequestedIdentifierByType($request->item_type);
            //$this->_assignData['custom_redirect']  = \URL::to($this->_panelPath . $this->_object_identifier . "/" . $this->_entity_controller->identifier."?item_type=".$request->item_type);
            $this->_assignData['custom_redirect']  = \URL::to($this->_panelPath . $this->_object_identifier . "/" .$requested_identifier);

        }*/
    }

    /**
     * Validate Add entity
     * @param $request
     * @return bool|mixed|View
     */
    private function _addEntityValidate($request)
    {
        $entity_helper = new EntityHelper();
        $return = $entity_helper->validationEntity($request->all());
        // print_r($return); exit;
        if($return['error'] == 0){
            return $this->_add($request);
        }
        else{
            return $return;
        }

        return true;

    }


    /**
     * check update option is ON/OFf
     * @param $paginated_id
     * @return bool
     */
    private function _checkUpdateOption($paginated_id)
    {
        if(isset( $this->_entity_controller->identifier)){

            //if entity type is business user and also business user is logged with session then he cannot update his record
            if( $this->_entity_controller->identifier == 'business_user'){
                $session_department = Session::get($this->_entity_session_identifier . "department");
                if($session_department == 'business_user'){

                    $auth = Session::get($this->_entity_session_identifier . "auth");
                    if(isset($auth->auth->email) && (isset($paginated_id->auth->email) && $auth->auth->email == $paginated_id->auth->email)){
                        return false;
                    }
                }

                return true;
            }
            elseif( $this->_entity_controller->identifier == 'order'){

                $pickup_date = $paginated_id->attributes->pickup_date;

                if(in_array($paginated_id->attributes->order_status->detail->attributes->keyword,array('pending','confirmed','assigned'))){
                    return true;
                }
                else if($paginated_id->attributes->order_status->detail->attributes->keyword == 'accepted' && (strtotime($pickup_date) < strtotime(date('Y-m-d')))){
                    return true;
                }
                else{
                    return false;
                }
            }

            else{
                return true;
            }
        }

    }

    /**
     * @param $paginated_id
     * @return bool
     */
    private function _checkDeleteOption($paginated_id)
    {
        //if entity type is business user and also business user is logged with session then he cannot delete his record
        if( $this->_entity_controller->identifier == 'business_user'){
            $session_department = Session::get($this->_entity_session_identifier . "department");
                if($session_department == 'business_user'){

                    $auth = Session::get($this->_entity_session_identifier . "auth");
                    if(isset($auth->auth->email) && (isset($paginated_id->auth->email) && $auth->auth->email == $paginated_id->auth->email)){
                        return false;
                    }
                }
            }
        return true;
    }


    /**
     * Customize item list/add/view urls
     * @param Request $request
     * @param $identifier
     * @return bool|string
     */
    private function _customizeRequestedIdentifier(Request $request,$identifier)
    {
        if($identifier == 'other_item'){
           if(!isset($request->is_other)) $request->request->add(['is_other' => 1]);
            $this->_assignData['s_title'] = 'Other Item';
            return 'item';
        }
          elseif($identifier == 'item'){
              if(!isset($request->is_other)) $request->request->add(['is_other' => 0]);
              return 'item';
          }
        else
            return false;

    }

    /**
     * This function is used for view order history
     * @param {string} $panel
     * @param {int} $id
     */
    public function orderHistory($panel,$id)
    {
        //get order history data
        $entity_lib = new Entity();
        $params = array(
            'entity_type_id' => 'order_history',
            'order_id'  => $id,
            'order_by' => 'created_at',
            'sorting' => 'ASC',
            'mobile_json' => 1,
            'limit' => -1
        );

        $order_history = $entity_lib->apiList($params);
        $order_history = json_decode(json_encode($order_history));
        $view_file = $this->_assignData["dir"] . snake_case(__FUNCTION__);
        /*Check if any template exist with entity type then render file from template dir*/
        if(!empty($this->_entity_controller->template)){
            if(View::exists($this->_assignData["dir"] .$this->_entity_controller->template.'/'. snake_case(__FUNCTION__))){
                $view_file = $this->_assignData["dir"] .$this->_entity_controller->template.'/'. snake_case(__FUNCTION__);
            }
        }


        //Get Order
        $post_arr = [];
        $post_arr['entity_type_id'] = 15;
        $post_arr['entity_id'] = $id;
        $order_raw = $entity_lib->doGet($post_arr);
        $order = json_decode(json_encode($order_raw));


        $this->_assignData['order_id'] = $id;
        $this->_assignData['order'] = $order;
        $this->_assignData['orderHistory'] = $order_history;
        $view =  View::make($view_file, $this->_assignData);
        return $view;
    }

    /**
     * This function is used for update other item flag
     * @param {string} $panel,
     * @param {int} $id
     */
    public function updateOtherItem(Request $request)
    {
        $entity_lib = new Entity();
        $post_arr = [];
        $post_arr['entity_type_id'] = 14;
        $post_arr['entity_id'] = $request->input('item_id');
        $post_arr['is_other'] = 0;
        $entity_lib->doUpdate($post_arr);
        exit;
    }

    /**
     * This funciton is used for get entity data
     * @param {object} $request
     */
    public function getEntityData(Request $request)
    {
        $entity_lib = new Entity();
        if(!empty($request->input('city_ids'))) {
            $city_ids     = implode(',',$request->input('city_ids'));
            $getCustomers = SYSEntityAttribute::getCitiesCustoemr($city_ids);
            $data = [];
            if(count($getCustomers)){
                foreach($getCustomers as $customer){
                    $customer_ids[] = $customer->customer_id;
                }
                $customer_ids = array_unique($customer_ids);
                $params = array(
                    'entity_type_id' => 'customer',
                    'entity_id' => implode(',',$customer_ids),
                    'order_by' => 'created_at',
                    'sorting' => 'ASC',
                    'mobile_json' => 1,
                    'limit' => -1,
                );
                $getData = $entity_lib->apiList($params);
                return json_encode($getData['data']['customer']);
            }
            return json_encode($data);
        }else{
            $identifier = $request->input('notify_to');
            if($identifier == 'driver'){
                $params = array(
                    'entity_type_id' => 'driver',
                    'order_by' => 'created_at',
                    'sorting' => 'ASC',
                    'mobile_json' => 1,
                    'limit' => -1,
                );
            }else{
                $params = array(
                    'entity_type_id' => 'customer',
                    'order_by' => 'created_at',
                    'sorting' => 'ASC',
                    'mobile_json' => 1,
                    'limit' => -1,
                );
            }
            $getData = $entity_lib->apiList($params);
            return json_encode($getData['data'][$identifier]);
        }
    }

    /**
     * get truck class
     * @param {object} $request
     * @return {json} Response
     */
    public function getTruckClass(Request $request)
    {
        $entity_lib = new Entity();
        $params = array(
            'entity_type_id' => 'truck_class',
            'order_by'       => 'created_at',
            'sorting'        => 'ASC',
            'entity_id'      =>  $request->input('truck_class_id')
        );
        $getData = $entity_lib->apiList($params);
        return json_encode($getData['data']['entity_listing']);
    }

    /**
     * This function is used for Rating Listing management modile
     * @return view
     */
    public function ratingListing(Request $request)
    {
        //export functionality
        if($request->input('do_export')){
            return $this->customEntityImport($request->all(),'rating');
        }
        $this->_assignData["page_action"]  = 'Rating Management';
        $this->_assignData["route_action"] = 'rating';
        $view_file = $this->_assignData["dir"] . 'rating/listing';
        $this->_assignData['getCustomers'] = self::getEnityData('customer');
        $this->_assignData['getDrivers'] = self::getEnityData('driver');
        $view =  View::make($view_file, $this->_assignData);
        return $view;
    }
    
    /**
     * This function is used for load rating mobile data via ajax request 
     */
    public function ratingAjaxListing(Request $request)
    {
        // datagrid params : sorting/order
        $search_value = isset($_REQUEST['search']['value']) ? $_REQUEST['search']['value'] : '';
        $dg_order = isset($_REQUEST['order'][0]['column']) ? $_REQUEST['order'][0]['column'] : '';
        $dg_sort = isset($_REQUEST['order'][0]['dir']) ? $_REQUEST['order'][0]['dir'] : '';
        $dg_columns = isset($_REQUEST['columns']) ? $_REQUEST['columns'] : '';
        // default ordering
        if ($dg_order == "" && $dg_sort == "") {
            $dg_order = "a.created_at";
            $dg_sort = "ASC";
        } else {
            // fix invalid column
            $dg_order = $dg_order == 0 ? 1 : $dg_order;
            // get column field name
            $dg_order = $dg_columns[$dg_order]["data"];
            // fix joined column name
            $dg_order = str_replace("|",".",$dg_order);
        }
        $dg_limit = intval($_REQUEST['length']);
        $dg_start = intval($_REQUEST['start']);
        
        $getData = FlatTable::getOrderRating($dg_limit,$dg_start,$request->all());

        $getRecords    = $getData['data'];
        $total_records = $getData['total_records'];


        $records["data"] = [];
        // if records
        if (count($getRecords)) {
            // collect records
            $i=0;
            foreach ($getRecords as $order_id => $record) {

                $getCustomer = $getDriver = false;
                if(isset($record['driver_review']) && !empty($record['driver_review'])){
                    $review_id  = $record['driver_review']->package_rate_id;
                    $getDriver  = self::getEnityData('driver',$record['driver_review']->actor_entity_id);
                    $created_at =  date(DATE_FORMAT_ADMIN, strtotime($record['driver_review']->created_at));
                }
                if(isset($record['customer_review']) && !empty($record['customer_review'])){
                    $review_id  = $record['customer_review']->package_rate_id;
                    $getCustomer = self::getEnityData('customer',$record['customer_review']->actor_entity_id);
                   // echo "<pre>"; print_r($getCustomer);
                    $created_at = date(DATE_FORMAT_ADMIN, strtotime($record['customer_review']->created_at));
                }

                $options  = '<div class="btn-group">';
                $options .= '<a class="btn btn-xs btn-default mr5" href="'. \URL::to($this->_panelPath . $this->_assignData['module'] . '/rating/rating-detail/' . $order_id) .'" data-toggle="tooltip" >
<i class="fa fa-eye"></i></a>';
                $options .= '</div>';

                //customer rating html
                $custom_rating_html  = '<div class="star-rating">';
                if(isset($record['customer_review']) && !empty($record['customer_review']))
                {
                    for($i2=1; $i2 <= 5; $i2++){
                        if($i2 > $record['customer_review']->rating){
                            $customer_star_class = 'fa fa-star-o';
                        }else{
                            $customer_star_class = 'fa fa-star';
                        }
                        $custom_rating_html .= '<span class="'. $customer_star_class .'" data-rating="'.$i2.'"></span>';
                    }
                }
                $custom_rating_html .= '</div>';

                //driver rating html
                $driver_rating_html  = '<div class="star-rating">';
                if(isset($record['driver_review']) && !empty($record['driver_review'])) {
                    for ($i3 = 1; $i3 <= 5; $i3++) {
                        if($i3 > $record['driver_review']->rating){
                            $driver_star_class = 'fa fa-star-o';
                        }else{
                            $driver_star_class = 'fa fa-star';
                        }
                        $driver_rating_html .= '<span class="'.$driver_star_class.'" data-rating="' . $i3 . '"></span>';
                    }
                }
                $driver_rating_html .= '</div>';

                // collect data
                if($getCustomer || $getDriver)
                $records["data"][] = array(
                    "ids"                => ($i + 1),
                    "order_id"           => $order_id,
                    "customer"           => ($getCustomer && isset($getCustomer)) ? $getCustomer->attributes->full_name : '',
                    "customer_rating"    => ($getCustomer && isset($getCustomer)) ? $custom_rating_html : '',
                    "customer_comment"   => ($getCustomer && isset($getCustomer)) ? str_limit($record['customer_review']->review,15) : '',
                    "driver"             => ($getDriver && isset($getDriver))   ? $getDriver->attributes->full_name : '',
                    "driver_rating"      =>  ($getDriver && isset($getDriver))   ? $driver_rating_html : '',
                    "driver_comment"     =>  ($getDriver && isset($getDriver)) ? str_limit($record['driver_review']->review,15) : '',
                    "created_at"         => isset($created_at) ?  $created_at : '',
                    "option"             => $options
                );
                // increament
                $i++;
            }
        }

        $records["draw"] = intval($_REQUEST['draw']);
        $records["recordsTotal"] = $total_records; //total records
        $records["recordsFiltered"] = $total_records; //total records

        echo json_encode($records);
    }

    public function customEntityImport($params,$entity_type)
    {
        $getData = FlatTable::getOrderRating('','',$params);

        $excel_data = [];
        //set column
        $column = [
            "order_id", "customer", "customer_rating", "customer_comment", "driver",
            "driver_rating", "driver_comment", "created_at"
        ];
        array_push($excel_data,$column);
        //set column data
        if(count($getData['data'])){
            foreach($getData['data'] as $order_id => $record){

                $getCustomer = $getDriver = false;
                if(isset($record['driver_review']) && isset($record['driver_review']->package_rate_id)){
                    $review_id  = $record['driver_review']->package_rate_id;
                    $getDriver  = self::getEnityData('driver',$record['driver_review']->actor_entity_id);
                    $created_at = $record['driver_review']->created_at;

                }
                if(isset($record['customer_review']) && isset($record['customer_review']->package_rate_id)){
                    $review_id  = $record['customer_review']->package_rate_id;
                    $getCustomer = self::getEnityData('customer',$record['customer_review']->actor_entity_id);
                    $created_at = $record['customer_review']->created_at;
                }
                $column_data = array(
                    $order_id,
                    ($getCustomer && isset($getCustomer)) ? $getCustomer->attributes->full_name : '',
                    ($getCustomer && isset($getCustomer)) ? $record['customer_review']->rating : '',
                    ($getCustomer && isset($getCustomer)) ? $record['customer_review']->review : '',
                    ($getDriver && isset($getDriver))   ? $getDriver->attributes->full_name : '',
                    ($getDriver && isset($getDriver))  ? $record['driver_review']->rating : '',
                    ($getDriver && isset($getDriver))  ? $record['driver_review']->review : '',
                    $created_at
                );
                array_push($excel_data,$column_data);
            }
        }
        $file_name = $entity_type.'-list-'.time();
        Excel::create($file_name, function($excel) use($excel_data) {
            $excel->sheet('Sheet1', function($sheet) use($excel_data) {
                //  $sheet->fromArray($data);
                $sheet->fromArray($excel_data, null, 'A1', false, false);
            });
        })->export('xls');
    }

    public function getEnityData($entity_type,$entity_id = '')
    {
        $entity_lib = new Entity();
        $params = array(
            'entity_type_id' => $entity_type,
        );
        if(!empty($entity_id)){
            $params['entity_id'] = $entity_id;
        }
        $getData = $entity_lib->apiList($params);

       // echo "<pre>"; print_r($getData);
        if(!empty($entity_id) && isset($getData['data']['entity_listing'][0])){
            return $getData['data']['entity_listing'][0];
        } elseif(isset($getData['data']['entity_listing'][0])){
            return $getData['data']['entity_listing'];
        }else{
                return false;
            }

    }

    /**
     * This function is used for show rating detail
     * @param {object} $request
     * @param {int} $order_id
     */
    public function ratingDetail(Request $request,$panel,$order_id)
    {
        $this->_assignData["page_action"]  = 'Order Rating Detail';
        $this->_assignData["route_action"] = 'rating-detail';
        //get order review detil
        $getOrderReview = FlatTable::getOrderReviewDetail($order_id);

       // echo "<pre>"; print_r($getOrderReview); exit;
        $this->_assignData['orderReviewDetail'] = $getOrderReview;
        //view template path
        $view_file = $this->_assignData["dir"] . 'rating/rating_detail';
        $view =  View::make($view_file, $this->_assignData);
        return $view;
    }

    /**
     * Report management
     * @param {object} $request
     */
    public function report(Request $request,$panel)
    {
        //export functionality
        if($request->input('do_export')){
            return $this->reportListing($request,$panel,true);
        }
        $this->_assignData["page_action"]  = 'Report Management';
        $this->_assignData["route_action"] = 'report';
        $view_file = $this->_assignData["dir"] . 'report/listing';
        //$this->_assignData['getCustomers'] = self::getEnityData('customer');
        $this->_assignData['getDrivers']   = self::getEnityData('driver');
        $this->_assignData['orderStatuses']= self::getEnityData('order_statuses');
        //$this->_assignData['trucks']       = self::getEnityData('truck');
        $this->_assignData['cities']       = self::getEnityData('city');
        $view =  View::make($view_file, $this->_assignData);
        return $view;
    }

    /**
     * reportListing
     * @param {object} $request
     */
    public function reportListing(Request $request,$panel,$export = false)
    {
        $total_records   = 0;
        $records["data"] = [];
        if($request->input('do_search'))
        {
            $params = $request->all();
            $dg_limit    = $export === false ? intval($_REQUEST['length']) : -1;
            $dg_start    = $export === false ? intval($_REQUEST['start']) : 0;

            $query = "SELECT o.* FROM order_flat o";

            if(!empty($params['driver_rating'])){
                $query .= " LEFT JOIN ext_package_rate d ON (d.actor_entity_id = o.driver_id AND d.target_entity_id = o.entity_id)";
            }

            $query .= " WHERE o.deleted_at IS NULL";


            $entity_lib = new Entity();
            $apiParams['entity_type_id'] = 'order';
            $to_date = !empty($params['to_date']) ? $params['to_date']: date('Y-m-d');

            if(!empty($params['driver_id'])){
             //   $apiParams['driver_id'] = $params['driver_id'];
                $query .= " AND o.driver_id = ".$params['driver_id'];
            }
            if(!empty($params['city_id'])){
               // $apiParams['city_id'] = $params['city_id'];
                $query .= " AND o.city_id = ".$params['city_id'];
            }
            if(!empty($params['order_status'])){
                //$apiParams['order_status'] = $params['order_status'];
                $query .= " AND o.order_status = ".$params['order_status'];
            }

            if(!empty($params['driver_rating'])){
                //$apiParams['star_rating'] = $params['driver_rating'];
                $query .= " AND d.rating = ".$params['driver_rating'];
            }

            $apiParams['where_condition'] = '';

            if(!empty($params['from_date']) && !empty($to_date)){
                $from_date = $params['from_date'];
               // $apiParams['where_condition'] .= " AND (pickup_date BETWEEN '$from_date' AND '$to_date' )";
                $query .= " AND (o.pickup_date BETWEEN '$from_date' AND '$to_date' )";
            }


            if(!empty($params['order_amount'])){
                $grand_total = $params['order_amount'];
               // $apiParams['where_condition'] .= " AND (o.grand_total <=  $grand_total)";
                $query .= " AND (o.grand_total <=  $grand_total)";
            }

             $query .= " ORDER BY o.entity_id DESC";

           // $query .= " LIMIT $dg_start, $dg_limit";

        /*  echo $query; exit;
          echo '<br>';*/

            $apiParams['limit']  = $dg_limit;
            $apiParams['offset'] = $dg_start;
            $apiParams['query'] = $query;

            $getData = $entity_lib->apiList($apiParams);

            $getRecords    = $getData['data']['entity_listing'];
            $total_records = $getData['data']['page']['total_records'];


            $records["data"] = [];
            // if records
            if (count($getRecords) !=0 && !empty($getRecords)) {
                // collect records
                $i=0;
                foreach ($getRecords as $record) {

                    //get dropoff location
                    $getDropOffLocation = \DB::table('order_dropoff_flat')
                                            ->select('city_flat.*')
                                            ->join('city_flat','city_flat.entity_id','=','order_dropoff_flat.city')
                                            ->where('order_id',$record->entity_id)
                                            ->first();
                    //get pickup location
                    $getPickupLocation = \DB::table('order_pickup_flat')
                                            ->select('city_flat.*')
                                            ->where('order_id',$record->entity_id)
                                            ->join('city_flat','city_flat.entity_id','=','order_pickup_flat.city')
                                            ->first();
                    //get driver rating
                    //get driver rating
                    if(isset($record->attributes->driver_id->detail->entity_id)){
                        $getDriverRating   = \DB::table('ext_package_rate')
                            ->where('actor_entity_id',$record->attributes->driver_id->detail->entity_id)
                            ->where('target_entity_id',$record->entity_id)
                            ->first();
                    }else{
                        $getDriverRating = [];
                    }
                    //get customer rating
                    //get customer rating
                    if(isset($record->attributes->customer_id->detail->entity_id)){
                        $getCustomerRating   = \DB::table('ext_package_rate')
                            ->where('actor_entity_id',$record->attributes->customer_id->detail->entity_id)
                            ->where('target_entity_id',$record->entity_id)
                            ->first();
                    }else{
                        $getCustomerRating = [];
                    }

                    //driver rating html
                    $driver_rating_html   = '';
                    $customer_rating_html = '';
                    if($export === false)
                    {
                        $driver_rating_html  = '<div class="star-rating">';
                        if(count($getDriverRating)) {
                            for ($i3 = 1; $i3 <= 5; $i3++) {
                                if($i3 > $getDriverRating->rating){
                                    $driver_star_class = 'fa fa-star-o';
                                }else{
                                    $driver_star_class = 'fa fa-star';
                                }
                                $driver_rating_html .= '<span class="'.$driver_star_class.'" data-rating="' . $i3 . '"></span>';
                            }
                        }
                        $driver_rating_html .= '</div>';

                        //customer rating html
                        $customer_rating_html  = '<div class="star-rating">';
                        if(count($getCustomerRating)) {
                            for ($i3 = 1; $i3 <= 5; $i3++) {
                                if($i3 > $getCustomerRating->rating){
                                    $customer_star_class = 'fa fa-star-o';
                                }else{
                                    $customer_star_class = 'fa fa-star';
                                }
                                $customer_rating_html .= '<span class="'.$customer_star_class.'" data-rating="' . $i3 . '"></span>';
                            }
                        }
                        $customer_rating_html .= '</div>';
                    }else{
                        $customer_rating_html = isset($getCustomerRating->rating) ? $getDriverRating->rating : '';
                        $driver_rating_html = isset($getDriverRating->rating) ? $getDriverRating->rating : '';
                    }

                    $customer_email = isset($record->attributes->customer_id->detail->auth->email) ? $record->attributes->customer_id->detail->auth->email : '';
                    $customer_phone = isset($record->attributes->customer_id->detail->auth->mobile_no) ? $record->attributes->customer_id->detail->auth->mobile_no : '';


                    // collect data
                    $records["data"][] = array(
                        'ids'             => $record->entity_id,
                        'driver_name'     => isset($record->attributes->driver_id->value) ? $record->attributes->driver_id->value : '',
                        'driver_rating'   => $driver_rating_html,
                        'pickup_city'     => count($getPickupLocation) ? $getPickupLocation->title : '',
                        'dropoff_city'    => count($getDropOffLocation) ? $getDropOffLocation->title : '',
                        'customer_name'   => isset($record->attributes->customer_id->value) ? $record->attributes->customer_id->value : '',
                        'customer_rating' => $customer_rating_html,
                        'customer_email'  => $customer_email,
                        'customer_phone'  => $customer_phone,
                        'order_amount'    => $record->attributes->grand_total,
                        'date'            => $record->attributes->pickup_date,
                        'order_status'    => $record->attributes->order_status->detail->attributes->display_title
                    );
                    // increament
                    $i++;
                }
                if($request->input('do_export'))
                {
                    return self::reportExport($records["data"]);
                }
            }
        }

        //echo "<pre>"; print_r($records); exit;

        $records["draw"] = intval($_REQUEST['draw']);
        $records["recordsTotal"] = $total_records; //total records
        $records["recordsFiltered"] = $total_records; //total records
        echo json_encode($records);
    }

    /**
     * reportListing
     */
    public function reportExport($records)
    {
        if(!empty($records))
        {
            $keys = $excel_data = [];
            $i = 0;
            foreach($records as $record)
            {
               $data =  [];
               foreach($record as $key => $value){
                   if($i == 0){
                       $keys[] = $key;
                   }

                   $data[] = $value;
               }

                if($i == 0){
                    $excel_data[] = $keys;
                }

                $excel_data[] = $data;
               $i++;
            }

           // $excel_data[] = $keys;
            //echo "<pre>"; print_r($excel_data); exit;

            $file_name = 'report-list-'.time();
            Excel::create($file_name, function($excel) use($excel_data) {
                $excel->sheet('Sheet1', function($sheet) use($excel_data) {
                    //  $sheet->fromArray($data);
                    $sheet->fromArray($excel_data, null, 'A1', false, false);
                });
            })->export('xls');
        }
    }

    public function calendar(Request $request)
    {
        $flat_model = new SYSTableFlat('driver');
        $this->_assignData['drivers'] = $flat_model->getAll(false,' ORDER BY first_name');

        $start_date = Carbon::now()->startOfWeek()->format('Y-m-d');
        $dates[] = $start_date = Carbon::createFromFormat('Y-m-d', $start_date)->subDays(1)->format('Y-m-d');
        for($i = 1; $i <=6; $i++){
            $dates[] = Carbon::createFromFormat('Y-m-d', $start_date)->addDays($i)->format('Y-m-d');
        }

        $end_date = $dates[6];
        $order_flat = new OrderFlat();
        $this->_assignData['orders'] = $order_flat->getDriverOrderSlots(false,$start_date,$end_date);

        $this->_assignData['dates'] = $dates;
        $view_file = $this->_assignData["dir"] . 'order/calendar-full';
       // $view_file = $this->_assignData["dir"] . 'order/calendar';
        $view =  View::make($view_file, $this->_assignData);
        return $view;
    }
}
    