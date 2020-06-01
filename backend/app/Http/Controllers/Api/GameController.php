<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
// load models
use App\Http\Models\ApiMethod;
use App\Http\Models\ApiMethodField;
use App\Http\Models\ApiUser;
use App\Http\Models\Conf;
use App\Http\Models\QAContainerBehavior;
use App\Http\Models\QAPackageType;
use App\Http\Models\Level;
use App\Http\Models\Achievement;
use App\Http\Models\VirtualItem;

class GameController extends Controller {

	private $_assignData = array(
		'pDir' => '',
		'dir' => DIR_API
	);
	private $_apiData = array();
	private $_layout = "";
	private $_models = array();
	private $_jsonData = array();

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
		
		// error response by default
		$this->_apiData['kick_user'] = 0;
		$this->_apiData['response'] = "error";
		
		// check access
		$this->__models['api_user_model']->checkAccess($request);
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
	 * Configurations
	 *
	 * @return JSON
	*/
	public function configurations(Request $request)
	{
		// init models
		$this->__models['conf_model'] = new Conf;
		$this->__models['qa_container_behavior_model'] = new QAContainerBehavior;
		$this->__models['qa_package_type_model'] = new QAPackageType;
		
		
		
		// success response
		$this->_apiData['response'] = "success";
		
		// init output data array
		$this->_apiData['data'] = $data = array();
		
		// get conf
		$conf_data = $this->__models['conf_model']->getBy("key","game_config");
		
		// put configurations in data
		$data["configuration"] = $conf_data ? json_decode($conf_data->value) : (object)array();
		
		
		// get container behaviors
		$data["qa_container_behavior"] = array();
		$raw_ids = $this->__models['qa_container_behavior_model']->select("container_behavior_id")->get();
		if(isset($raw_ids[0])) {
			foreach($raw_ids as $raw_id) {
				$record = $this->__models['qa_container_behavior_model']->getData($raw_id->container_behavior_id);
				$data["qa_container_behavior"][] = $record;
			}
		}
		
		// get container behaviors
		$data["qa_package_type"] = array();
		$raw_ids = $this->__models['qa_package_type_model']->select("package_type_id")->get();
		if(isset($raw_ids[0])) {
			foreach($raw_ids as $raw_id) {
				$record = $this->__models['qa_package_type_model']->getData($raw_id->package_type_id);
				$data["qa_package_type"][] = $record;
			}
		}
		
		
		// put last updated date
		$data["updated_at"] = $conf_data->updated_at;
		
		// assign to output
		$this->_apiData['data'] = $data;	
		
		
		return $this->__ApiResponse($request,$this->_apiData);
	}
	
	
	/**
	 * virtualItems
	 *
	 * @return JSON
	*/
	public function virtualItems(Request $request)
	{
		// init models
		$this->__models['virtual_item_model'] = new VirtualItem;
		// trim/escape all
		$request->merge(array_map('strip_tags', $request->all()));
		$request->merge(array_map('trim', $request->all()));
		
		$allowed_order = array("title","created_at","updated_at");
		$allowed_sorting = array("ASC","DESC");
		
		// validate
		$request->order = in_array($request->order,$allowed_order) ? $request->order : "title";
		$request->sorting = in_array(strtoupper($request->sorting),$allowed_sorting) ? strtoupper($request->sorting) : "ASC"; 
		
		// success response
		$this->_apiData['response'] = "success";
		
		// init output data array
		$this->_apiData['data'] = $data = array();
		
		// fetch ids
		$query  = $this->__models['virtual_item_model']->select("virtual_item_id");
		$query->where("type","=","inapp");
		$query->whereNull("deleted_at");
		$query->orderBy($request->order,$request->sorting);
		$raw_ids = $query->get();
		
		// fetch records
		if(isset($raw_ids[0])) {
			foreach($raw_ids as $raw_id) {
				$record = $this->__models['virtual_item_model']->getData($raw_id->virtual_item_id);				
				$data[] = $record;
			}
		}
		
		// assign to output
		$this->_apiData['data'] = $data;	
		
		
		return $this->__ApiResponse($request,$this->_apiData);
	}
	
	
	/**
	 * levels
	 *
	 * @return JSON
	*/
	public function levels(Request $request)
	{
		// init models
		$this->__models['level_model'] = new Level;
		
		
		
		
		// success response
		$this->_apiData['response'] = "success";
		
		// init output data array
		$this->_apiData['data'] = $data = array();
		
		// fetch ids
		$query  = $this->__models['level_model']->select("level_id");
		$query->whereNull("deleted_at");
		$query->orderBy("created_at","ASC");
		$raw_ids = $query->get();
		
		// fetch records
		if(isset($raw_ids[0])) {
			foreach($raw_ids as $raw_id) {
				$record = $this->__models['level_model']->getData($raw_id->level_id);				
				$data[] = $record;
			}
		}
		
		// assign to output
		$this->_apiData['data'] = $data;	
		
		
		return $this->__ApiResponse($request,$this->_apiData);
	}
	
	
	/**
	 * achievements
	 *
	 * @return JSON
	*/
	public function achievements(Request $request)
	{
		// init models
		$this->__models['achievement_model'] = new Achievement;
		
		// success response
		$this->_apiData['response'] = "success";
		
		// init output data array
		$this->_apiData['data'] = $data = array();
		
		// fetch ids
		$query  = $this->__models['achievement_model']->select("achievement_id");
		$query->whereNull("deleted_at");
		$query->orderBy("created_at","ASC");
		$raw_ids = $query->get();
		
		// fetch records
		if(isset($raw_ids[0])) {
			foreach($raw_ids as $raw_id) {
				$record = $this->__models['achievement_model']->getData($raw_id->achievement_id);				
				$data[] = $record;
			}
		}
		
		// assign to output
		$this->_apiData['data'] = $data;	
		
		
		return $this->__ApiResponse($request,$this->_apiData);
	}
	

}
