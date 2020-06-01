<?php
namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Api\System\EntityController;
use App\Http\Controllers\Controller;
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

class AttributeController extends EntityController {

    private $_object_identifier = "attribute";
    private $_attribute_pk = "attribute_id";
    private $_listing_fields = array();
    public function __construct(Request $request) {
        //$this->middleware('auth');
        // construct parent

        parent::__construct($request);

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
        //model path
    }

    /**
     * Return data to admin listing page
     * 
     * @return type Array()
     */
    public function index(Request $request) {

        $this->_assignData['module'] = $this->_object_identifier;
        $this->_assignData['search'] = $this->_listing_fields;
        $this->_assignData['columns']['ids'] = '<div class="checkbox-t"><input type="checkbox" id="check_all" name="check_all" /><label for="check_all"></label></div>';
        foreach ($this->_listing_fields as $listing_field) {
            $this->_assignData['columns'][$listing_field->name] = $listing_field->description;
        }
        $this->_assignData['columns']['options'] = 'Options';

        $view = View::make($this->_assignData["dir"] . __FUNCTION__, $this->_assignData);
        return $view;
    }

    /**
     * Ajax Listing
     * 
     * @return json 
     */
    public function ajaxListing(Request $request) {

        // datagrid params : sorting/order
        //$search_value = isset($_REQUEST['search']['value']) ? $_REQUEST['search']['value'] : '';
        $search_value = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : '';
        $dg_order = isset($_REQUEST['order'][0]['column']) ? $_REQUEST['order'][0]['column'] : '';
        $dg_sort = isset($_REQUEST['order'][0]['dir']) ? $_REQUEST['order'][0]['dir'] : '';
        $dg_columns = isset($_REQUEST['columns']) ? $_REQUEST['columns'] : '';

        $search_columns = array();
        if (trim($search_value) != "") {
            if (isset($_REQUEST['search_columns']) && is_array($_REQUEST['search_columns'])) {
                foreach ($_REQUEST['search_columns'] as $columns) {
                    $search_columns[$columns] = $search_value;
                }
            }
        }

        // default ordering
        if ($dg_order == "" && $dg_sort == "") {
            $dg_order = "created_at";
            $dg_sort = "ASC";
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
        $data = $this->apiPostRequest(\URL::to(DIR_API) . '/system/' . $this->_object_identifier . '/listing', 'GET', $search_columns);

        if ($data) {
            $this->_assignData['records'] = $data->data->{$this->_object_identifier . '_listing'};

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
                foreach ($paginated_ids as $paginated_id) {

                    //$id_record = $this->_model->get($paginated_id->{$this->_pk});
                    // status html
                    $status = "";
                    // options html
                    $options = '<div class="btn-group">';
                    // selectbox html
                    $checkbox = '<div class="checkbox-t">';
                    // manage options
                    // - update

                    $options .= '<a class="btn btn-xs btn-default" type="button" href="' . \URL::to($this->_panelPath . $this->_assignData['module'] . '/update/' . $paginated_id->{$this->_object_identifier . '_id'}) . '" data-toggle="tooltip" title="Update" data-original-title="Update"><i class="fa fa-pencil"></i></a>';
                    $options .= '<a data-module_url="delete" class="btn btn-xs btn-default grid_action_del delete_action" type="button" data-toggle="tooltip" title="" data-original-title="Delete"><i class="fa fa-times"></i></a>';

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
                    $list["options"] = $options;
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
    public function add(Request $request) {

        //Checking module Authentication
        // page action
        $this->_assignData["dir"] = config("panel.DIR") . $this->_object_identifier . '/';
        $data = $this->load_params('system/' . $this->_object_identifier, 'post');
        $this->_assignData['records'] = $data['records'];
        $this->_assignData["page_action"] = ucfirst(__FUNCTION__);
        $this->_assignData["route_action"] = strtolower(__FUNCTION__);

        // validate post form
        if ($request->do_post == 1) {
            return $this->_add($request);
        }

        $view = View::make($this->_assignData["dir"] . __FUNCTION__, $this->_assignData);
        return $view;
    }

    /**
     * Add (private)
     * 
     * @return view
     */
    private function _add(Request $request) {

        $_POST["created_at"] = date("Y-m-d H:i:s");
        $ret = $this->apiPostRequest(\URL::to(DIR_API) . '/system/' . $this->_object_identifier, 'POST', $_POST);
        if ($ret->error == "1") {
            $this->_assignData['error'] = $ret->message;
			\Session::put(ADMIN_SESS_KEY . '_POST_DATA', $_POST);
            \Session::put(ADMIN_SESS_KEY . 'error_msg', $ret->message);
            $this->_assignData['redirect'] = \URL::to($this->_panelPath . $this->_object_identifier . '/add');
            return $this->_assignData;
        } else {
            $this->_assignData['success'] = $ret->message;
            \Session::put(ADMIN_SESS_KEY . 'success_msg', $this->_assignData['success']);
            //redirect
            $this->_assignData['redirect'] = \URL::to($this->_panelPath . $this->_object_identifier);
            return $this->_assignData;
			
        }
    }

    /**
     * Update
     * 
     * @return view
     */
    public function update(Request $request, $department, $id) {
        // page action
        $this->_assignData["page_action"] = ucfirst(__FUNCTION__);
        $this->_assignData["route_action"] = strtolower(__FUNCTION__);

        // validate post form
        if (isset($request->do_post)) {
            return $this->_update($request);
        }

        $getData['attribute_id'] = $id;
        $this->_assignData["update"] = $this->__internalCall($request, \URL::to(DIR_API) . '/system/' . $this->_object_identifier, "get", $getData);

        $this->_assignData["dir"] = config("panel.DIR") . $this->_object_identifier . '/';
        $data = $this->load_params('system/' . $this->_object_identifier . '/update', 'post');
        $this->_assignData['records'] = $data['records'];

        // get record
        // $this->_assignData["data"] = $this->_model->get($id);
        // redirect on invalid record
        if ($this->_assignData["update"] == FALSE) {
            // set session msg
            \Session::put(ADMIN_SESS_KEY . 'error_msg', 'Invalid record selection');
            // redirect
            $this->_assignData['redirect'] = \URL::to(DIR_ADMIN . $this->_assignData['module']);
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
    private function _update(Request $request) {
        //$this->_assignData["data"] = $_POST;
        $this->_assignData["update"] =  $this->__internalCall($request,\URL::to(DIR_API) . '/system/' . $this->_object_identifier . '/update', 'POST', $_POST);

        if ($this->_assignData["update"]->error == "1") {
			\Session::put(ADMIN_SESS_KEY . '_POST_DATA', $_POST);
            \Session::put(ADMIN_SESS_KEY . 'error_msg', $this->_assignData["update"]->message);
            //redirect
            $this->_assignData['redirect'] = \URL::to($this->_panelPath . $this->_object_identifier . '/update/' . $request->{$this->_attribute_pk});
            return $this->_assignData;
        } else {
            \Session::put(ADMIN_SESS_KEY . 'success_msg', $this->_assignData["update"]->message);
            //redirect
            $this->_assignData['redirect'] = \URL::to($this->_panelPath . $this->_object_identifier);
            return $this->_assignData;
        }
        //return $this->_update($request, $this->_assignData["data"]);
    }

    /**
     * Select Action
     * 
     * @return query
     */
    private function _selectActions($request) {
        $request->select_action = trim($request->select_action);
        $request->checked_ids = is_array($request->checked_ids) ? $request->checked_ids : array();

        if ($request->select_action != "" && isset($request->checked_ids[0])) {
            foreach ($request->checked_ids as $checked_id) {
                $postData['attribute_id'] = $checked_id;
                $data = $this->apiPostRequest(\URL::to(DIR_API) . '/system/' . $this->_object_identifier . '/delete', 'POST', $postData);
            }
        }
    }

}
