<?php
namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Api\System\EntityController;
use App\Http\Controllers\Controller;
use App\Http\Hooks\EntityNotification;
use App\Http\Models\SYSAttributeOption;
use Auth;
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
use App\Libraries\Fields;

class CategoryController extends EntityController
{

    private $_object_identifier = "category";
    private $_object_identifier_module = "modules";
  //  private $_object_identifier_entity = "entity_id";
    private $_attribute_pk = "category_id";
    private $_listing_fields = array();
    private $_check_box_checked = "checked='checked'";
    private $_check_box_Unchecked = "";
    private $_child_arrow = "--->";
    private $_model_path = "\App\Http\Models\\";
    private $_model = "";
    private $_entity_session_identifier;

    /**
     * CategoryController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        //$this->middleware('auth');
        // construct parent
		
        parent::__construct($request);

        $this->_entity_session_identifier = config("panel.SESS_KEY");
        $_data = $this->load_params('system/' . $this->_object_identifier . '/listing', 'GET');
        $this->_listing_fields = $_data['records'];
        // role module listing
        $_datas = $this->load_params('system/' . $this->_object_identifier . '/' . $this->_object_identifier_module, 'GET');
        $this->_listing_fields_modules = $_datas['records'];


        $this->_model = $this->_model_path . "SYSCategory";
        $this->_model = new $this->_model;

        // define default dir
        $this->_assignData["dir"] = config("panel.DIR") . $this->_object_identifier . '/';
        // assign meta from parent constructor
        $this->_assignData["_meta"] = $this->__meta;
        // assign request
        $this->_assignData["request"] = $request;
        //module
        $this->_assignData['module'] = $this->_object_identifier;
        //model path		
    }

    /**
     * Return data to admin listing page
     *
     * @return type Array()
     */
    public function index(Request $request)
    {
        $this->_assignData['module'] = $this->_object_identifier;
        $this->_assignData['search'] = $this->_listing_fields;
        $this->_assignData['columns']['ids'] = '<div class="checkbox-t"><input type="checkbox" id="check_all" name="check_all" /><label for="check_all"></label></div>';
        $this->_assignData['columns']['image'] = 'Image';


        foreach ($this->_listing_fields as $listing_field) {
            $this->_assignData['columns'][$listing_field->name] = $listing_field->description;
            if($listing_field->name != 'created_at')
            $this->_assignData['listing_columns'][] = $listing_field;
        }
        $this->_assignData['columns']['options'] = 'Options';
        $this->_assignData["route_action"] = strtolower(__FUNCTION__);
        $this->add($request);


        if ($request->do_post == 1) {
            return $this->_add($request);
        }

        $view = View::make($this->_assignData["dir"] . __FUNCTION__, $this->_assignData);
        return $view;
    }

    /**
     * Ajax Listing
     *
     * @return json
     */


    public function ajaxListing(Request $request)
    {
        // datagrid params : sorting/order
        $search_value = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : '';
        $dg_order = isset($_REQUEST['order'][0]['column']) ? $_REQUEST['order'][0]['column'] : '';
        $dg_sort = isset($_REQUEST['order'][0]['dir']) ? $_REQUEST['order'][0]['dir'] : '';
        $dg_columns = isset($_REQUEST['columns']) ? $_REQUEST['columns'] : '';

        $search_columns = isset($_REQUEST['search_columns']) ? $_REQUEST['search_columns'] : array();
     /*   if (trim($search_value) != "") {
            if (isset($_REQUEST['search_columns']) && is_array($_REQUEST['search_columns'])) {
                foreach ($_REQUEST['search_columns'] as $columns) {
                    $search_columns[$columns] = $search_value;

                    //if search value is category title then first get category parent id to search in caetgory table
                    if($columns == "parent_id"){
                        $category_data = $this->_model->getCategoryByTitle(trim($search_value));
                        if($category_data){
                            $search_columns[$columns] = $category_data->category_id;
                        }

                    }
                }
            }
        }*/

        // default ordering
        if ($dg_order == "" && $dg_sort == "") {
            $dg_order = "created_at";
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
            $this->_selectActions($request);
        }
        $dg_limit = intval($_REQUEST['length']);
        $dg_limit = $dg_limit < 0 ? $total_records : $dg_limit;
        $dg_start = intval($_REQUEST['start']);
        $dg_draw = intval($_REQUEST['draw']);
        $dg_end = $dg_start + $dg_limit;


        $search_columns['limit'] = $dg_limit;
        $search_columns['offset'] = $dg_start;
        $search_columns['order_by'] = $dg_order;
        $search_columns['sorting'] = $dg_sort;


        $data = $this->__internalCall($request,\URL::to(DIR_API) . '/system/' . $this->_object_identifier . '/listing', 'GET', $search_columns,false);

        $this->_assignData['records'] = $data->data->{$this->_object_identifier . '_listing'};

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
            foreach ($paginated_ids as $paginated_id) {

               // print_r($paginated_id); continue;
                //Get entity type title for display user type name
                if(isset($paginated_id->parent_id) && !empty($paginated_id->parent_id)){
                    $paginated_id->parent_id = $this->_model->geCategoryTitleById($paginated_id->parent_id);
                }
                else{
                    $paginated_id->parent_id = "N/A";
                }

               /* Get Status option name from attribute option*/
                if(isset($paginated_id->status)){
                    $attr_option_model = $this->_model_path . "SYSAttributeOption";
                    $attr_option_model = new $attr_option_model;
                   $status_data =  $attr_option_model->getAttributeOptionByAttribute('status',$paginated_id->status);
                    if($status_data){
                        $paginated_id->status = $status_data->option;
                    }

                }

                //$id_record = $this->_model->get($paginated_id->{$this->_pk});
                // status html
                $status = "";
                // options html
                $options = '<div class="btn-group">';
                // selectbox html
                $checkbox = '<div class="checkbox-t">';
                // manage options
                // - update
                $options .= '<a class="btn btn-sm btn-default mr5" type="button" href="' . \URL::to($this->_panelPath . $this->_assignData['module'] . '/update/' . $paginated_id->{$this->_object_identifier . '_id'}) . '" data-toggle="tooltip" title="Update" data-original-title="Update"><i class="fa fa-pencil"></i></a>';
                $options .= '<a data-module_url="delete" class="btn btn-sm btn-default grid_action_del delete_action" type="button" data-toggle="tooltip" title="" data-original-title="Delete"><i class="fa fa-times"></i></a>';
                $options .= '<a class="btn btn-sm btn-default mr5" type="button" href="' . \URL::to($this->_panelPath . $this->_assignData['module'] . '/view/' . $paginated_id->{$this->_object_identifier . '_id'}) . '" data-toggle="tooltip" title="Update" data-original-title="View"><i class="fa fa-eye"></i></a>';

                $checkbox .= '<input type="checkbox" id="check_id_' . $paginated_id->{$this->_object_identifier . '_id'} . '" name="check_ids[]" value="' . $paginated_id->{$this->_object_identifier . '_id'} . '" />';
                $checkbox .= '<label class="deleted_btn" for="check_id_' . $paginated_id->{$this->_object_identifier . '_id'} . '"></label>';
                $options .= '</div>';
                $checkbox .= '</div>';

                $list["ids"] = $checkbox;

                // collect data
                foreach ($this->_listing_fields as $listing_field) {
                    switch ($listing_field->name) {
                        case "created_at":
                        case "updated_at":
                            $list[$listing_field->name] = date(DATE_FORMAT_ADMIN, strtotime($paginated_id->{$listing_field->name}));
                            break;
                        default:
                            $list[$listing_field->name] = empty($paginated_id->{$listing_field->name}) ? '' : $paginated_id->{$listing_field->name};
                            break;
                    }
                }


                //Get image path for entity
                $list["image"] = '';
               // if($paginated_id->is_parent == 1){
                    $gallery = isset($paginated_id->image) ? $paginated_id->image : false;
                    $list["image"] = Fields::getCategoryImagePath($gallery,true);
                //}


                $list["options"] = $options;
                $records["data"][] = $list;
                // increament
                $i++;
            }
        }

        $records["draw"] = $dg_draw;
        $records["recordsTotal"] = $total_records;
        $records["recordsFiltered"] = $total_records;

        echo json_encode($records);
    }


    private function getCheckBox($identifier , $module_id ,$checked=false)
    {
		if($checked) $checked='checked="checked"'; else $checked='';
        $checkbox_per = '<div class="checkbox-t">';
        $checkbox_per .= '<input type="checkbox" ' . $checked . '  id="'.$identifier.$module_id.'" name="'.$identifier.'[]" value="'.$module_id.'" />';
        $checkbox_per .= '<label class="deleted_btn"  for="' . $identifier . $module_id. '"></label>';
        $checkbox_per .= '</div>';
        return $checkbox_per;
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
        $data = $this->load_params('system/' . $this->_object_identifier, 'post');
       // $this->_assignData['records'] = $data['records'];

        $this->_assignData["page_action"] = ucfirst(__FUNCTION__);


        $api_hidden_fields = array();
        $api_fields = array();
        if(count( $data['records'])>0){

            foreach($data['records'] as $record){

                if($record->element_type == "hidden"){
                    $record->is_entity_column = 0;
                    $api_hidden_fields[] = $record;
                }
                else if($record->element_type == "query" || $record->data_type == "callback") {
                    continue;
                }
                else{
                    $record->is_entity_column = 0;
                    $api_fields[] =  $record;
                }
            }
        }

        $this->_assignData['records'] = $api_fields;
        $this->_assignData['hidden_records'] = $api_hidden_fields;


        // validate post form
        /*if ($request->do_post == 1) {
            return $this->_add($request);
        }*/

        //$view = View::make($this->_assignData["dir"] . __FUNCTION__, $this->_assignData);
        //return $view;
    }

    /**
     * Add (private)
     *
     * @return view
     */
    private function _add(Request $request)
    {

        $return = $this->_checkActionPermission($this->_object_identifier,'add');
        if(isset($return['error']) && $return['error'] == 1){
            return $return;
        }

        $_POST["created_at"] = date("Y-m-d H:i:s");
        $ret = $this->__internalCall($request,\URL::to(DIR_API) . '/system/' . $this->_object_identifier, 'POST', $_POST,false);
        if ($ret->error == '1') {
            $assignData['error'] = 1;
            $assignData['message'] = $ret->message;
            return $assignData;
        } else {
            $assignData['error'] = 0;
            $assignData['message'] = $ret->message;
            $assignData['redirect'] = \URL::to($this->_panelPath . $this->_object_identifier);
            return $assignData;
        }

    }

    /**
     * Update
     *
     * @return view
     */
    public function update(Request $request, $department, $id)
    {
        // page action
        $this->_assignData["page_action"] = ucfirst(__FUNCTION__);
        $this->_assignData["route_action"] = strtolower(__FUNCTION__);

        // validate post form
        if (isset($request->do_post)) {
            return $this->_update($request);
        }

        $getData[$this->_object_identifier . '_id'] = $id;

        $update = $this->__internalCall($request,\URL::to(DIR_API) . '/system/' . $this->_object_identifier. '/listing', 'GET', $getData,false);
        $this->_assignData["update"] = $update->data->category_listing[0];

        //echo "<pre>"; print_r($this->_assignData["update"]); exit;

        $this->_assignData["dir"] = config("panel.DIR") . $this->_object_identifier . '/';
        $data = $this->load_params('system/' . $this->_object_identifier . '/update', 'post');


        $this->_assignData['records'] = $data['records'];

        $api_hidden_fields = array();
        $api_fields = array();
        if(count( $data['records'])>0){

            foreach($data['records'] as $record){

                if($record->element_type == "hidden"){
                    $record->is_entity_column = 0;
                    $api_hidden_fields[] = $record;
                }
                else if($record->element_type == "query" || $record->data_type == "callback") {
                    continue;
                }
                else{

                    //check if parent category then do not show few fields
                    if(isset($this->_assignData["update"]->is_parent)){

                        if($this->_assignData["update"]->is_parent == 1){
                            if (in_array($record->name, array('parent_id','is_featured'))) {
                                continue;
                            }
                        }
                    }

                    $record->is_entity_column = 0;
                    $api_fields[] =  $record;
                }
            }
        }

        $this->_assignData['records'] = $api_fields;
        $this->_assignData['hidden_records'] = $api_hidden_fields;


        // get record
        // $this->_assignData["data"] = $this->_model->get($id);

        // redirect on invalid record
        if ($this->_assignData["update"] == FALSE) {
            // set session msg
            \Session::put(ADMIN_SESS_KEY . 'error_msg', 'Invalid record selection');
            // redirect
            $this->_assignData['redirect'] = \URL::to($this->_panelPath . $this->_assignData['module']);
            return $this->_assignData;
        }

        $view = View::make($this->_assignData["dir"] . __FUNCTION__, $this->_assignData);
        return $view;
    }

    /**
     * Update (private)
     *
     * @return view
     */
    private function _update(Request $request)
    {
        //$this->_assignData["data"] = $_POST;
        $this->_assignData["update"] = $this->__internalCall($request,\URL::to(DIR_API) . '/system/' . $this->_object_identifier . '/update', 'POST', $request->all(),false);

        if ($this->_assignData["update"]->error == "1") {
            $assignData['error'] = 1;
            $assignData['message'] = $this->_assignData["update"]->message;
            return $assignData;
        } else {
            $assignData['error'] = 0;
            $assignData['message'] = $this->_assignData["update"]->message;
            $assignData['redirect'] = \URL::to($this->_panelPath . $this->_object_identifier);
            return $assignData;
        }
        //return $this->_update($request, $this->_assignData["data"]);
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

                $postData[$this->_attribute_pk] = $checked_id;
                $data = $this->apiPostRequest(\URL::to(DIR_API) . '/system/' . $this->_object_identifier . '/delete', 'POST', $postData);

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
    private function _checkActionPermission($page,$action)
    {
        $module_lib = new Module();
        $module_lib->checkActionPermission($page,$action);
    }

    /**
     * View Page
     * @param Request $request
     * @param $department
     * @param $id
     * @return mixed
     */
    public function view(Request $request, $department, $id)
    {
        // page action
        $this->_assignData["page_action"] = ucfirst(__FUNCTION__);
        $this->_assignData["route_action"] = strtolower(__FUNCTION__);

        //check if request from notification then update is read
        $entity_notification = new \App\Libraries\EntityNotification();
        $entity_notification->updateNotificationRead($request->all());

        $getData[$this->_object_identifier . '_id'] = $id;

        $update = $this->__internalCall($request,\URL::to(DIR_API) . '/system/' . $this->_object_identifier. '/listing', 'GET', $getData,false);
        $this->_assignData["update"] = $update->data->category_listing[0];


       if($this->_assignData["update"]){

           //Get Parent title
           if($this->_assignData["update"]->parent_id > 0){
               $this->_assignData["update"]->parent_id = $this->_model->geCategoryTitleById($this->_assignData["update"]->parent_id);
           }

           //Get attribute options to display
           $attribute_option_model = new SYSAttributeOption();
           $this->_assignData["update"]->status = $attribute_option_model->getOptionByAttributeCode('status', $this->_assignData["update"]->status);
           $this->_assignData["update"]->is_featured = $attribute_option_model->getOptionByAttributeCode('is_featured', $this->_assignData["update"]->is_featured);
           $this->_assignData["update"]->featured_type = $attribute_option_model->getOptionByAttributeCode('featured_type', $this->_assignData["update"]->featured_type);

       }

        $this->_assignData["dir"] = config("panel.DIR") . $this->_object_identifier . '/';
        $data = $this->load_params('system/' . $this->_object_identifier . '/update', 'post');


        $this->_assignData['records'] = $data['records'];

        $api_hidden_fields = array();
        $api_fields = array();
        if(count( $data['records'])>0){

            foreach($data['records'] as $record){

                if($record->element_type == "hidden"){
                    $record->is_entity_column = 0;
                    $api_hidden_fields[] = $record;
                }
                else if($record->element_type == "query" || $record->data_type == "callback") {
                    continue;
                }
                else{

                    //check if parent category then do not show few fields
                    if(isset($this->_assignData["update"]->is_parent)){

                        if($this->_assignData["update"]->is_parent == 1){
                            if (in_array($record->name, array('parent_id','is_featured','featured_type'))) {
                                continue;
                            }
                        }
                    }

                    $record->is_entity_column = 0;
                    $api_fields[] =  $record;
                }
            }
        }

        $this->_assignData['records'] = $api_fields;
        $this->_assignData['hidden_records'] = $api_hidden_fields;


        // redirect on invalid record
        if ($this->_assignData["update"] == FALSE) {
            // set session msg
            \Session::put(ADMIN_SESS_KEY . 'error_msg', 'Invalid record selection');
            // redirect
            $this->_assignData['redirect'] = \URL::to($this->_panelPath . $this->_assignData['module']);
            return $this->_assignData;
        }


        $view = View::make($this->_assignData["dir"] . __FUNCTION__, $this->_assignData);
        return $view;
    }

}