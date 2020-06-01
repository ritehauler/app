<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Validator;
// load models
use App\Http\Models\ApiMethod;
use App\Http\Models\ApiMethodField;
use App\Http\Models\ApiUser;
use App\Http\Models\Conf;


class StubApiController extends Controller
{

    public $_assignData = array(
        'pDir' => '',
        'dir' => DIR_API
    );
    public $_apiData = array();
    public $_layout = "";
    public $_models = array();
    public $_jsonData = array();
    protected $_model = "ApiStub";
    protected $_hook = "APIStub";

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        // load extension model
        $this->_model = $this->_modelPath . $this->_model;
        $this->_model = new $this->_model;

        // error response by default
        $this->_apiData['kick_user'] = 0;
        $this->_apiData['response'] = "error";
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
     * post
     *
     * @return Response
     */

    public function post(Request $request, $endpoint_uri)
    {
        $data = array();

        // get record
        $record = $this->_model
            ->where('request_type', __FUNCTION__)
            ->where('endpoint_uri', $endpoint_uri)
            ->whereNull('deleted_at')
            ->first();
        $record = json_decode(json_encode($record));

        if ($record) {
            $data = json_decode($record->response);
        } else {
            $data = array('not found');
        }

        return response()->json($data);

    }


    /**
     * get
     *
     * @return Response
     */
    public function get(Request $request, $endpoint_uri)
    {
        $data = array();

        // get record
        $record = $this->_model
            ->where('request_type', __FUNCTION__)
            ->where('endpoint_uri', $endpoint_uri)
            ->whereNull('deleted_at')
            ->first();
        $record = json_decode(json_encode($record));

        if ($record) {
            $data = json_decode($record->response);
        } else {
            $data = array('not found');
        }

        return response()->json($data);

    }

}
