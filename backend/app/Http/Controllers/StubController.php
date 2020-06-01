<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use View;
use Cache;
use Input;
use Validator;
use Illuminate\Http\Request;

// load models
//use App\Http\Models\User as AppUser;


class StubController extends Controller
{

    private $_assignData = array(
        'pDir' => '',
        'dir' => DIR_STUB_CONSOLE
    );
    private $_headerData = array();
    private $_footerData = array();
    private $_layout = "";
    protected $_model = "ApiStub";

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // load extension model
        $this->_model = $this->_modelPath . $this->_model;
        $this->_model = new $this->_model;

        // assign model to view
        $this->_assignData['_model'] = $this->_model;
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function index()
    {


        $this->_assignData["raw_methods"] = $this->_model
            ->where("is_active", 1)
            ->whereNull("deleted_at")
            ->orderBy("title", "ASC")
            //->orderBy("order", "ASC")
            ->get(array($this->_model->primaryKey));


        $this->_layout .= view($this->_assignData["dir"] . __FUNCTION__, $this->_assignData)->with($this->__models);
        return $this->_layout;
    }


    /**
     * Method    :    load_params
     * Reason    :    load parameters for requested api method
     **/
    public function loadParams(Request $request)
    {
        // view file
        $view_file = strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', __FUNCTION__));

        $this->_assignData['record'] = $this->_model->get($request->{$this->_model->primaryKey});

        // target element
        $this->_jsonData['targetElem'] = 'div[id=parameters]';

        // html into string
        $this->_jsonData['html'] = View::make($this->_assignData["dir"] . $view_file, $this->_assignData)->__toString();

        $this->_assignData['jsonData'] = $this->_jsonData;
        $this->_layout .= view(DIR_ADMIN . "jsonResponse", $this->_assignData)->with($this->__models);
        return $this->_layout;

    }

}
