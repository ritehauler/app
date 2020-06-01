<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use View;
use Session;
use Validator;
use Image;
// load models
use App\Http\Models\ApiMethod;
use App\Http\Models\ApiMethodField;
use App\Http\Models\ApiUser;
use App\Http\Models\User;

class PostController extends Controller {

    private $_assignData = array(
        'pDir' => '',
        'dir' => DIR_API
    );
    private $_apiData = array();
    private $_layout = "";
    private $_models = array();
    private $_jsonData = array();
	private $_model_path = "\App\Http\Models\\";
	/*private $_entity_identifier = "{wildcard_identifier}";
	private $_entity_pk = "{wildcard_pk}";
	private $_entity_ucfirst = "{wildcard_ucfirst}";
	*/
	private $_entity_identifier = "post";
	private $_entity_pk = "post_id";
	private $_entity_ucfirst = "Post";
	private $_entity_model;
	

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request) {
		// load entity model
		$this->_entity_model = $this->_model_path.$this->_entity_ucfirst;
		$this->_entity_model = new $this->_entity_model;		
        // init models
        $this->__models['api_method_model'] = new ApiMethod;
        $this->__models['api_user_model'] = new ApiUser;
		$this->__models['user_model'] = new User;
		$this->__models[$this->_entity_identifier.'_model'] = $this->_entity_model;

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
     * AddComment
     *
     * @return Response
     */
    public function addPost(Request $request) {		
		// load models
		$post_model = $this->_model_path."Post";
		$post_model = new $post_model;
		$user_log_model = $this->_model_path."UserLog";
		$user_log_model = new $user_log_model;		
		
		// trim/escape all
		$request->merge(array_map('strip_tags', $request->all()));
		$request->merge(array_map('trim', $request->all()));		
		$request->content_type = in_array($request->content_type, array("audio", "file", "image", "text", "video")) ? $request->content_type : "text";
		// get user data
		$user = $this->__models['user_model']->get($request->user_id);		
		// validations
        if ($request->user_id == '') {
            $this->_apiData['message'] = 'Please enter user_id';
        } else if ($request->title == '') {
            $this->_apiData['message'] = 'Please enter title';
        } else if ($request->content_type == '') {
            $this->_apiData['message'] = 'Please enter content_type';
        } else if ($request->{"content"} == '') {
            $this->_apiData['message'] = 'Please enter content';		
        } else if($user === FALSE){
			$this->_apiData['message'] = 'Invalid Creator ID Request';
		}else{
        // success response
        $this->_apiData['response'] = "success";	

		$save['user_id'] = $request->user_id;
		$save['content_type'] = $request->content_type;
		$save['title'] = $request->title;
		$save['content'] = $request->{"content"};
		$save['location'] = $request->location;
		$save['latitude'] = $request->latitude;
		$save['longitude'] = $request->longitude;
		
		// Insert Post data
		$insert_id = $post_model->put($save);
		// set for log
		/*$data['activity_type'] = 'post_add';
		$data['user_id'] = $save['user_id'];			
		$user_log_model->put($data);*/

		// get post data
		$post = $post_model->get($insert_id);
		// message
		$this->_apiData['message'] = "Add Post";

		$data['post'] = $post;
		// assign to output
		$this->_apiData['data'] = $data;
        }    
        return $this->__ApiResponse($request,$this->_apiData);
    }
	
	
	/**
     * UpdateComment
     *
     * @return Response
     */
    public function updatePost(Request $request) {		
		// load models
		$post_model = $this->_model_path."Post";
		$post_model = new $post_model;
		$user_log_model = $this->_model_path."UserLog";
		$user_log_model = new $user_log_model;		
		// trim/escape all
		$request->merge(array_map('strip_tags', $request->all()));
		$request->merge(array_map('trim', $request->all()));
		// get user data
		$user = $this->__models['user_model']->get($request->user_id);
		// get post data
		$post = $post_model->get($request->post_id);			
		// validations
		if ($request->post_id == '') {
            $this->_apiData['message'] = 'Please enter post_id';
        } else if ($request->user_id == '') {
            $this->_apiData['message'] = 'Please enter user_id';
        } else if ($request->title == '') {
            $this->_apiData['message'] = 'Please enter title';
        } else if ($request->content_type == '') {
            $this->_apiData['message'] = 'Please enter content_type';
        } else if ($request->{"content"} == '') {
            $this->_apiData['message'] = 'Please enter content';		
        } else if($user === FALSE){
			$this->_apiData['message'] = 'Invalid Creator ID Request';
		} else if($post === FALSE){
			$this->_apiData['message'] = 'Invalid Post ID Request';
		} else{
        // success response
        $this->_apiData['response'] = "success";	

		$save['user_id'] = $request->user_id;
		$save['content_type'] = $request->content_type;
		$save['title'] = $request->title;
		$save['content'] = $request->{"content"};
		$save['location'] = $request->location;
		$save['latitude'] = $request->latitude;
		$save['longitude'] = $request->longitude;
		$save['updated_at'] = date('Y-m-d H:i:s');		
		// Update Post data
		$post_model->set($request->post_id, (array) $save);
		// get post data
		$post = $post_model->get($request->post_id);
		// message
		$this->_apiData['message'] = "Update Post";

		$data['post'] = $post;
		// assign to output
		$this->_apiData['data'] = $data;
        }    
        return $this->__ApiResponse($request,$this->_apiData);	
    }

	/**
     * DeletePost
     *
     * @return Response
     */
    public function deletePost(Request $request) {		
		// load models
		$post_model = $this->_model_path."Post";
		$post_model = new $post_model;
		$user_log_model = $this->_model_path."UserLog";
		$user_log_model = new $user_log_model;		
		
		// trim/escape all
		$request->merge(array_map('strip_tags', $request->all()));
		$request->merge(array_map('trim', $request->all()));			
		// get post data
		$post = $post_model->get($request->post_id);		
		// validations
        if ($request->post_id == '') {
            $this->_apiData['message'] = 'Please enter post_id';
        } else if($post === FALSE){
			$this->_apiData['message'] = 'Invalid Post ID Request';
		} else{
        // success response
        $this->_apiData['response'] = "success";			
		$save['deleted_at'] = date('Y-m-d H:i:s');		
		// Update Post data
		$post_model->set($request->post_id, (array) $save);
		// get post data
		$post = $post_model->get($request->post_id);
		// message
		$this->_apiData['message'] = "Delete Post";

		$data['post'] = $post;
		// assign to output
		$this->_apiData['data'] = $data;
        }    
        return $this->__ApiResponse($request,$this->_apiData);
    }

	/**
     * GetPost
     *
     * @return Response
     */
    public function getPost(Request $request) {		
		// load models
		$post_model = $this->_model_path."Post";
		$post_model = new $post_model;
		$user_log_model = $this->_model_path."UserLog";
		$user_log_model = new $user_log_model;		
		
		// trim/escape all
		$request->merge(array_map('strip_tags', $request->all()));
		$request->merge(array_map('trim', $request->all()));			
		// get post data
		$post = $post_model->get($request->post_id);		
		// validations
        if ($request->post_id == '') {
            $this->_apiData['message'] = 'Please enter post_id';
        } else if($post === FALSE){
			$this->_apiData['message'] = 'Invalid Post ID Request';
		} else{
        // success response
        $this->_apiData['response'] = "success";			
		// get post data
		$post = $post_model->get($request->post_id);
		// message
		$this->_apiData['message'] = "Get Post";

		$data['post'] = $post;
		// assign to output
		$this->_apiData['data'] = $data;
        }    
        return $this->__ApiResponse($request,$this->_apiData);
    }
	
	/**
     * GetAllPost List
     *
     * @return Response
     */
    public function getAllPost(Request $request) {		
		// load models
		$post_model = $this->_model_path."Post";
		$post_model = new $post_model;
		$user_log_model = $this->_model_path."UserLog";
		$user_log_model = new $user_log_model;		
		
		// trim/escape all
		$request->merge(array_map('strip_tags', $request->all()));
		$request->merge(array_map('trim', $request->all()));			
		// get user data
		$user = $post_model->get($request->user_id);		
		// validations
        if ($request->user_id == '') {
            $this->_apiData['message'] = 'Please enter user_id';
        } else if($user === FALSE){
			$this->_apiData['message'] = 'Invalid User ID Request';
		} else{
        // success response
        $this->_apiData['response'] = "success";	
		// set initial array for records
			$data["post_list"] = array();
			$page_no = $request->page_no? $request->page_no: 0;
			// find total pages
			$query = $post_model->where("p.user_id", "=", $request->user_id)
				->whereNull("p.deleted_at");			
			$query->from("post AS p");
			$query->leftJoin('user', 'user.user_id', '=', 'p.user_id');
			$total_records = $query->count();
			/*var_dump($total_records);
			var_dump($query->toSql());
			exit;*/
			// offfset / limits / valid pages
			$total_pages = ceil($total_records / 20);
			$page_no = $page_no >= $total_pages ? $total_pages : $page_no;
			$page_no = $page_no <= 1 ? 1 : $page_no;
			$offset = 20 * ($page_no - 1);
			
			// query records
			$query = $post_model->where("p.user_id", "=", $request->user_id)
				->whereNull("p.deleted_at");			
			$query->from("post AS p");
			$query->leftJoin('user', 'user.user_id', '=', 'p.user_id');
			/*var_dump($query->toSql());
			exit;*/
			
			$query->take(20);
			$query->skip($offset);
			$query->orderBy("post_id", "ASC");
			$data["post_list"] = $query->get();
			// set pagination response
			$data["page"] = array(
				"current" => $page_no,
				"total" => $total_pages,
				"next" => $page_no >= $total_pages ? 0 : $page_no + 1,
				"prev" => $page_no <= 1 ? 0 : $page_no - 1
			);
			// message
			$this->_apiData['message'] = "Get All Post";
			// assign to output
			$this->_apiData['data'] = $data;
        }    
        return $this->__ApiResponse($request,$this->_apiData);
    }
}
