<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Http\Request;

// models
use App\Http\Models\SYSEntity;

class EntityController extends Controller
{

    protected $_assignData = array(
        //'s_title' => 'Admin', // singular title
        //'p_title' => 'Admins', // plural title
        //'p_dir' => DIR_ADMIN, // parent directory
        'page_action' => 'Listing', // default page action
        'parent_nav' => '', // parent navigation id
        'err_msg' => '',
        'succ_msg' => '',

    );
    protected $_module, $_pk, $_model, $_jsonData;

    // entity vars
    private $_entitySessionIdentifier, $_entityIdentifier, $_entityDir, $_entityModel, $_entityPk;

    /**
     * Prevent Unauthorized User
     */
    public function __construct(Request $request)
    {
        //$this->middleware('auth');
        // construct parent
        parent::__construct($request);

        /*// init models
        $this->_assignData["admin_model"] = new Admin;
        $this->_assignData["admin_module_permission_model"] = new AdminModulePermission;
        // set model path for views
        $this->_assignData["model_path"] = $this->__modelPath;
        // init current module model
        $this->_model = $this->__modelPath.$this->_model;
        $this->_model = $this->_assignData["model"] = new $this->_model;
        // default nav id
        $this->_assignData["active_nav"] = $this->_assignData["parent_nav"].$this->_module;
        // set dir path
        $this->_assignData["dir"] = $this->_assignData["p_dir"].$this->_module."/";
        // set module name
        $this->_assignData["module"] = $this->_module;
        // set primary key
        $this->_pk = $this->_assignData["pk"] = $this->_module."_id";
        // assign meta from parent constructor
        $this->_assignData["_meta"] = $this->__meta;
        //
        $this->_entityModel = $this->_assignData["admin_model"];*/
    }


}
