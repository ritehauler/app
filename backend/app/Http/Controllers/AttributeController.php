<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Http\Request;

// models
use App\Http\Models\SYSEntity;

class AttributeController extends Controller
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
    public function __construct()
    {

    }


}
