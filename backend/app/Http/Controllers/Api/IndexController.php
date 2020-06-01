<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use View;
use Illuminate\Http\Request;

// load models
#use App\Http\Models\Category as Category;
use App\Http\Models\ApiMethod;
use App\Http\Models\ApiMethodField;
use App\Http\Models\SYSEntityType;
use App\Http\Models\ApiUser;

class IndexController extends Controller
{
    private $_model_path = "\App\Http\Models\\";
    private $_assignData = array(
        'pDir' => '',
        'dir' => DIR_API
    );
    private $_headerData = array();
    private $_footerData = array();
    private $_layout = "";
    private $_jsonData = array();

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        //$this->middleware('guest');
        // init models
        $this->__models['api_method_model'] = new ApiMethod;
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function index()
    {

        // init models
        //$this->__models['api_method_model'] = $api_method_model = new ApiMethod;

        /*
        // non-blade templating
        $view = view($this->_assignData["dir"]."/".__FUNCTION__, $this->_assignData);
        return $view;*/
        /*
        // blade templating
        return View::make($this->_assignData["dir"].".index");
        $data['header'] = View::make($this->_assignData["dir"].".index")->render();
        //$this->_layout .= View::make($this->_assignData["dir"].".blade.index",$this->_assignData)->with($this->__models);
        */
        // get api methods
        //$this->_assignData["raw_methods"] = $this->__models['api_method_model']->all(array("api_method_id"));
        $this->_assignData["raw_methods"] = $this->__models['api_method_model']
            ->where("is_active", 1)
            ->whereNull("deleted_at")
            ->orderBy("name", "ASC")
            //->orderBy("order", "ASC")
            ->get(array("api_method_id"));

        $this->_layout .= view($this->_assignData["dir"] . "/" . __FUNCTION__, $this->_assignData)->with($this->__models);

        return $this->_layout;

    }


    /**
     * Method    :    load_params
     * Reason    :    load parameters for requested api method
     **/
    public function load_params(Request $request)
    {
        // load model
        $this->__models['api_method_field_model'] = new ApiMethodField;

        //$this->_assignData['api_method'] = $this->__models['api_method_model']->get((int)Input::get('api_method_id', 0));
        $uri_data = explode("|", $request->input("uri", ""));
        // defaults
        $type = preg_match("@|@", $request->uri) ? $uri_data[0] : "post";
        $uri = preg_match("@|@", $request->uri) ? $uri_data[1] : $uri_data[0];
        $type_id = preg_match("@|@", $request->uri) ? isset($uri_data[2])?$uri_data[2]:0 : $uri_data[0];
        $this->_assignData['api_method'] =
            $this->__models['api_method_model']
                ->where("type", "=", $type)
                ->where("uri", "=", $uri)
                ->where("type_id", "=", $type_id)
                ->where("is_active", "=", 1)
                ->whereNull("deleted_at")
                ->first();

        if ($this->_assignData['api_method'] !== FALSE) {
            if (isset($request->entity_type_id)) $this->_assignData['api_method']->type_id = $request->entity_type_id;

            if ($this->_assignData['api_method']->type_id != '0') {
                $entity_type_model = $this->_model_path . "SYSEntityType";
                $entity_type_model = new $entity_type_model;
                $entity_type = $entity_type_model->getData($this->_assignData['api_method']->type_id);
                if ($entity_type && $entity_type->show_gallery == "1") {
                    $this->_assignData['entity_type'] = $entity_type;
                    $this->_assignData['show_gallery'] = $entity_type->show_gallery;
                }
            }
            // fetch
            $query = $this->__models['api_method_field_model']
                ->where('is_active', '=', 1)
                ->whereNull("deleted_at")
                ->where("request_type", "=", $type)
                ->where('method_uri', '=', $this->_assignData['api_method']->uri);
            $query->orderBy("order", "ASC");

            $this->_assignData['records'] = $query->get();


            // target element
            $this->_jsonData['targetElem'] = 'div[id=parameters]';

            // html into string
            $this->_jsonData['html'] = View::make($this->_assignData["dir"] . "/" . __FUNCTION__, $this->_assignData)->with($this->__models)->__toString();

            $this->_assignData['jsonData'] = $this->_jsonData;
            $this->_layout .= view(DIR_API.'json_response', $this->_assignData)->with($this->__models);
            return $this->_layout;
        }

    }


    /**
     * Under Development
     *
     * @return Response
     */
    public function ud(Request $request)
    {
        // init models
        $this->__models['api_user_model'] = new ApiUser;
        // check access
        $this->__models['api_user_model']->checkAccess($request);

        // error response by default
        $this->_apiData['kick_user'] = 0;
        $this->_apiData['response'] = "error";
        $this->_apiData['message'] = "Under Development";

        return $this->__ApiResponse($request, $this->_apiData);
    }

}
