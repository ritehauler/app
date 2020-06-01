<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
// load models
use App\Http\Models\ApiMethod;
use App\Http\Models\ApiMethodField;
use App\Http\Models\ApiUser;
use App\Http\Models\Conf;
use App\Http\Models\Asset;


class AssetController extends Controller {

    public $_assignData = array(
		'pDir' => '',
		'dir' => DIR_API
	);
    public $_apiData = array();
    public $_layout = "";
    public $_models = array();
    public $_jsonData = array();

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(Request $request)
	{
		// init models
		$this->__models['api_method_model'] = new ApiMethod;
		$this->__models['api_user_model'] = new ApiUser;
		$this->__models['asset_model'] = new Asset;




		// check access
		//$this->__models['api_user_model']->checkAccess($request);
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
	 * virtualItems
	 *
	 * @return JSON
	*/
	public function getAll(Request $request)
	{	
		// init vars
		$def_types = array("image", "audio", "video", "xml");
		
		// get params
		$type = trim(strip_tags($request->type));
		$page_no = intval(trim(strip_tags($request->page_no)));
		
		
		if($type != "" && (!in_array($type,$def_types))) {
			$this->_apiData['message'] = 'Invalid type';
		}
		else {
			// success response
			$this->_apiData['response'] = "success";
			// init output data array
			$this->_apiData['data'] = $data = array();
			
			// set initial array for records
			$data["assets"] = array();
			
			$query = $this->__models['asset_model']->select("asset_id");
			$query->orderBy("created_at", "ASC");
			if($type != "") {
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
			if(isset($raw_records[0])) {
				//var_dump($raw_records); exit;
				foreach($raw_records as $raw_record) {
					$asset = $this->__models['asset_model']->getData($raw_record->asset_id);
					
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
		
		
		return $this->__ApiResponse($request,$this->_apiData);
	}
	

}
