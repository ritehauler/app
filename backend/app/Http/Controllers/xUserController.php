<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use View;
use Cache;
use Input;
use Validator;
use Request;

// load models
use App\Http\Models\User as AppUser;

class xUserController extends Controller {

	private $_assignData = array(
		'pDir' => '',
		'dir' => 'user/'
	);
	private $_headerData = array();
	private $_footerData = array();
	private $_layout = "";

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest');
		
		// init models
		$this->__models['user_model'] = new AppUser;
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		return "Hello World";		
	}
	
	
	/**
	 * Web Profile
	 *
	 * @return HTML
	*/
	public function webProfile($user_name)
	{
		// init models
		
		// set param
		$user_name = trim($user_name);
		
		if($user_name != "") {
			$row_type_exists = $this->__models['user_model']
				->where('web_username', '=', $user_name)
				->get(array("user_id"));
			
			$exists_id = isset($row_type_exists[0]) ? $row_type_exists[0]->user_id : 0;
			
			
			$this->_assignData["user"] = $this->__models['user_model']->getData($exists_id);
			
			
			$this->_layout .= view($this->_assignData["dir"]."/web_profile", $this->_assignData)->with($this->__models);
			return $this->_layout;
		}
		
	}
	

}
