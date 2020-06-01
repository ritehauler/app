<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use View;
use Session;
use Validator;
use Image;
// load models
use App\Http\Models\User;
use App\Http\Models\ApiMethod;
use App\Http\Models\ApiMethodField;
use App\Http\Models\ApiUser;
use App\Http\Models\UserHistory;
use App\Http\Models\GEUserLevel;
use App\Http\Models\Level;

class xUserController extends Controller {

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
    public function __construct(Request $request) {
        // init models
        $this->__models['api_method_model'] = new ApiMethod;
        $this->__models['user_model'] = new User;
        $this->__models['api_user_model'] = new ApiUser;
		$this->__models['user_history_model'] = new UserHistory;

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
    public function index() {
        
    }
	
	
	/**
     * login
     *
     * @return Response
     */
    public function login(Request $request) {
		// trim/escape all
		$request->merge(array_map('strip_tags', $request->all()));
		$request->merge(array_map('trim', $request->all()));
		
		// param validations
		$validator = Validator::make($request->all(), array(
			'device_udid' => 'required|alpha_num',
			'device_type' => 'required|in:android,ios'
		));
		
		// get user existance
		$raw_exists = $this->__models['user_model']->select("user_id")
			->where("device_type","=",$request->device_type)
			->where("device_udid","=",$request->device_udid)
			->whereNull("deleted_at")
			->get();
		$exists_id = isset($raw_exists[0]) ? $raw_exists[0]->user_id : 0;
		
		// get data
		$user = $this->__models['user_model']->get($exists_id);
		
        // validations
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } elseif($user !== FALSE && $user->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($user !== FALSE && $user->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} else {
			// success response
            $this->_apiData['response'] = "success";
            // init output data array
            $this->_apiData['data'] = $data = array();
			

            // create account if not exists
            if ($user === FALSE) {
				
				// set data
				$user['device_udid'] = $request->device_udid;
				$user['device_type'] = $request->device_type;
				$user['status'] = 1;		
				// create user
				$user_id = $this->__models['user_model']->signupViaDevice($user);
				// unset user array
				unset($user);
				// get user data
            	$user = $this->__models['user_model']->get($user_id);
				
            }

            // update last login as current timestamp
            $user->last_login_at = date('Y-m-d H:i:s');
			// device data
            if ($request->device_token != "" && $request->device_type != "") {
				$user->device_type = $request->device_type;
                $user->device_token = $request->device_token;
                // remove this device token from other user ids
                $this->__models['user_model']->replaceToken($user->user_id, $user->device_token);
            }
			
			// update user data
            $this->__models['user_model']->set($user->user_id, (array) $user);

            
            // response data
            $data['user'] = $this->__models['user_model']->getData($user->user_id);
			
			// message
			$this->_apiData['message'] = "Success";

            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request,$this->_apiData);
    }
	
	
	/**
     * changeLevel
     *
     * @return Response
     */
    public function changeLevel(Request $request) {
		// init models
		$this->__models['level_model'] = new Level;
		
		// trim/escape all
		$request->merge(array_map('strip_tags', $request->all()));
		$request->merge(array_map('trim', $request->all()));
		
		// param validations
		$validator = Validator::make($request->all(), array(
			'user_id' => 'required|exists:user,user_id',
			'level_id' => 'required|exists:level,level_id'
		));
		
		// get data
		$user = $this->__models['user_model']->getData($request->user_id);
		$level = $this->__models['level_model']->getData($request->level_id);
		
        // validations
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else if ($level === FALSE) {
            $this->_apiData['message'] = "Invalid Level ID";
        } elseif($user !== FALSE && $user->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($user !== FALSE && $user->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} else {
			// init models
			$user_level_model = new GEUserLevel;
			
			// success response
            $this->_apiData['response'] = "success";
            // init output data array
            $this->_apiData['data'] = $data = array();
			
			
			// update user data
            $user_level_model->changeData($request->user_id, $request->level_id);
            
            // response data
            $data['user'] = $this->__models['user_model']->getData($user->user_id);
			
			// message
			$this->_apiData['message'] = "Successfully changed level";

            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request,$this->_apiData);
    }
	
	
	
	/**
     * User Social Login
     *
     * @return Response
     */
    public function socialLogin() {
        // get params
        $name = trim(strip_tags(Input::get('name', '')));
		$first_name = trim(strip_tags(Input::get('first_name', '')));
		$last_name = trim(strip_tags(Input::get('last_name', '')));
        $gender = trim(strip_tags(Input::get('gender', '')));
		$gender = in_array($gender, array("male", "female")) ? $gender : "male"; // set default value
		$interested_gender = $gender == "male" ? "female" : "male"; // set default value
		$about_me = trim(strip_tags(Input::get('about_me', '')));
		$dob = trim(strip_tags(Input::get('dob', '')));
		$dob = $dob == "" ? "0000-00-00" : date("Y-m-d",strtotime($dob));
		$looking_for = trim(strip_tags(Input::get('looking_for', '')));
		$looking_for = in_array($looking_for, array("friendship", "dating")) ? $looking_for : "dating"; // set default value
		$email = trim(str_replace(" ","",strip_tags(Input::get('email', ''))));
		$type = trim(strip_tags(Input::get('type', '')));
        $type = in_array($type, array("facebook", "twitter", "gplus")) ? $type : "facebook"; // set default value
        $uid = trim(strip_tags(Input::get('uid', 0)));
        $uid = $uid == "" ? 0 : $uid; // set default value
		//$image_url = trim(strip_tags(Input::get('image_url', '')));
		//$thumb_url = trim(strip_tags(Input::get('thumb_url', '')));
		$location = trim(strip_tags(Input::get('location', '')));
		$disabilities_ids = trim(strip_tags(Input::get('disabilities_ids', '')));
		$works = trim(strip_tags(Input::get('works', '')));
		$selected_works = trim(strip_tags(Input::get('selected_works', '')));
		$educations = trim(strip_tags(Input::get('educations', '')));
		$selected_educations = trim(strip_tags(Input::get('selected_educations', '')));
		$interest_raw_data = trim(strip_tags(Input::get('interest_raw_data', '')));		
		
        $device_type = trim(strip_tags(Input::get('device_type', '')));
        $device_type = in_array($device_type, array("android", "ios")) ? $device_type : "android"; // set default value
        $device_token = trim(strip_tags(Input::get('device_token', '')));
        $latitude = trim(strip_tags(Input::get('latitude', 0)));
        $latitude = $latitude == "" ? 0 : $latitude; // set default value
		$longitude = trim(strip_tags(Input::get('longitude', 0)));
        $longitude = $longitude == "" ? 0 : $longitude; // set default value
		$photo_urls = trim(strip_tags(Input::get('photo_urls', '')));
		$show_disability = intval(trim(strip_tags(Input::get('show_disability', 0))));
		$show_disability = $show_disability > 1 ? 1 : $show_disability; // default value
		
        // validations
        $valid_email = Validator::make(array('email' => $email), array('email' => 'email'));
        $row_type_exists = $this->__models['user_model']
			->where('type', '=', $type)
			->where('uid', '=', $uid)
			->get(array("user_id"));
		
		$exists_id = isset($row_type_exists[0]) ? $row_type_exists[0]->user_id : 0;
		
		// get user data
		$user = $this->__models['user_model']->get($exists_id);		
		
        // validations
        if ($name == '') {
            $this->_apiData['message'] = 'Please enter Name';
        } else if ($first_name == '') {
            $this->_apiData['message'] = 'Please enter First Name';
        } else if ($last_name == '') {
            $this->_apiData['message'] = 'Please enter Last Name';
        } else if ($user === FALSE && $dob == "") {
            $this->_apiData['message'] = "Please provide Date of Birth";
        } else if ($email == "") {
            $this->_apiData['message'] = "Please enter Email";
        } else if ($valid_email->fails()) {
            $this->_apiData['message'] = 'Please enter valid Email';
        } else if ($type == "") {
            $this->_apiData['message'] = 'Please provide Login Type';
        } else if ($uid == 0) {
            $this->_apiData['message'] = 'Please provide UID';
        }
		else if ($user === FALSE && $photo_urls == "") {
            $this->_apiData['message'] = "Please provide Photo URLs";
        } /*else if ($user === FALSE && $image_url == "") {
            $this->_apiData['message'] = "Please provide Social Image URL";
        } else if ($user === FALSE && $location == "") {
            $this->_apiData['message'] = "Please provide Location";
        }*/ else if ($user === FALSE && $latitude == 0) {
            $this->_apiData['message'] = "Please provide Latitude";
        } else if ($user === FALSE && $longitude == 0) {
            $this->_apiData['message'] = "Please provide Longitude";
        } /*else if ($user === FALSE && $disabilities_ids == "") {
            $this->_apiData['message'] = "Please provide Disabilities";
        } else if ($user === FALSE && $works == "") {
            $this->_apiData['message'] = "Please provide Works";
        } else if ($user === FALSE && $educations == "") {
            $this->_apiData['message'] = "Please provide Educations";
        } else if ($user === FALSE && $interest_raw_data == "") {
            $this->_apiData['message'] = "Please provide Interests Raw Data";
        }*/else if ($works != "" && $selected_works == "") {
            $this->_apiData['message'] = "Please provide selected works";
        } else if ($educations != "" && $selected_educations == "") {
            $this->_apiData['message'] = "Please provide selected educations";
        } else if ($device_type == "") {
            $this->_apiData['message'] = 'Please provide Device Type';
        } elseif($user !== FALSE && $user->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($user !== FALSE && $user->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} else {
			// init models
			$this->__models['user_disability_model'] = new UserDisability;
			$this->__models['user_background_model'] = new UserBackground;
			$this->__models['user_photo_model'] = new UserPhoto;
			
			// success response
            $this->_apiData['response'] = "success";
            // init output data array
            $this->_apiData['data'] = $data = array();
			

            // create account if not exists
            if ($user === FALSE) {
				// get defaults
				$age = $this->__models['user_model']->getAge($dob);
				$interest_raw_data = $interest_raw_data == "" ? json_encode(array()) : $interest_raw_data;
				
				// set data
				$user['name'] = $name;
				$user['first_name'] = $first_name;
				$user['last_name'] = $last_name;
				$user['gender'] = $gender;
				$user['interested_gender'] = $interested_gender;
				$user['looking_for'] = $looking_for;
				$user['about_me'] = $about_me;
                $user['email'] = $email;
				$user['type'] = $type;
				$user['uid'] = $uid;
				$user['dob'] = $dob;
				$user['age'] = $age;
				$user['location'] = $location;
				$user['latitude'] = $latitude;
				$user['longitude'] = $longitude;
				// defaults
				$user['interested_age_range'] = "18-30";
				$user['search_distance'] = 50;
				$user['distance_unit'] = "mile";
				$user['is_discoverable'] = 1;
				$user['show_disability'] = 1;
				$user['interest_raw_data'] = $interest_raw_data;
				$user['image'] = "";
				$user['thumb'] = "";
				
				
				
				// create user
				$user_id = $this->__models['user_model']->createSocialUser($user);
				// unset user array
				unset($user);
				// get user data
            	$user = $this->__models['user_model']->get($user_id);
				
				// save user image
				// - take from photo_urls
				/*if($image_url != "") {
					$img_data = @file_get_contents($image_url);
					
					$user_img = "dp-" . $user->user_id . ".jpg";
                    $create_success = @file_put_contents(getcwd() . "/" . DIR_USER_IMG . $user_img, $img_data);

                    // if dp created successfully, create thumbnail
                    if ($create_success) {
                        $user_thumb = "thumb-" . $user->user_id . ".jpg";
                        $thumb_data = file_get_contents(url("/") . "/" . "thumb/user/150x150/" . $user_img);
                        @file_put_contents(getcwd() . "/" . DIR_USER_IMG . $user_thumb, $thumb_data);
						
						// assign new values
						$user->image = $user_img;
						$user->thumb = $user_thumb;
                    }
					
				}*/
				
				// save user works (background)
				$this->__models['user_background_model']->putUserBackground($user_id,"work",$works, $selected_works);
				// save user educations (background)
				$this->__models['user_background_model']->putUserBackground($user_id,"education",$educations, $selected_educations);
				// save user disabilities
				if($disabilities_ids != "") {
					$this->__models['user_disability_model']->putUserDisabilities($user_id,$disabilities_ids);
				}
				
            }

            // update last login as current timestamp
            $user->name = $name;
			$user->first_name = $first_name;
			$user->last_name = $last_name;
			$user->email = $email;
			$user->show_disability = $show_disability;
			$user->about_me = $about_me == "" ? $user->about_me : $about_me;
            $user->last_login_at = date('Y-m-d H:i:s');
			// device data
            if ($device_token != "" && $device_type != "") {
                $user->device_type = $device_type;
                $user->device_token = $device_token;
                // remove this device token from other user ids
                $this->__models['user_model']->replaceToken($user->user_id, $user->device_token);
            }
            $user->latitude = $latitude == 0 ? $user->latitude : $latitude;
			$user->longitude = $longitude == 0 ? $user->longitude : $longitude;
			// if user came back, activate account
			if($user->deleted_at !== NULL) {
				$user->deleted_at = NULL;
				// set current location to true
				$user->use_current_location = 1;
				// save user disabilities
				if($disabilities_ids != "") {
					$this->__models['user_disability_model']->putUserDisabilities($user->user_id,$disabilities_ids);
				}
			}

            // save photo urls
			if($photo_urls != "") {
				// get user photos
				$user_photos = $this->__models['user_photo_model']->getUserPhotos($user->user_id);
				// if user dont have photo, then save - otherwise ignore
				if(isset($user_photos[0])) {
					// nothing to do
				} else {
					//$photos_urls_data = preg_match("@,@",$photo_urls) ? explode(",",$photo_urls) : array();
					$photos_urls_data = explode(",",$photo_urls);
					
					if(isset($photos_urls_data[0])) {
						// flush old
						$this->__models['user_photo_model']->removeUserPhotos($user->user_id, "facebook");
						
						// save user photos (background)
						$saved_imgs = $this->__models['user_photo_model']->putUserPhotoUrls($user->user_id,"facebook", $photo_urls, 1);
						
						// mark first as display picture
						$user->image = $saved_imgs[0]["url"];
						$user->thumb = $saved_imgs[0]["url"];
						
					}
				}
				
			}
			
			// update user data
            $this->__models['user_model']->set($user->user_id, (array) $user);

            
            // response data
            $data['user'] = $this->__models['user_model']->getData($user->user_id);
			
			// message
			$this->_apiData['message'] = "Success";

            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request,$this->_apiData);
    }
	
	
	/**
     * is_social_registered
     *
     * @return Response
     */
    public function isSocialRegistered() {
        // get params
        $type = trim(strip_tags(Input::get('type', '')));
        $type = in_array($type, array("facebook", "twitter", "gplus")) ? $type : ""; // set default value
        $uid = trim(strip_tags(Input::get('uid', '')));
        $uid = $uid == "" ? '' : $uid; // set default value
        $device_type = trim(strip_tags(Input::get('device_type', '')));
        $device_type = in_array($device_type, array("android", "ios")) ? $device_type : ""; // set default value
        $device_token = trim(strip_tags(Input::get('device_token', '')));

        $row_type_exists = $this->__models['user_model']
                ->where('type', '=', $type)
                ->where('uid', '=', $uid)
				->whereNull('deleted_at')
                ->get(array("user_id")
        );
        $user_id = isset($row_type_exists[0]) ? $row_type_exists[0]->user_id : 0;

        // get user data
        $user = $this->__models['user_model']->get($user_id);

        if ($type == "") {
            $this->_apiData['message'] = 'Please provide Social Login Type';
        } else if ($uid == "") {
            $this->_apiData['message'] = 'Please provide UID';
        } else if ($device_type == "") {
            $this->_apiData['message'] = 'Please provide Device Type';
        } else if ($user === FALSE) {
            $this->_apiData['message'] = 'User does not exist';
        } elseif($user !== FALSE && $user->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($user !== FALSE && $user->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} else {
            // success response
            $this->_apiData['response'] = "success";
			// init output data array
            $this->_apiData['data'] = $data = array();

            // update last login as current timestamp
            $user->last_login_at = date('Y-m-d H:i:s');
			
            if ($device_token != "" && $device_type != "") {
                $user->device_type = $device_type;
                $user->device_token = $device_token;
            }
            $this->__models['user_model']->set($user->user_id, (array) $user);

            $data['user'] = $this->__models['user_model']->getData($user->user_id);
			
			// message
			$this->_apiData['message'] = "Success";

            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__ApiResponse($request,$this->_apiData);
    }
	
	/**
     * User data
     *
     * @return Response
     */
    public function get() {
        // get params
        $user_id = trim(strip_tags(Input::get('user_id', 0)));
        $user_id = $user_id == "" ? 0 : $user_id; // set default value
        $device_type = trim(strip_tags(Input::get('device_type', '')));
        $device_type = in_array($device_type, array("android", "ios")) ? $device_type : "android"; // set default value
        $device_token = trim(strip_tags(Input::get('device_token', '')));

        // get data
        $user = $this->__models['user_model']->get($user_id);

        if ($user_id == 0) {
            $this->_apiData['message'] = 'Please enter User ID';
        } else if ($user === FALSE) {
            $this->_apiData['message'] = 'User does not exist';
        } elseif($user->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($user->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} else {
            // init models
            //$this->__models['predefined_model'] = new Predefined;

            // success response
            $this->_apiData['response'] = "success";
			// init output data array
            $this->_apiData['data'] = $data = array();
			
			$this->_apiData['message'] = "Success";

            if ($device_token != "" && $device_type != "") {
                $user->device_type = $device_type;
                $user->device_token = $device_token;
                // update user data
                $this->__models['user_model']->set($user->user_id, (array) $user);
            }
			

            // get user data
            $data['user'] = $this->__models['user_model']->getData($user->user_id);
			
			// message
			$this->_apiData['message'] = "Success";

            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__ApiResponse($request,$this->_apiData);
    }
	
	
	/**
     * Cupid Search
     *
     * @return Response
     */
    public function cupidSearch() {
        // get params
        $user_id = trim(strip_tags(Input::get('user_id', 0)));
        $page_no = (int) trim(strip_tags(Input::get('page_no', 0)));
		$latitude = trim(strip_tags(Input::get('latitude', 0)));
		$longitude = trim(strip_tags(Input::get('longitude', 0)));

        // get data
        $user = $this->__models['user_model']->get($user_id);

        if ($user === FALSE) {
            $this->_apiData['message'] = 'Invalid user Request';
        } elseif ($user->status == 0) {
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
        } elseif ($user->status > 1) {
            // kick user
            $this->_apiData['kick_user'] = 1;
            // message
            $this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
        } else {
            // init models
            //$this->__models['user_follow_model'] = new UserFollow;
			
			// success response
            $this->_apiData['response'] = "success";
            // init output data array
            $this->_apiData['data'] = $data = array();

			// vars
			$limit_records = 10;
			
			// search with current location
			if($latitude > 0 && $longitude > 0) {
				$user->latitude = $latitude;
				$user->longitude = $longitude;
			}
			
			// get search data
			$data = $this->_getCupidSearchData($user,$limit_records,$page_no);
			
			// message
			$this->_apiData['message'] = "Success";

            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__ApiResponse($request,$this->_apiData);
    }
	
	/**
     * Get Cupid Search Data
     * @param object $user
	 * @param integer $limit_records default : 5
	 * @param integer $page_no default : 1
     * @return Response
     */
    public function _getCupidSearchData($user, $limit_records = 5, $page_no = 1) {
        // get params
        
		$data = array();

		// set initial array for records
		$data["search_results"] = array();
		

		// find total pages
		$query = $this->__models['user_model']
			->select(array(
				"u.user_id",
				\DB::raw("calc_distance('".$user->latitude."','".$user->longitude."',latitude,longitude) AS distance")
			));
		$query = $this->_applyCupidFilters($query, $user);
		// apply 
		//$total_records = $query->count();
		$query->from("user AS u");
		//exit($query->toSql());
		$raw_records = $query->get();
		
		$total_records = isset($raw_records[0]) ? count($raw_records) : 0;
		
		
		// offfset / limits / valid pages
		$total_pages = ceil($total_records / $limit_records);
		$page_no = $page_no >= $total_pages ? $total_pages : $page_no;
		$page_no = $page_no <= 1 ? 1 : $page_no;
		$offset = $limit_records * ($page_no - 1);

		// query records
		$query = $this->__models['user_model']
					->select(array(
						"u.user_id",
						\DB::raw("calc_distance('".$user->latitude."','".$user->longitude."',latitude,longitude) AS distance"),
						\DB::raw("search_sort_priority(".$user->user_id.",u.user_id) AS searching_priority")
					));
		$query = $this->_applyCupidFilters($query, $user);
		$query->from("user AS u");
		$query->take($limit_records);
		$query->skip($offset);
		$query->orderBy("searching_priority", "DESC");
		$query->orderBy("distance", "ASC");
		$raw_records = $query->get();

		/* var_dump($total_records);
		  var_dump($query->toSql());
		  exit; */

		// set records
		if (isset($raw_records[0])) {
			foreach ($raw_records as $raw_record) {
				$app_user = $this->__models['user_model']->getData($raw_record->user_id, $user->user_id);
				$app_user->tracking_id = (string)intval(str_replace(array(" ","."),"",microtime()));
				$data["search_results"][] = $app_user;
			}
		}


		// set pagination response
		$data["page"] = array(
			"current" => $page_no,
			"total" => $total_pages,
			"next" => $page_no >= $total_pages ? 0 : $page_no + 1,
			"prev" => $page_no <= 1 ? 0 : $page_no - 1
		);

        return $data;
    }
	
	/**
     * Apply Cupid Filters (private)
     * @param query $query
	 * @param object $user
     * @return Response
     */
	private function _applyCupidFilters($query, $user) {
		// vars
		$age_range = explode("-",$user->interested_age_range);
		$last_searched_difference = 1; // ignored one day before
		
		// exclude self
		$query->where("u.user_id", "<>", $user->user_id);
		// looking for
		$query->where("u.looking_for", "=", $user->looking_for);
		// apply gender interest
		if($user->interested_gender != "both") {
			$query->where("u.gender",$user->interested_gender);
		}
		// age range
		//$query->where("u.age", ">=", $age_range[0]);
		//$query->where("u.age", "<=", $age_range[1]);
		$query->where(\DB::raw("TIMESTAMPDIFF(YEAR, u.dob, CURDATE())"), ">=", $age_range[0]);
		$query->where(\DB::raw("TIMESTAMPDIFF(YEAR, u.dob, CURDATE())"), "<=", $age_range[1]);
		
		// discoverables only
		$query->where("u.is_discoverable", "=", 1);
		// active ones
		$query->where("u.status", "=", 1);
		// distance
		$query->having("distance", "<=", $user->search_distance);
		// have ignored xx days ago i.e: last_searched_difference
		$query->whereRaw("COALESCE(DATEDIFF('".date("Y-m-d H:i:s")."', (
			SELECT created_at FROM user_view
			WHERE user_id = '".$user->user_id."'
			AND target_user_id = u.user_id
			AND view_action_id = (
				SELECT view_action_id FROM view_action
				WHERE `key` = 'pass'
			)
			AND deleted_at IS NULL
			ORDER BY created_at DESC
			LIMIT 1
		)), ".($last_searched_difference + 1).") > ".$last_searched_difference);
		// exclude liked/super_liked users
		$query->whereRaw("u.user_id NOT IN (
			SELECT target_user_id FROM user_view
			WHERE user_id = '".$user->user_id."'
			AND view_action_id IN (
				SELECT view_action_id FROM view_action
				WHERE `key` != 'pass'
			)
			AND deleted_at IS NULL
		)");
		// exclude matched ones
		$query->whereRaw("u.user_id NOT IN (
			SELECT IF(user_id = '".$user->user_id."',target_user_id,user_id) AS friend_user_id
			FROM friend
			WHERE ((`user_id` = '".$user->user_id."' AND `target_user_id` = u.user_id)
			AND (`user_id` =  u.user_id AND `target_user_id` ='".$user->user_id."'))
			AND deleted_at IS NULL
		)");
		// undeleted
		$query->whereNull("deleted_at");
		
		//exit($query->toSql());
		
		return $query;
	}
	
	/**
     * Logout
     *
     * @return Response
     */
    public function logout() {
        // get params
        $user_id = intval(trim(strip_tags(Input::get('user_id', 0))));

        // get data
        $user = $this->__models['user_model']->get($user_id);

        if ($user === FALSE) {
            $this->_apiData['message'] = "Invalid account request";
        } else {

            // success response
            $this->_apiData['response'] = "success";
            // init output data array
            $this->_apiData['data'] = $data = array();

            // set data
            $user->device_token = "";

            // update user data
            $this->__models['user_model']->set($user->user_id, (array) $user);

            // set message
            $this->_apiData['message'] = "Token successfully cleared";
            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__ApiResponse($request,$this->_apiData);
    }
	
	/**
     * Save view action
     *
     * @return Response
     */
    public function saveViewAction() {
		// init models
		$this->__models["view_action_model"] = new ViewAction;
		
        // get params
        $user_id = intval(trim(strip_tags(Input::get('user_id', 0))));
		$target_user_id = intval(trim(strip_tags(Input::get('target_user_id', 0))));
		$view_action = trim(strip_tags(Input::get('view_action', '')));
		$view_action = in_array($view_action, array("pass", "like", "super_like")) ? $view_action : "pass"; // set default value
		$tracking_id = intval(trim(strip_tags(Input::get('tracking_id', ""))));

        // get data
        $user = $this->__models['user_model']->get($user_id);
		$target_user = $this->__models['user_model']->get($target_user_id);
		$view_action_data = $this->__models["view_action_model"]->getBy("key",$view_action);
		$pass_action_data = $this->__models["view_action_model"]->getBy("key","pass");
		
		
		// check user super_like limit
		

        if ($user === FALSE) {
            $this->_apiData['message'] = "Invalid user request";
        } elseif($user->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($user->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} elseif ($target_user === FALSE) {
            $this->_apiData['message'] = "Invalid target user request";
        } elseif ($tracking_id == 0) {
            $this->_apiData['message'] = "Please provide Tracking ID";
        } else {
			// init models
			$this->__models["user_view_model"] = new UserView;

            // success response
            $this->_apiData['response'] = "success";
			
			// init output data array
            $this->_apiData['data'] = $data = array();
			
			// check if target user has liked/superliked
			$query = $this->__models["user_view_model"]
				->where("user_id",$target_user->user_id)
				->where("target_user_id",$user->user_id)
				->whereNull("deleted_at")
				->where("view_action_id","!=",$pass_action_data->view_action_id);
			$match_record_count = $query->count();
			
			// save
			$save["user_id"] = $user->user_id;
			$save["target_user_id"] = $target_user->user_id;
			$save["view_action_id"] = $view_action_data->view_action_id;
			$save["is_match"] = 0;
			$save["tracking_id"] = $tracking_id;
			// if view action is not ignore action, check for match
			/*var_dump($match_record_count);
			var_dump($view_action_data->{"key"});
			var_dump($pass_action_data->{"key"});
			exit;*/
			
			if($view_action_data->{"key"} != $pass_action_data->{"key"}) {
				$save["is_match"] = $match_record_count > 0 ? 1 : 0;
			}
			$save["created_at"] = date('Y-m-d H:i:s');
			$user_view_id = $this->__models["user_view_model"]->put($save);
			
			// put history : view_action
			$reference_data = array(
				"reference_module" => "view_action",
				"reference_id" => $view_action_data->view_action_id,
				"against" => "user",
				"against_id" => $target_user->user_id,
				"tracking_id" => $tracking_id,
			);
			$user_history_id = $this->__models['user_history_model']->putUserHistory($user->user_id,"view_action",$reference_data);
			
			/*
			// no need
			if($view_action_data->{"key"} != "pass") {
				$history_key = "got_".$view_action_data->{"key"};
				// put history : got_like/got_super_like
				$reference_data = array(
					"reference_module" => "user_view",
					"reference_id" => $user_view_id,
					"against" => "user",
					"against_id" => $user->user_id
				);
				$this->__models['user_history_model']->putUserHistory($target_user->user_id,$history_key,$reference_data);
				
			}*/
			
			// if match, put in matches (friend)
			if($save["is_match"] > 0) {
				// init model
				$this->__models["friend_model"] = new Friend;
				
				// save
				$friend["user_id"] = $user->user_id;
				$friend["target_user_id"] = $target_user->user_id;
				$friend["status"] = 1;
				$friend["tracking_id"] = $tracking_id;
				$friend["created_at"] = date('Y-m-d H:i:s');
				$friend_id = $this->__models["friend_model"]->put($friend);
				
				// put history : user_made_matcch
				$reference_data = array(
					"reference_module" => "friend",
					"reference_id" => $friend_id,
					"against" => "user",
					"against_id" => $target_user->user_id,
					"tracking_id" => $tracking_id
				);
				$match_history_id = $this->__models['user_history_model']->putUserHistory($user->user_id,"made_match",$reference_data);
			}

			// output target user data
			$data["target_user"] = $this->__models['user_model']->getData($target_user->user_id,$user->user_id);
			$data["user"] = $this->__models['user_model']->getData($user->user_id,$target_user->user_id);
			$data["is_match"] = $save["is_match"];
			$data["last_action"] = array(
				"user_view_id" => $user_view_id,
				"user_history_id" => $user_history_id,
				"match_history_id" => isset($match_history_id) ? $match_history_id : 0
			);
			
            // set message
            $this->_apiData['message'] = "Action successfully saved";
            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__ApiResponse($request,$this->_apiData);
    }
	
	/**
     * Revert view action
     *
     * @return Response
     */
    public function revertViewAction() {
		// init models
		$this->__models["user_view_model"] = new UserView;
		
        // get params
        $user_id = intval(trim(strip_tags(Input::get('user_id', 0))));
		$target_user_id = intval(trim(strip_tags(Input::get('target_user_id', 0))));
		$tracking_id = intval(trim(strip_tags(Input::get('tracking_id', ""))));

        // get data
        $user = $this->__models['user_model']->get($user_id);
		$target_user = $this->__models['user_model']->get($target_user_id);
		// check if target user has liked/superliked
		$query = $this->__models["user_view_model"]
			->select(array("user_view_id"))
			->where("user_id",$user_id)
			->where("target_user_id",$target_user_id)
			->where("tracking_id",$tracking_id)
			->whereNull("deleted_at");
		$query_records = $query->get();
		$query_count = isset($query_records[0]) ? 1 : 0;
		
        if ($user === FALSE) {
            $this->_apiData['message'] = "Invalid user request";
        } elseif($user->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($user->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} elseif ($target_user === FALSE) {
            $this->_apiData['message'] = "Invalid target user request";
        } elseif ($tracking_id == 0) {
            $this->_apiData['message'] = "Please provide Tracking ID";
        } elseif ($query_count == 0) {
            $this->_apiData['message'] = "Invalid Revert Action Request";
        } else {
			// init models
			$this->__models["user_view_model"] = new UserView;

            // success response
            $this->_apiData['response'] = "success";
			
			// init output data array
            $this->_apiData['data'] = $data = array();
			
			$user_view_id = $query_records[0]->user_view_id;
			$this->__models["user_view_model"]->revertAction($user_id, $target_user_id, $user_view_id, $tracking_id);
			
			
            // set message
            $this->_apiData['message'] = "Action successfully reverted";
            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__ApiResponse($request,$this->_apiData);
    }
	
	/**
     * Profile
     *
     * @return Response
     */
    public function profile() {
        // get params
        $user_id = trim(strip_tags(Input::get('user_id', 0)));
        $user_id = $user_id == "" ? 0 : $user_id; // set default value
		$target_user_id = intval(trim(strip_tags(Input::get('target_user_id', 0))));
        $device_type = trim(strip_tags(Input::get('device_type', '')));
        $device_type = in_array($device_type, array("android", "ios")) ? $device_type : "android"; // set default value
        $device_token = trim(strip_tags(Input::get('device_token', '')));

        // get data
        $user = $this->__models['user_model']->get($user_id);
		$target_user = $this->__models['user_model']->get($target_user_id);
		$profile = $this->__models['user_model']->getData($target_user->user_id,$user->user_id);
		

        if ($user_id == 0) {
            $this->_apiData['message'] = 'Please enter User ID';
        } else if ($user === FALSE) {
            $this->_apiData['message'] = 'Invalid user request';
        } else if ($target_user === FALSE) {
            $this->_apiData['message'] = 'Invalid profile user request';
        } else if ($target_user->deleted_at !== NULL) {
            $this->_apiData['message'] = 'Target profile removed';
        } elseif($user->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($user->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} else if ($profile->is_user_blocked > 0) {
            $this->_apiData['message'] = 'Sorry ! You cannot access this profile';
        } else {
            // init models
            //$this->__models['predefined_model'] = new Predefined;

            // success response
            $this->_apiData['response'] = "success";
			// init output data array
            $this->_apiData['data'] = $data = array();

            // get user data
            //$data['profile'] = $this->__models['user_model']->getData($target_user->user_id,$user->user_id);
			$data['profile'] = $profile;
			
			// message
			$this->_apiData['message'] = "Success";
			
            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__ApiResponse($request,$this->_apiData);
    }
	
	
	/**
     * Update Notification Settings
     *
     * @return Response
     */
    public function updateNotificationSettings() {
		// init models
		//$this->__models["preference_model"] = new Preference;
		
        // get params
        $user_id = intval(trim(strip_tags(Input::get('user_id', 0))));
		$keys = trim(strip_tags(Input::get('keys', "")));
		$switches = trim(strip_tags(Input::get('switches', '')));
		

        // get data
        $user = $this->__models['user_model']->get($user_id);
		

        if ($user === FALSE) {
            $this->_apiData['message'] = "Invalid user request";
        } elseif($user !== FALSE && $user->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($user !== FALSE && $user->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} else if ($keys == "") {
            $this->_apiData['message'] = "Please provide keys";
        } else if ($switches == "") {
            $this->_apiData['message'] = "Please provide switches values";
        } else {
			// init models
			$this->__models["preference_model"] = new Preference;
			$this->__models["user_preference_model"] = new UserPreference;

            // success response
            $this->_apiData['response'] = "success";
			// init output data array
            $this->_apiData['data'] = $data = array();
			
			$keys_data = explode(",",$keys);
			$switches_data = explode(",",$switches);
			
			if(isset($keys_data[0])) {
				$i=0; // itrator for other data
				foreach($keys_data as $key) {
					$key_data = $this->__models["preference_model"]->getBy("key",$key);
					// if valid key
					if($key_data !== FALSE) {
						// get user preference record
						$raw_existance = $this->__models['user_preference_model']
							->where('preference_id', '=', $key_data->preference_id)
							->where('user_id', '=', $user->user_id)
							->get(array("user_preference_id"));
						$record_id = isset($raw_existance[0]) ? $raw_existance[0]->user_preference_id : 0;
						$record = $this->__models['user_preference_model']->get($record_id);
						// if record exists
						if($record !== FALSE) {
							$key_value = isset($switches_data[$i]) ? intval($switches_data[$i]) : 1;
							$record->value = $key_value;
							// update
							$this->__models['user_preference_model']->set($record_id, (array)$record);
						}
					}
					// increment itrator
					$i++;
				}
			}
			
			// output data
			$data["user"] = $this->__models['user_model']->getData($user->user_id);
			
            // set message
            $this->_apiData['message'] = "Settings successfully updated";
            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__ApiResponse($request,$this->_apiData);
    }


	/**
     * Update Discovery Setting
     *
     * @return Response
     */
    public function updateDiscoverySettings() {
		// init models
		//$this->__models["preference_model"] = new Preference;
		
        // get params
        $user_id = intval(trim(strip_tags(Input::get('user_id', 0))));
		$is_discoverable = intval(trim(strip_tags(Input::get('is_discoverable', 1))));
		$is_discoverable = $is_discoverable > 1 ? 1 : $is_discoverable; // default value
		$location = trim(strip_tags(Input::get('location', '')));
		$latitude = trim(strip_tags(Input::get('latitude', 0)));
        $latitude = $latitude == "" ? 0 : $latitude; // default value
		$longitude = trim(strip_tags(Input::get('longitude', 0)));
        $longitude = $longitude == "" ? 0 : $longitude; // set default value
		$interested_gender = trim(strip_tags(Input::get('interested_gender', '')));
		$interested_gender = in_array($interested_gender, array("male", "female","both")) ? $interested_gender : ""; // default value
		$looking_for = trim(strip_tags(Input::get('looking_for', '')));
		$looking_for = in_array($looking_for, array("friendship", "dating")) ? $looking_for : ""; // default value
		$distance = intval(trim(strip_tags(Input::get('distance', 0))));
		$interested_age_range = trim(strip_tags(Input::get('interested_age_range', '')));
		$use_current_location = intval(trim(strip_tags(Input::get('use_current_location', 0))));
		$use_current_location = $use_current_location > 1 ? 1 : $use_current_location; // default value
		$change_discoverable_only = intval(trim(strip_tags(Input::get('change_discoverable_only', 0))));
		$change_discoverable_only = $change_discoverable_only > 1 ? 1 : $change_discoverable_only; // default value
		
		//$web_url = trim(strip_tags(Input::get('web_url', '')));
		//$web_username = trim(strip_tags(Input::get('web_username', '')));
		
        // get data
        $user = $this->__models['user_model']->get($user_id);
		/*$raw_existance = $this->__models['user_model']
			->where('web_username', '=', $web_username)
			->get(array("user_id"));
		$record_id = isset($raw_existance[0]) ? $raw_existance[0]->user_id : 0;
		$valid_web_username = TRUE;
		if($user->web_username != $web_username) {
			$valid_web_username = $record_id == 0 ? TRUE : FALSE;
		}*/

        if ($user === FALSE) {
            $this->_apiData['message'] = "Invalid user request";
        } elseif($user->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($user->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} /*else if ($change_discoverable_only < 1 && $location == "") {
            $this->_apiData['message'] = "Please provide Location";
        }*/ else if ($change_discoverable_only < 1 && $latitude == 0) {
            $this->_apiData['message'] = "Please provide Latitude";
        } else if ($change_discoverable_only < 1 && $longitude == 0) {
            $this->_apiData['message'] = "Please provide Longitude";
        } else if ($change_discoverable_only < 1 && $distance < 1) {
            $this->_apiData['message'] = "Please provide valid distance";
        } else if ($change_discoverable_only < 1 && $interested_age_range == "") {
            $this->_apiData['message'] = "Please age range";
        } /*else if ($web_username != "" && !$valid_web_username) {
            $this->_apiData['message'] = "Username already exists. Please try another";
        }*/ else {
			// init models
			//$this->__models["preference_model"] = new Preference;
			
			// success response
            $this->_apiData['response'] = "success";
			// init output data array
            $this->_apiData['data'] = $data = array();
			
			// update
			$user->is_discoverable = $is_discoverable;
			
			
			if($change_discoverable_only < 1) {
				$user->location = $location;
				$user->latitude = $latitude;
				$user->longitude = $longitude;
				$user->looking_for = $looking_for == "" ? $user->looking_for : $looking_for;
				$user->interested_gender = $interested_gender == "" ? $user->interested_gender : $interested_gender;
				$user->search_distance = $distance;
				$user->interested_age_range = $interested_age_range;
				$user->use_current_location = $use_current_location;
			}
			//$user->web_url = $web_url == "" ? $user->web_url : $web_url;
			//$user->web_username = $web_username == "" ? $user->web_username : $web_username;
			// update data
			$this->__models['user_model']->set($user->user_id, (array)$user);
			
			// output data
			$data["user"] = $this->__models['user_model']->getData($user->user_id);
			
			// get data from cupid search
			if($change_discoverable_only < 1) {
				$data["search_data"] = $this->_getCupidSearchData($user,5);
			}
			
            // set message
            $this->_apiData['message'] = "Settings successfully updated";
            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__ApiResponse($request,$this->_apiData);
    }
	
	
	/**
     * Set web username
     *
     * @return Response
     */
    public function setWebUsername() {
		// init models
		//$this->__models["preference_model"] = new Preference;
		
        // get params
        $user_id = intval(trim(strip_tags(Input::get('user_id', 0))));
		$web_username = trim(strip_tags(Input::get('web_username', '')));
		$web_username = str_slug($web_username); // slugify
		

        // get data
        $user = $this->__models['user_model']->get($user_id);
		$raw_existance = $this->__models['user_model']
			->where('web_username', '=', $web_username)
			->get(array("user_id"));
		$record_id = isset($raw_existance[0]) ? $raw_existance[0]->user_id : 0;
		$valid_web_username = TRUE;
		if($user->web_username != $web_username) {
			$valid_web_username = $record_id == 0 ? TRUE : FALSE;
		}

        if ($user === FALSE) {
            $this->_apiData['message'] = "Invalid user request";
        } elseif($user !== FALSE && $user->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($user !== FALSE && $user->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} else if ($web_username != "" && !$valid_web_username) {
            $this->_apiData['message'] = "Username already exists. Please try another";
        } else {
			// init models
			//$this->__models["preference_model"] = new Preference;
			
			// success response
            $this->_apiData['response'] = "success";
			// init output data array
            $this->_apiData['data'] = $data = array();
			
			// update
			$user->web_username = $web_username == "" ? $user->web_username : $web_username;
			$user->web_url = $user->web_username == "" ? "" : "[WEB_URL]/@".$user->web_username;
			// update data
			$this->__models['user_model']->set($user->user_id, (array)$user);
			
			// output data
			$data["user"] = $this->__models['user_model']->getData($user->user_id);
			
			// set message
            $this->_apiData['message'] = "Username successfully updated";
            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__ApiResponse($request,$this->_apiData);
    }
	
	
	/**
     * Update Profile
     *
     * @return Response
     */
    public function updateProfile() {
        // get params
		$user_id = intval(trim(strip_tags(Input::get('user_id', 0))));
        $gender = trim(strip_tags(Input::get('gender', '')));
		$gender = in_array($gender, array("male", "female")) ? $gender : ""; // default value
		$about_me = trim(strip_tags(Input::get('about_me', '')));
		//$looking_for = trim(strip_tags(Input::get('looking_for', '')));
		//$looking_for = in_array($looking_for, array("friendship", "dating")) ? $looking_for : ""; // default value
		$works = trim(strip_tags(Input::get('works', '')));
		$selected_works = trim(strip_tags(Input::get('selected_works', '')));
		$educations = trim(strip_tags(Input::get('educations', '')));
		$selected_educations = trim(strip_tags(Input::get('selected_educations', '')));
		$disabilities_ids = trim(strip_tags(Input::get('disabilities_ids', '')));
		$show_disability = intval(trim(strip_tags(Input::get('show_disability', 0))));
		$show_disability = $show_disability > 1 ? 1 : $show_disability; // default value
		$dob = trim(strip_tags(Input::get('dob', '')));
		$interest_raw_data = trim(strip_tags(Input::get('interest_raw_data', '')));
		$get_general_data = intval(trim(strip_tags(Input::get('get_general_data', 0))));
		$get_general_data = $get_general_data > 1 ? 1 : $get_general_data; // default value
		$update_disability = intval(trim(strip_tags(Input::get('update_disability', 0))));
		$update_disability = $update_disability > 1 ? 1 : $update_disability; // default value
		
		
		// get user data
		$user = $this->__models['user_model']->get($user_id);		
		
        // validations
        if ($user === FALSE) {
            $this->_apiData['message'] = 'Invalid user request';
        } /*else if ($disabilities_ids == '') {
            $this->_apiData['message'] = 'Please provide disabilities';
        }*/ elseif($user !== FALSE && $user->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($user !== FALSE && $user->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} else if ($works != "" && $selected_works == "") {
            $this->_apiData['message'] = "Please provide selected works";
        } else if ($educations != "" && $selected_educations == "") {
            $this->_apiData['message'] = "Please provide selected educations";
        } else {
			// init models
			$this->__models['user_disability_model'] = new UserDisability;
			$this->__models['user_background_model'] = new UserBackground;
			
			// success response
            $this->_apiData['response'] = "success";
            // init output data array
            $this->_apiData['data'] = $data = array();

            // update last login as current timestamp
            $user->about_me = $about_me;
			//$user->looking_for = $looking_for == "" ? $user->looking_for : $looking_for;
			$user->gender = $gender == "" ? $user->gender : $gender;
			$user->show_disability = $show_disability;
			$user->updated_at = date('Y-m-d H:i:s');
			if($dob != "") {
				$dob = date("Y-m-d",strtotime($dob));
				$age = $this->__models['user_model']->getAge($dob);
				$user->dob = $dob;
				$user->age = $age;
			}
			if($interest_raw_data != "") {
				$user->interest_raw_data = $interest_raw_data;
			}
			
			
			// update user data
            $this->__models['user_model']->set($user->user_id, (array) $user);
			
			// other updates
			// - work
			if($works != "") {
				// flush old
				$this->__models['user_background_model']->removeUserBackground($user_id,"work");
				// save user works (background)
				$this->__models['user_background_model']->putUserBackground($user_id, "work", $works, $selected_works);
			}
			// - education
			if($educations != "") {
				// flush old
				$this->__models['user_background_model']->removeUserBackground($user_id,"education");
				// save user works (background)
				$this->__models['user_background_model']->putUserBackground($user_id, "education", $educations, $selected_educations);
			}
			// - disabilities
			if($update_disability == 1) {
				// flush old
				$this->__models['user_disability_model']->removeUserDisabilities($user_id);
				if($disabilities_ids != "") {
					// flush old
					//$this->__models['user_disability_model']->removeUserDisabilities($user_id);
					// save user works (background)
					$this->__models['user_disability_model']->putUserDisabilities($user_id,$disabilities_ids);
				}
			}

            // response data
            $data['user'] = $this->__models['user_model']->getData($user->user_id);
			
			// message
			$this->_apiData['message'] = "Successfully updated profile";
			
			// get extra data
			if($get_general_data > 0) {
				// init models
				$this->__models['disability_model'] = new Disability;
				$this->__models['package_model'] = new Package;
				
				// disabilities
				$query = $this->__models['disability_model']->select(array("disability_id"));
				$query->orderBy("name", "ASC");
				$query->whereNull("deleted_at");
				$raw_records = $query->get();
				
				if(isset($raw_records[0])) {
					foreach($raw_records as $raw_record) {
						$record = $this->__models['disability_model']->getData($raw_record->disability_id);
						$data['diabilities'][] = $record;
					}
				}
				
				// packages
				$query = $this->__models['package_model']->select(array("package_id"));
				$query->orderBy("package_id", "ASC");
				$raw_records = $query->get();
				
				if(isset($raw_records[0])) {
					foreach($raw_records as $raw_record) {
						$record = $this->__models['package_model']->getData($raw_record->package_id);
						$data['packages'][] = $record;
					}
				}
				
			}
			

            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request,$this->_apiData);
    }
	
	
	/**
     * Update Social Info
     *
     * @return Response
     */
    public function updateSocialInfo() {
        // get params
		$user_id = intval(trim(strip_tags(Input::get('user_id', 0))));
        $network = trim(strip_tags(Input::get('network', '')));
		$network = in_array($network, array("instagram", "facebook", "twitter")) ? $network : "instagram"; // default value
		$uid = trim(strip_tags(Input::get('uid', '')));
		$access_token = trim(strip_tags(Input::get('access_token', '')));
		$profile_url = trim(strip_tags(Input::get('profile_url', '')));
		
		// get user data
		$user = $this->__models['user_model']->get($user_id);		
		
        // validations
        if ($user === FALSE) {
            $this->_apiData['message'] = 'Invalid user request';
        } else if ($uid == '') {
            $this->_apiData['message'] = 'Please provide UID';
        }/* else if ($access_token == '') {
            $this->_apiData['message'] = 'Please Access Token';
        }*/ elseif($user !== FALSE && $user->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($user !== FALSE && $user->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} else {
			// init models
			$this->__models['user_social_info_model'] = new UserSocialInfo;
			
			// success response
            $this->_apiData['response'] = "success";
            // init output data array
            $this->_apiData['data'] = $data = array();
			
			// check existance
			$row_type_exists = $this->__models['user_social_info_model']
				->where('user_id', '=', $user_id)
				->where('network', '=', $network)
				->get(array("user_social_info_id"));
			
			$exists_id = isset($row_type_exists[0]) ? $row_type_exists[0]->user_social_info_id : 0;
			
			$user_social_info = $this->__models['user_social_info_model']->get($exists_id);
			
			// prepare array shape
			$user_social_info = $user_social_info !== FALSE ? (array)$user_social_info : array();
			
			
			$user_social_info["user_id"] = $user_id;
			$user_social_info["network"] = $network;
			$user_social_info["uid"] = $uid;
			$user_social_info["access_token"] = $access_token;
			$user_social_info["profile_url"] = $profile_url;
			
			if(isset($user_social_info["user_social_info_id"])) {
				$user_social_info["updated_at"] = date('Y-m-d H:i:s');
				$user_social_info["deleted_at"] = NULL;
				// update
				$this->__models['user_social_info_model']->set($user_social_info["user_social_info_id"], (array)$user_social_info);
			} else {
				$user_social_info["created_at"] = date('Y-m-d H:i:s');
				// insert
				$this->__models['user_social_info_model']->put($user_social_info);
			}

            // response data
            $data['user'] = $this->__models['user_model']->getData($user->user_id);
			
			// message
			$this->_apiData['message'] = 'Successfully updated info';

            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request,$this->_apiData);
    }
	
	
	/**
     * Upgrade
     *
     * @return Response
     */
    public function upgrade() {
		// init models
		$this->__models["package_model"] = new Package;
		
        // get params
		$user_id = intval(trim(strip_tags(Input::get('user_id', 0))));
        $package_id = intval(trim(strip_tags(Input::get('package_id', ''))));
		
		
		// get user data
		$user = $this->__models['user_model']->get($user_id);
		$user_data = $this->__models['user_model']->getData($user_id);
		$package = $this->__models['package_model']->get($package_id);
		
        // validations
        if ($user === FALSE) {
            $this->_apiData['message'] = 'Invalid user request';
        } else if ($package === FALSE) {
            $this->_apiData['message'] = 'Invalid package request';
        } else if ($package->package_id == 1) {
            $this->_apiData['message'] = 'The free package is by default for every user';
        } else if ($user_data->user_package["package_id"] == $package->package_id) {
            $this->_apiData['message'] = 'You are already subscribed to this package';
        } elseif($user !== FALSE && $user->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($user !== FALSE && $user->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} else {
			// init models
			$this->__models['user_package_model'] = new UserPackage;
			
			// success response
            $this->_apiData['response'] = "success";
            // init output data array
            $this->_apiData['data'] = $data = array();
			
			// insert
			$this->__models['user_package_model']->putUserPackage($user->user_id, $package->{"key"});
			
            // response data
            $data['user'] = $this->__models['user_model']->getData($user->user_id);
			
			// message
			$this->_apiData['message'] = "User successfully upgraded";

            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request,$this->_apiData);
    }
	
	
	/**
     * Block
     *
     * @return Response
     */
    public function block() {
        // get params
        $user_id = trim(strip_tags(Input::get('user_id', 0)));
        $user_id = $user_id == "" ? 0 : $user_id; // set default value
		$target_user_id = intval(trim(strip_tags(Input::get('target_user_id', 0))));

        // get data
        $user = $this->__models['user_model']->get($user_id);
		$target_user = $this->__models['user_model']->get($target_user_id);
		$user_data = $this->__models['user_model']->getData($user->user_id,$target_user->user_id);
		$target_user_data = $this->__models['user_model']->getData($target_user->user_id,$user->user_id);

        if ($user_id == 0) {
            $this->_apiData['message'] = 'Please enter User ID';
        } else if ($user === FALSE) {
            $this->_apiData['message'] = 'Invalid user request';
        } else if ($target_user === FALSE) {
            $this->_apiData['message'] = 'Invalid profile user request';
        } elseif($user !== FALSE && $user->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($user !== FALSE && $user->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} else if ($user_data->is_user_blocked > 0) {
            $this->_apiData['message'] = 'You have already blocked this user';
        } else if ($target_user_data->is_user_blocked > 0) {
            $this->_apiData['message'] = 'User has already blocked you';
        } else {
            // init models
            $this->__models['user_block_model'] = new UserBlock;

            // success response
            $this->_apiData['response'] = "success";
			
            // init output data array
            $this->_apiData['data'] = $data = array();
			
			// save
			$save["user_id"] = $user->user_id;
			$save["target_user_id"] = $target_user->user_id;
			$save["created_at"] = date('Y-m-d H:i:s');
			$this->__models['user_block_model']->put($save);

			// message
			$this->_apiData['message'] = "User successfully blocked";

            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__ApiResponse($request,$this->_apiData);
    }
	
	
	/**
     * Save Facebook friends
     *
     * @return Response
     */
    public function saveFbFriends() {
        // get params
		$user_id = intval(trim(strip_tags(Input::get('user_id', 0))));
		$uids = trim(strip_tags(Input::get('uids', '')));
		
		// get user data
		$user = $this->__models['user_model']->get($user_id);
		
		// validations
        if ($user === FALSE) {
            $this->_apiData['message'] = 'Invalid user request';
        } else if ($uids == '') {
            $this->_apiData['message'] = 'Please provide uids';
        }  elseif($user !== FALSE && $user->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($user !== FALSE && $user->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} else {
			// init models
			$this->__models['user_social_friend_model'] = new UserSocialFriend;
			
			
			// success response
            $this->_apiData['response'] = "success";
            // init output data array
            $this->_apiData['data'] = $data = array();
			
			// get user friends
			//$user_social_friends = $this->__models['user_social_friend_model']->getFriends($user_id,"facebook");
			
			// if user dont have photo, then save - otherwise ignore
			// let mobile developer take care of this
			//if(isset($user_social_friends[0])) {
				//$uids_data = preg_match("@,@",$uids) ? explode(",",$uids) : array();
				$uids_data = explode(",",$uids);
				
				if(isset($uids_data[0])) {
					// flush old
					$this->__models['user_social_friend_model']->removeFriends($user_id, "facebook");
					
					// save user photos (background)
					$this->__models['user_social_friend_model']->putFriends($user_id,"facebook", $uids);
				}
			//}
			
			

            // response data
            $data['user'] = $this->__models['user_model']->getData($user->user_id);
			
			// message
			$this->_apiData['message'] = "Success";

            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request,$this->_apiData);
    }
	
	
	/**
     * Delete user
     *
     * @return Response
     */
    public function delete() {
        // get params
		$user_id = intval(trim(strip_tags(Input::get('user_id', 0))));
		
		// get user data
		$user = $this->__models['user_model']->get($user_id);
		
		// validations
        if ($user === FALSE) {
            $this->_apiData['message'] = 'Invalid user request';
        } elseif($user !== FALSE && $user->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($user !== FALSE && $user->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} else {
			// init models
			$this->__models['user_social_friend_model'] = new UserSocialFriend;
			
			
			// success response
            $this->_apiData['response'] = "success";
            // init output data array
            $this->_apiData['data'] = $data = array();
			
			// put deleted date
			//$user->deleted_at = date("Y-m-d H:i:s");
			//$this->__models['user_model']->set($user->user_id,(array)$user);
			// remove user account and related tasks
			$this->__models['user_model']->remove($user->user_id);

            // response data
            $data['user'] = $this->__models['user_model']->getData($user->user_id);
			
			// message
			$this->_apiData['message'] = "Success";

            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request,$this->_apiData);
    }
	
	
	/**
     * Report
     *
     * @return Response
     */
    public function report() {
		// init models
		$this->__models['user_report_model'] = new UserReport;
		
		// get params
        $user_id = trim(strip_tags(Input::get('user_id', 0)));
        $user_id = $user_id == "" ? 0 : $user_id; // set default value
		$target_user_id = intval(trim(strip_tags(Input::get('target_user_id', 0))));

        // get data
        $user = $this->__models['user_model']->get($user_id);
		$target_user = $this->__models['user_model']->get($target_user_id);
		$check = $this->__models['user_report_model']->check($user_id, $target_user_id, 1);
		
        if ($user_id == 0) {
            $this->_apiData['message'] = 'Please enter User ID';
        } else if ($user === FALSE) {
            $this->_apiData['message'] = 'Invalid user request';
        } else if ($target_user === FALSE) {
            $this->_apiData['message'] = 'Invalid profile user request';
        } elseif($user->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($user->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} else if ($check > 0) {
            $this->_apiData['message'] = 'You have already reported this user. Please wait for administrator\'s review';
        } else {
            // success response
            $this->_apiData['response'] = "success";
			
            // init output data array
            $this->_apiData['data'] = $data = array();
			
			// save
			$save["user_id"] = $user->user_id;
			$save["target_user_id"] = $target_user->user_id;
			$save["status"] = 1;
			$save["created_at"] = date('Y-m-d H:i:s');
			$this->__models['user_report_model']->put($save);

			// message
			$this->_apiData['message'] = "User successfully reported, we will soon review and take take appropriate action";

            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__ApiResponse($request,$this->_apiData);
    }
	
	
	/**
     * removePackage
     *
     * @return Response
     */
    public function removePackage() {
		// init models
		$this->__models["package_model"] = new Package;
		$this->__models['user_package_model'] = new UserPackage;
		
        // get params
		$user_id = intval(trim(strip_tags(Input::get('user_id', 0))));
        $package_id = intval(trim(strip_tags(Input::get('package_id', ''))));
		
		
		// get user data
		$user = $this->__models['user_model']->get($user_id);
		$user_data = $this->__models['user_model']->getData($user_id);
		$package = $this->__models['package_model']->get($package_id);
		$record_id = $this->__models['user_package_model']->check($user_id,$package_id, 0);
		
        // validations
        if ($user === FALSE) {
            $this->_apiData['message'] = 'Invalid user request';
        } else if ($package === FALSE) {
            $this->_apiData['message'] = 'Invalid package request';
        } else if ($package->package_id == 1) {
            $this->_apiData['message'] = 'Cannot remove free package';
        } else if ($record_id == 0) {
            $this->_apiData['message'] = 'No record found for this package';
        } elseif($user->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($user->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} else {
			// init models
			//$this->__models['user_package_model'] = new UserPackage;
			
			// success response
            $this->_apiData['response'] = "success";
            // init output data array
            $this->_apiData['data'] = $data = array();
			
			// update
			$user_package = $this->__models['user_package_model']->get($record_id);
			
			
			$this->__models['user_package_model']->removePackage($user->user_id, $package->package_id);
			/*if($user_package !== FALSE) {
				$user_package->is_expired = 1;
				$user_package->deleted_at = date("Y-m-d H:i:s");
				$user_package->valid_until = date("Y-m-d H:i:s");
				$this->__models['user_package_model']->set($user_package->user_package_id,(array)$user_package);
			}*/
			
            // response data
            $data['user'] = $this->__models['user_model']->getData($user->user_id);
			
			// message
			$this->_apiData['message'] = "User package successfully removed";

            // assign to output
            $this->_apiData['data'] = $data;
        }

        return $this->__ApiResponse($request,$this->_apiData);
    }
	
	
	/**
     * Randomize
     *
     * @return Response
     */
    public function randomize() {
		// param validations
		$validator = Validator::make(Input::all(), array(
			'user_id' => 'required|exists:user,user_id'
		));
		
		// get data
        $user = $this->__models['user_model']->get(Input::get('user_id', 0));
		
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else if (preg_match("@temp_@",$user->email)) {
            $this->_apiData['message'] = "already randomized";
        } else if($user->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($user->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} else {
            // init models
            //$this->__models['predefined_model'] = new Predefined;

            // success response
            $this->_apiData['response'] = "success";
			// init output data array
            $this->_apiData['data'] = $data = array();
			
			$this->_apiData['message'] = "Success";

			$user->name = $user->name. " ".time();
			$user->email = str_replace("@","temp_".time()."@",$user->email);
			$user->uid = $user->uid."-".time();
			// update user data
			$this->__models['user_model']->set($user->user_id, (array)$user);
			

            // get user data
            $data['user'] = $this->__models['user_model']->getData($user->user_id);
			
			// message
			$this->_apiData['message'] = "Success";

            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__ApiResponse($request,$this->_apiData);
    }
	
	
	/**
     * Remove web username
     *
     * @return Response
     */
    public function removeWebUsername() {
		
        // get params
        $user_id = intval(trim(strip_tags(Input::get('user_id', 0))));
		
        // get data
        $user = $this->__models['user_model']->get($user_id);
		
		if ($user === FALSE) {
            $this->_apiData['message'] = "Invalid user request";
        } elseif($user !== FALSE && $user->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} elseif($user !== FALSE && $user->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		} else {
			// init models
			//$this->__models["preference_model"] = new Preference;
			
			// success response
            $this->_apiData['response'] = "success";
			// init output data array
            $this->_apiData['data'] = $data = array();
			
			// update
			$user->web_username ="";
			$user->web_url = "";
			// update data
			$this->__models['user_model']->set($user->user_id, (array)$user);
			
			// output data
			$data["user"] = $this->__models['user_model']->getData($user->user_id);
			
			// set message
            $this->_apiData['message'] = "Username successfully removed";
            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__ApiResponse($request,$this->_apiData);
    }
	
	
	/**
     * Update Device info
     *
     * @return Response
     */
    public function updateDeviceInfo() {
		// trim/escape all
		Input::merge(array_map('strip_tags', Input::all()));
		Input::merge(array_map('trim', Input::all()));
		
        // get params
        $user_id = intval(trim(strip_tags(Input::get('user_id', 0))));
		$type = trim(strip_tags(Input::get('type', '')));
        /*$type = in_array($type, array("android", "ios")) ? $type : "android"; // set default value
		$name = trim(strip_tags(Input::get("name", "")));
		$model = trim(strip_tags(Input::get("model", "")));
		$os = trim(strip_tags(Input::get("os", "")));
		$os_version = trim(strip_tags(Input::get("os_version", "")));
		$app_version = trim(strip_tags(Input::get("app_version", "")));*/
		$build_id = trim(strip_tags(Input::get("build_id", "")));
		
		// param validations
		$validator = Validator::make(Input::all(), array(
			'user_id' => 'required|exists:user,user_id',
			'type' => 'required|in:android,ios',
			'brand' => 'required',
			'model' => 'required',
			'manufacturer' => 'required',
			'os_version' => 'required',
			'api_level' => 'required',
			//'build_id' => 'required', // only for android
			'app_version' => 'required'
		));
		
        // get data
        $user = $this->__models['user_model']->get($user_id);
		
		if ($user === FALSE) {
            $this->_apiData['message'] = "Invalid user request";
        } /*else if($user !== FALSE && $user->status == 0){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is inactive. Please check your activation email sent on registration.';
		} else if($user !== FALSE && $user->status > 1){
			// kick user
			$this->_apiData['kick_user'] = 1;
			// message
			$this->_apiData['message'] = 'Your account is either removed or banned by Administrator. Please contact Admin for details.';
		}*/ else if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else if ($type == "android" && $build_id == "") {
            $this->_apiData['message'] = "Please provide Build ID";
        } else {
			// init models
			$this->__models["user_device_model"] = new UserDevice;
			
			// success response
            $this->_apiData['response'] = "success";
			// init output data array
            $this->_apiData['data'] = $data = array();
			
			// get user device data
			$user_device = $this->__models["user_device_model"]->getBy("user_id",$user_id);
			$save = $user_device ? (array)$user_device : array();
			// set data
			$save["user_id"] = Input::get('user_id', 0);
			$save["type"] = Input::get('type', "");
			$save["brand"] = Input::get('brand', "");
			$save["model"] = Input::get('model', "");
			$save["manufacturer"] = Input::get('manufacturer', "");
			$save["os_version"] = Input::get('os_version', "");
			$save["api_level"] = Input::get('api_level', "");
			$save["build_id"] = Input::get('build_id', "");
			$save["app_version"] = Input::get('app_version', "");
			// insert / update
			if($user_device) {
				$save["updated_at"] = date("Y-m-d H:i:s");
				$this->__models["user_device_model"]->set($save["user_device_id"], $save);
			} else {
				$save["created_at"] = date("Y-m-d H:i:s");
				$this->__models["user_device_model"]->insert($save);
			}
			
			
			// set message
            $this->_apiData['message'] = "Device info successfully updated";
            // assign to output
            $this->_apiData['data'] = $data;
        }


        return $this->__ApiResponse($request,$this->_apiData);
    }
	
	

}
