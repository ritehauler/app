<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Auth;
use Crypt;
use Input;
use Session;
use Request;


// load models
//use App\Http\Models\User;

class CheckController extends Controller {

	private $_assign_data = array(
		'p_dir' => '',
		'dir' => 'og/'
	);
	private $_header_data = array();
	private $_footer_data = array();
	private $_layout = "";

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//$this->middleware('guest');
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
	 * Share
	 *
	 * @return Response
	*/
	public function adminAuth(Request $request)
	{
		// params
		$encrypt_data = trim(Request::get("encrypt_data"));
		
		// init output
		$return = "";
		
		if($encrypt_data == "") {
			$return = "";
		} else {
			try {
				$decrypted = Crypt::decrypt($encrypt_data);
				return $decrypted;
			} catch (DecryptException $e) {
				//
				$return = "";
			}
		}
		return $return;		
	}

}
