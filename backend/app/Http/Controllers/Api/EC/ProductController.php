<?php
namespace App\Http\Controllers\Api\EC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use View;
use Validator;
// load models
use App\Http\Models\ApiMethod;
use App\Http\Models\ApiMethodField;
use App\Http\Models\ApiUser;
use App\Http\Models\Conf;

//use Twilio;

class ProductController extends Controller
{
    protected $_assignData = array(
        'p_dir' => '',
        'dir' => DIR_API
    );
    protected $_apiData = array();
    protected $_layout = "";
    protected $_models = array();
    protected $_jsonData = array();
    // hook / request
    private $_extHookRequest = "Category"; // request hook
    private $_extHook = "Category"; // response hook

    // e-commerce
    protected $_objectIdentifier, $_jsonStubDir;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        // set hooks / requests path
        $this->_extHookRequest = config('ec_'.EC_BASE_API.'.REQUEST_HOOK_PATH').$this->_extHookRequest;
        $this->_extHook = config('ec_'.EC_BASE_API.'.RESPONSE_HOOK_PATH').$this->_extHook;
        // set values
        $this->_objectIdentifier = config('ec_'.EC_BASE_API.'.IDENTIFIER_PRODUCT');
        $this->_jsonStubDir = config('ec_'.EC_BASE_API.'.DIR_JSON_STUB');

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
     * get
     *
     * @return Response
     */
    public function get(Request $request)
    {

        // success response
        $this->_apiData['response'] = "success";

        // init output data array
        $this->_apiData['data'] = $data = array();

        // call response hook
        $request = $this->_hookData($this->_extHookRequest, __FUNCTION__, $request, $request->all());

        // get json dir
        $file_path = $this->_jsonStubDir . $this->_objectIdentifier . "/" . __FUNCTION__ . ".json";
        // get json data
        $jsonData = json_decode(file_get_contents($file_path));

        $data[$this->_objectIdentifier] = $jsonData;

        // assign to output
        $this->_apiData['data'] = $data;

        // call response hook
        $this->_apiData = $this->_hookData($this->_extHook, __FUNCTION__, $request, $this->_apiData);

        return $this->__ApiResponse($request, $this->_apiData);

    }


    /**
     * post
     *
     * @return Response
     */
    public function post(Request $request)
    {

        // success response
        $this->_apiData['response'] = "success";

        // init output data array
        $this->_apiData['data'] = $data = array();

        // call response hook
        $request = $this->_hookData($this->_extHookRequest, __FUNCTION__, $request, $request->all());

        // get json dir
        $file_path = $this->_jsonStubDir . $this->_objectIdentifier . "/" . __FUNCTION__ . ".json";
        // get json data
        $jsonData = json_decode(file_get_contents($file_path));

        $data[$this->_objectIdentifier] = $jsonData;

        // assign to output
        $this->_apiData['data'] = $data;

        // call response hook
        $this->_apiData = $this->_hookData($this->_extHook, __FUNCTION__, $request, $this->_apiData);

        return $this->__ApiResponse($request, $this->_apiData);

    }


    /**
     * listing
     *
     * @return Response
     */
    public function listing(Request $request)
    {

        // success response
        $this->_apiData['response'] = "success";

        // init output data array
        $this->_apiData['data'] = $data = array();

        // call response hook
        $request = $this->_hookData($this->_extHookRequest, __FUNCTION__, $request, $request->all());

        // get json dir
        $file_path = $this->_jsonStubDir . $this->_objectIdentifier . "/" . __FUNCTION__ . ".json";
        // get json data
        $jsonData = json_decode(file_get_contents($file_path));

        $data[$this->_objectIdentifier] = $jsonData;

        // assign to output
        $this->_apiData['data'] = $data;

        // call response hook
        $this->_apiData = $this->_hookData($this->_extHook, __FUNCTION__, $request, $this->_apiData);

        return $this->__ApiResponse($request, $this->_apiData);

    }


    /**
     * update
     *
     * @return Response
     */
    public function update(Request $request)
    {

        // success response
        $this->_apiData['response'] = "success";

        // init output data array
        $this->_apiData['data'] = $data = array();

        // call response hook
        $request = $this->_hookData($this->_extHookRequest, __FUNCTION__, $request, $request->all());

        // get json dir
        $file_path = $this->_jsonStubDir . $this->_objectIdentifier . "/" . __FUNCTION__ . ".json";
        // get json data
        $jsonData = json_decode(file_get_contents($file_path));

        $data[$this->_objectIdentifier] = $jsonData;

        // assign to output
        $this->_apiData['data'] = $data;

        // call response hook
        $this->_apiData = $this->_hookData($this->_extHook, __FUNCTION__, $request, $this->_apiData);

        return $this->__ApiResponse($request, $this->_apiData);

    }


    /**
     * delete
     *
     * @return Response
     */
    public function delete(Request $request)
    {

        // success response
        $this->_apiData['response'] = "success";

        // init output data array
        $this->_apiData['data'] = $data = array();

        // call response hook
        $request = $this->_hookData($this->_extHookRequest, __FUNCTION__, $request, $request->all());

        // get json dir
        $file_path = $this->_jsonStubDir . $this->_objectIdentifier . "/" . __FUNCTION__ . ".json";
        // get json data
        $jsonData = json_decode(file_get_contents($file_path));

        $data[$this->_objectIdentifier] = $jsonData;

        // assign to output
        $this->_apiData['data'] = $data;

        // call response hook
        $this->_apiData = $this->_hookData($this->_extHook, __FUNCTION__, $request, $this->_apiData);

        return $this->__ApiResponse($request, $this->_apiData);

    }


}

