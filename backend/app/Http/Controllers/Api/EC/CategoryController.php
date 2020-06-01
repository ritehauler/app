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
// load lib
use App\Libraries\EC\Proxy as ECProxy;


//use Twilio;

class CategoryController extends Controller
{
    protected $_assignData = array(
        'p_dir' => '',
        'dir' => DIR_API
    );
    protected $_apiData = array();
    protected $_layout = "";
    protected $_models = array();
    protected $_jsonData = array();
    // conf
    private $_ecConf;
    private $_entityConf = 'CONF_CATEGORY';
    // proxy
    private $_ecProxy;

    // e-commerce
    protected $_objectIdentifier, $_jsonStubDir;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        // entity conf
        $this->_ecConf = config('ec_' . EC_BASE_API);
        $this->_entityConf = $this->_ecConf[$this->_entityConf];
        // set hooks / requests path
        $this->_extHookRequest = $this->_ecConf['HOOK_PATH'] . $this->_entityConf['CLASS_NAME'].'Request';
        $this->_extHook = $this->_ecConf['HOOK_PATH'] . $this->_entityConf['CLASS_NAME'].'Response';
        // set values
        $this->_objectIdentifier = $this->_entityConf['IDENTIFIER'];
        $this->_jsonStubDir = $this->_ecConf['DIR_JSON_STUB'];

        // init proxy lib
        $this->_ecProxy = new ECProxy($request); // e-commerce proxy

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
        $r = $this->_hookData($this->_extHookRequest, __FUNCTION__, $request, $request->all());
        $request->merge($r);


        // request data from selected platform
        $jsonData = $this->_ecProxy->request(
            $this->_ecConf,
            $this->_entityConf['CLASS_NAME'],
            __FUNCTION__,
            $request
        );

        $data = $jsonData;

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
        $r = $this->_hookData($this->_extHookRequest, __FUNCTION__, $request, $request->all());
        $request->merge($r);

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
        $r = $this->_hookData($this->_extHookRequest, __FUNCTION__, $request, $request->all());
        $request->merge($r);

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
        $r = $this->_hookData($this->_extHookRequest, __FUNCTION__, $request, $request->all());
        $request->merge($r);

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
        $r = $this->_hookData($this->_extHookRequest, __FUNCTION__, $request, $request->all());
        $request->merge($r);

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

