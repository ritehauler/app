<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\SocialGistUserController;
use Illuminate\Http\Request;
use View;
use Validator;
// load models
use App\Http\Models\ApiMethod;
use App\Http\Models\ApiMethodField;
use App\Http\Models\ApiUser;
use App\Http\Models\EFEntityPlugin;
use App\Http\Models\Conf;

//use Twilio;

class UserController extends SocialGistUserController
{


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        //set vars
        $this->_object_identifier = "user";
        $this->_entity_identifier = "user";
        $this->_entity_pk = "user_id";
        $this->_entity_ucfirst = "User";
        $this->_entity_conf_file = "pl_user";

        // load entity model
        // get all webservices data
        //$this->__models['entity_plugin_model'] = new EFEntityPlugin;
        $this->_entity_model = $this->_model_path . $this->_entity_ucfirst;
        $this->_entity_model = new $this->_entity_model;

        // init models
        $this->__models['api_method_model'] = new ApiMethod;
        $this->__models['api_user_model'] = new ApiUser;
        $this->__models['conf_model'] = new Conf;
        $this->_entity_model = $this->_entity_model;

        // error response by default
        $this->_apiData['kick_user'] = 0;
        $this->_apiData['response'] = "error";

        // check access
        $this->__models['api_user_model']->checkAccess($request);
        // plugin config
        //$this->_plugin_config = $this->__models['entity_plugin_model']->getPluginSchema($this->_entity_id, $this->_plugin_identifier);
        // set defaults
        //$this->_plugin_config = isset($this->_plugin_config->webservices) ? $this->_plugin_config->webservices : array();
        //$this->_plugin_config["webservices"] = $this->_plugin_config;

    }




}