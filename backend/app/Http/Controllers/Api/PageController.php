<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
// load models
use App\Http\Models\User;
use App\Http\Models\ApiMethod;
use App\Http\Models\ApiMethodField;
use App\Http\Models\ApiUser;
use App\Http\Models\Page;


class PageController extends Controller {

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
		$this->__models['page_model'] = new Page;
		
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
	 * Get page by Key
	 *
	 * @return Response
	*/
	public function getBySlug(Request $request)
	{
		$slug = trim(strip_tags(Input::get('slug', 'about')));
		$slug = in_array($slug, array("terms","about","privacy","faq")) ? $slug : ""; // set default value
		
		$page = $this->__models['page_model']->getBy("slug", $slug);
		
		// get data
		//$user = $this->__models['user_model']->get($user_id);
		
		if($slug == "") {
			$this->_apiData['message'] = 'Please provide proper Slug';
		}
		else if($page === FALSE) {
			$this->_apiData['message'] = 'Invalid page Request';
		}
		/*if($user_id == 0) {
			$this->_apiData['message'] = 'Please enter User ID';
		}
		else if($user === FALSE) {
			$this->_apiData['message'] = 'Invalid User Request';
		}*/
		else {
			
			// success response
			$this->_apiData['response'] = "success";
			// kick user
			//$this->_apiData['kick_user'] = $user->status == 3 ? 1 : 0;
			
			// init output data array
			$this->_apiData['data'] = $data = array();
			
			// unset unrequired
			unset($page->page_id,$page->slug);
			$data["page"] = $page;
			
			
			// assign to output
			$this->_apiData['data'] = $data;		
		}
		
		
		return $this->__ApiResponse($request,$this->_apiData);
	}
	

}
