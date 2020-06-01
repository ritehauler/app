<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use View;
use Cache;
use Input;
use Validator;
use Illuminate\Http\Request;

// load models
use App\Http\Models\CCUser;

class CCUserController extends Controller {

	private $_assignData = array(
		'pDir' => '',
		'dir' => 'cc_user/'
	);
	private $_headerData = array();
	private $_footerData = array();
	private $_layout = "";

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(Request $request)
	{
		//$this->middleware('guest');
		
		// init models
		$this->__models['user_model'] = new CCUser;
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
	 * Confirm user Signup
	 *
	 * @return HTML
	*/
	public function confirmSignup(Request $request)
	{
		// init models
		//$this->__models['user_model'] = new AppUser;
		
		// init blank msg
		$msg = "";
		
		// get params
		$email = trim(strip_tags($request->input('email', '')));
		$hash = trim(strip_tags($request->input('hash', '')));
		
		// validations
		$valid_email = Validator::make(array('email' => $email), array('email' => 'email'));
		
		// fetch data
		$row_exists = $this->__models['user_model']
			->where('verification_token', '=', $hash)
			->where('email', '=', $email)
			->whereNull("deleted_at")
			->get(array("user_id"));
		
		$user_id = isset($row_exists[0]) ? $row_exists[0]->user_id : 0;
		
		// validations
		if($valid_email->fails() || $user_id === 0) {
			$msg = trans('cc_errors.invalid_confirmation_link');
		}
		/*if($email == "") {
			$msg = "Please Enter Email";
		}
		else if($valid_email->fails()) {
			$this->_apiData['message'] = 'Please enter valid Email';
		}
		else if($user_id === 0) {
			$msg = "Invalid confirmation Link";
		}*/
		else {
			// get user data
			$user = $this->__models['user_model']->get($user_id);
			
			// send forgot password email
			$this->__models['user_model']->welcomeUser($user);
			
			$msg = trans('cc_errors.account_successfully_activated');
		}
		
		if($msg !== "") {
			echo "<div align='center'><h2>$msg</h2></div>";
		}
		
	}
	
	

}
