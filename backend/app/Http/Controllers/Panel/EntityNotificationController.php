<?php
/**
 * Description: this is related to system notification work
 * Author: Samreen <samreen.quyyum@cubixlabs.com>
 * Date: 22-03-2018
 * Time: 06:00 PM
 * Copyright: CubixLabs
 */

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Api\System\EntityController;
use Auth;
use View;
use DB;
use Validator;
use Illuminate\Http\Request;
use Redirect;
use Mail;
use Illuminate\Support\Facades\Input;
use Response;

use App\Libraries\EntityNotification;

/**
 * Class EntityNotificationController
 * @package App\Http\Controllers\Panel
 */
Class EntityNotificationController extends EntityController
{
    private $_object_identifier = "notification";
    private $_listing_fields = array();
    private $_entities = "entities";

    /**
     * EntityNotificationController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->_assignData["dir"] = config("panel.DIR") .$this->_entities. '/';
        $this->_assignData["_meta"] = $this->__meta;
        // assign request
        $this->_assignData["request"] = $request;
        //module
        $this->_assignData['module'] = $this->_object_identifier;

    }

    /**
     * @param Request $request
     * @return \App\Http\Controllers\Api\System\Response
     */
    public function listing(Request $request)
    {
        $this->_assignData['module'] = $this->_object_identifier;
        $this->_assignData['search'] = $this->_listing_fields;
        $this->_assignData['columns']['message'] = 'Notification';
        $this->_assignData['columns']['option'] = 'Module';
        $this->_assignData['columns']['created_at'] = 'Created at';

        $view = View::make($this->_assignData["dir"] .'notification', $this->_assignData);
        return $view;
    }

    /**
     * @param Request $request
     */
    public function ajaxListing(Request $request)
    {
        // datagrid params : sorting/order
        $search_value = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : '';
        $dg_order = isset($_REQUEST['order'][0]['column']) ? $_REQUEST['order'][0]['column'] : '';
        $dg_sort = isset($_REQUEST['order'][0]['dir']) ? $_REQUEST['order'][0]['dir'] : '';
        $dg_columns = isset($_REQUEST['columns']) ? $_REQUEST['columns'] : '';

        $search_columns = isset($_REQUEST['search_columns']) ? $_REQUEST['search_columns'] : array();
        //  print_r($search_columns); exit;
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


        // init output
        $records = array();
        $records["data"] = array();

        $dg_limit = intval($_REQUEST['length']);
        $dg_limit = $dg_limit < 0 ? $total_records : $dg_limit;
        $dg_start = intval($_REQUEST['start']);
        $dg_draw = intval($_REQUEST['draw']);
        $dg_end = $dg_start + $dg_limit;


        $search_columns['limit'] = $dg_limit;
        $search_columns['offset'] = $dg_start;
        $search_columns['order_by'] = $dg_order;
        $search_columns['sorting'] = $dg_sort;

       // $data = $this->__internalCall($request,\URL::to(DIR_API) . '/system/' . $this->_object_identifier . '/listing', 'GET', $search_columns,false);
        $sys_notification_lib = new EntityNotification();
        $data = $sys_notification_lib->listing($search_columns);
        $data = json_decode(json_encode($data));
       // print_r($data); exit;
        $this->_assignData['records'] = $data->data->{$this->_object_identifier};

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
            $separate_identifier = array('general_setting','product_tags','recipe_tags');
            $out_of_entity_module = array('role','category','group');

            foreach ($paginated_ids as $paginated_id) {
                $list['option'] = $paginated_id->target_entity_type_title;
               // $link = \URL::to( $this->_panelPath .'entities/'.$paginated_id->target_entity_type_identifier).'?entity_notification_id='.$paginated_id->entity_notification_id;

                if(in_array($paginated_id->target_entity_type_identifier,$separate_identifier)){
                    $link = \URL::to($this->_panelPath  .'entities/'.$paginated_id->target_entity_type_identifier).'?entity_notification_id='.$paginated_id->entity_notification_id;
                }
                elseif(in_array($paginated_id->target_entity_type_identifier,$out_of_entity_module)){
                    $link = \URL::to($this->_panelPath .$paginated_id->target_entity_type_identifier).'/view/'.$paginated_id->entity_id.'?entity_notification_id='.$paginated_id->entity_notification_id;
                }
                else{
                    $link = \URL::to($this->_panelPath .'entities/'.$paginated_id->target_entity_type_identifier).'/view/'.$paginated_id->entity_id.'?entity_notification_id='.$paginated_id->entity_notification_id;

                }

                if(trim(strtolower($paginated_id->permission)) == 'add')  $permission_title = strtolower($paginated_id->permission).'ed';
                else $permission_title = strtolower($paginated_id->permission).'d';

                $message = $paginated_id->actor_entity_type_title.' has '.$permission_title.' '.$paginated_id->target_entity_type_title.' # ';
               $message .= '<a href="'.$link.'" >';
                $message .= $paginated_id->entity_id;
                $message .= '</a>';


                $list['message'] = $message;
                $list['created_at'] = date(DATE_FORMAT_ADMIN, strtotime($paginated_id->created_at));
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

    /**
     * Ajax Request - Count Notification
     * @param Request $request
     * @return array
     */
    public function countNotification(Request $request)
    {
        $entity_notification_model = new EntityNotification();
        $total_count = $entity_notification_model->getTotalCount();
        return array('error' => 0,'data'=> ['total_count' =>$total_count ] ,'message' => 'Success');
    }


    /**
     * Ajax Request - List Notification in notifation try
     * @param Request $request
     * @return array
     * @throws \Throwable
     */
    public function listNotification(Request $request)
    {
        $entity_notification_model = new EntityNotification();
        $list = $entity_notification_model->getList($request->all());

        $view_file = $this->_assignData["dir"] ."ajax/notification-list";
        $html =  view($view_file,['data'=>$list,'panel_path' => $this->_panelPath])->render();

        return array('error' => 0,'data'=> ['html' =>$html ] ,'message' => 'Success');
    }

}