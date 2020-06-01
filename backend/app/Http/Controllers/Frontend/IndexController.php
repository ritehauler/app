<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Api\System\EntityController;
use View;
use DB;
use Validator;
use Illuminate\Http\Request;
use App\Http\Models\ApiMethod;
use App\Http\Models\ApiMethodField;
use App\Http\Models\ApiUser;

// models
use App\Http\Models\SYSEntity;


class IndexController extends EntityController
{

    /**
     * Prevent Unauthorized User
     */
    public function __construct(Request $request)
    {

        //$this->middleware('auth');
        // construct parent
        parent::__construct($request);

        // define default dir
        $this->_assignData["dir"] = config("frontend.DIR");
        // assign meta from parent constructor
        $this->_assignData["_meta"] = $this->__meta;
        // assign request
        $this->_assignData["request"] = $request;
		$this->__models['api_method_model'] = new ApiMethod;
    }

    /**
     * Return data to admin listing page
     *
     * @return type Array()
     */
    public function index(Request $request)
    {	
		$para['_token'] = csrf_token();
		$para['uri'] = "get|system/attribute";
		$this->_assignData['data'] = $this->getData($para);
		$view = View::make($this->_assignData["dir"] . __FUNCTION__, $this->_assignData);
        return $view;
    }


    /**
     * Login
     *
     * @return type view
     */
    public function getData($post)
    {
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://localhost/cubix3/frontend/postData');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
		return $response = curl_exec($ch);
		
    }
	public function load_params(Request $request)
	{
		// load model
		$this->__models['api_method_field_model'] = new ApiMethodField;

		//$this->_assignData['api_method'] = $this->__models['api_method_model']->get((int)Input::get('api_method_id', 0));
		$uri_data = explode("|",$request->input("uri",""));
		// defaults
		$type = preg_match("@|@",$request->uri) ? $uri_data[0] : "post";
		$uri = preg_match("@|@",$request->uri) ? $uri_data[1] : $uri_data[0];

		$this->_assignData['api_method'] =
			$this->__models['api_method_model']
				->where("type","=",$type)
				->where("uri","=",$uri)
				->where("is_active","=",1)
				->whereNull("deleted_at")
				->first();

		if ($this->_assignData['api_method'] !== FALSE) {
			// fetch
			$query = $this->__models['api_method_field_model']
				->where('is_active', '=', 1)
				->whereNull("deleted_at")
				->where("request_type","=",$type)
				->where('method_uri', '=', $this->_assignData['api_method']->uri);
			$query->orderBy("order", "ASC");

			$this->_assignData['records'] = $query->get(array("api_method_field_id"));


			// target element
			$this->_jsonData['targetElem'] = 'div[id=parameters]';

			// html into string
			$this->_jsonData['html'] = View::make($this->_assignData["dir"] . "/" . __FUNCTION__, $this->_assignData)->with($this->__models)->__toString();

			$this->_assignData['jsonData'] = $this->_jsonData;
			$this->_layout .= view(DIR_ADMIN . "jsonResponse", $this->_assignData)->with($this->__models);
			return $this->_layout;
		}

	}


}
