<?php
namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Api\System\EntityController;
use View;
use DB;
use Validator;
use Illuminate\Http\Request;

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
        $this->_assignData["dir"] = $this->_panelPath();
        // assign meta from parent constructor
        $this->_assignData["_meta"] = $this->__meta;
        // assign request
        $this->_assignData["request"] = $request;
    }

    /**
     * Return data to admin listing page
     *
     * @return type Array()
     */
    public function index(Request $request)
    {
        $view = View::make($this->_assignData["dir"] . __FUNCTION__, $this->_assignData);
        return $view;
    }


    /**
     * Login
     *
     * @return type view
     */
    public function login(Request $request)
    {
        $view = View::make($this->_assignData["dir"] . __FUNCTION__, $this->_assignData);

        $request->entity_type = !$request->entity_type ? config("backend.DEFAULT_LOGIN_ENTITY") : $request->entity_type;

        if ($request->isMethod('post')) {
            //

            return;
        }

        // override request
        $this->_assignData["request"] = $request;

        return $view;
    }


}
