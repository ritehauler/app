<?php
namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Api\System\EntityController;
use App\Http\Controllers\Controller;
use App\Http\Models\SYSAttribute;
use App\Libraries\CustomHelper;
use App\Libraries\EntityHelper;
use App\Libraries\Fields;
use Auth;
use View;
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

/**
 * Class EntityAjaxController
 */
Class EntityPanelController extends EntityBackController
{
    public function __construct(Request $request)
    {
        //$this->middleware('auth');
        // construct parent

        parent::__construct($request);

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function orderRating(Request $request)
    {

        $this->_assignData['entity_type_id'] = 15;
        $this->_assignData['module'] = 'orderRating';
        $this->_assignData['module_identifier'] = "Order Rating";

         $this->_assignData['columns']['customer_id'] = "Customer Name";
        $this->_assignData['columns']['order_id'] = "Order ID";
        $this->_assignData['columns']['star_rating'] = "Rating";
        $this->_assignData['columns']['created_at'] = 'Created On';
       // $this->_assignData['columns']['options'] = 'Options';

        (empty($view_file)) ? $view_file = $this->_assignData["dir"] .'order_rating/listing' : $view_file = $view_file;

        $view = View::make($view_file, $this->_assignData);
        return $view;
    }

    public function orderRatingListing(Request $request)
    {

       // $this->_assignData['module'] = $this->_object_identifier . "/" . $this->_entity_controller->identifier;

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


        $search_columns['entity_type_id'] = $request->entity_type_id;

        $search_columns['custom_query'][] = array('column' => 'star_rating', 'operator' => '<>','value' => '');

        /* if customer entity type check for status only blacklist*/
        $sub_link = "";

        $data = $this->__internalCall($request,\URL::to(DIR_API) . '/system/' . $this->_object_identifier . '/listing', 'GET', $search_columns,false);

        $total_records = 0;
        if (isset($data->data->page->total_records) && $data->data->page->total_records > 0) {

            // print_r($data);exit;
            $this->_assignData['records'] = $data->data->{$this->_object_identifier_list . '_listing'};

            // get total records count

            $total_records = $data->data->page->total_records; // total records
            //$total_records = count($query->get()); // total records
            // datagrid settings
            $dg_end = $dg_end > $total_records ? $total_records : $dg_end;

            $paginated_ids = $this->_assignData['records'];

            // if records
            if (isset($paginated_ids[0])) {
                $i = 0;

                foreach ($paginated_ids as $paginated_id) {

                    $list['customer_id'] = EntityHelper::parseAttributeValue($paginated_id->attributes->customer_id);
                    $list['order_id'] = $paginated_id->entity_id;
                    $list['star_rating'] = (isset($paginated_id->attributes->star_rating)) ? EntityHelper::parseAttributeValue($paginated_id->attributes->star_rating) : "";


                    if (isset($paginated_id->created_at)) {
                        $list['created_at'] = date(DATE_FORMAT_ADMIN, strtotime($paginated_id->created_at));
                    }


                   // $list["options"] = '';
                    //print_r( $list); die;
                    $records["data"][] = $list;
                    $i++;
                }
            }
        }
        $records["draw"] = $dg_draw;
        $records["recordsTotal"] = $total_records;
        $records["recordsFiltered"] = $total_records;


        echo json_encode($records);
    }

}