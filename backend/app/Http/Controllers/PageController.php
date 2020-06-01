<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use View;
use Cache;
use Input;
use Validator;
use Request;

// load models
use App\Http\Models\User as AppUser;
use App\Http\Models\Winner;
use App\Http\Models\MediaVote;
use App\Http\Models\Dish;
use App\Http\Models\Notification;
use App\Http\Models\Category;
use App\Http\Models\FriendSound;
use App\Http\Models\FriendNotify;
use App\Http\Models\HistoryNotification;

class PageController extends Controller {

	private $_assignData = array(
		'pDir' => '',
		'dir' => 'page/'
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
	 * Terms & Conditions
	 *
	 * @return HTML
	*/
	public function terms()
	{
		// init models
		/*$this->__models['user_model'] = new AppUser;
		
		// get params
		$email = trim(strip_tags(Input::get('email', '')));
		$hash = trim(strip_tags(Input::get('hash', '')));
		
		$this->_assignData["user"] = $user;
		*/
		
		$this->_layout .= view($this->_assignData["dir"]."/".__FUNCTION__, $this->_assignData)->with($this->__models);
		return $this->_layout;
	}
	
	/**
	 * Privacy Policy
	 *
	 * @return HTML
	*/
	public function privacy()
	{
		$this->_layout .= view($this->_assignData["dir"]."/".__FUNCTION__, $this->_assignData)->with($this->__models);
		return $this->_layout;
	}
	
	/**
	 * Faq
	 *
	 * @return HTML
	*/
	public function faq()
	{
		$this->_layout .= view($this->_assignData["dir"]."/".__FUNCTION__, $this->_assignData)->with($this->__models);
		return $this->_layout;
	}
	
	/**
	 * About us
	 *
	 * @return HTML
	*/
	public function about()
	{
		$this->_layout .= view($this->_assignData["dir"]."/".__FUNCTION__, $this->_assignData)->with($this->__models);
		return $this->_layout;
	}
	
	/**
	 * Test
	 *
	 * @return HTML
	*/
	public function test()
	{
		exit("test profile");
	}

}
