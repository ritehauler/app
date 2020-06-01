<?php namespace App\Http\Controllers;

use App\Http\Controllers\SocialGistUserController;
use View;
use Cache;
use Validator;
use Illuminate\Http\Request;

// load models
use App\Http\Models\Notification;

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
        $this->_entity_api_route = "users/";

        // load entity model
        // get all webservices data
        //$this->__models['entity_plugin_model'] = new EFEntityPlugin;
        $this->_entity_model = $this->_modelPath . $this->_entity_ucfirst;
        $this->_entity_model = new $this->_entity_model;

        // init models
        //$this->_assignData['conf_model'] = $this->_model_path."Conf";
        //$this->_assignData['conf_model'] = new $this->_assignData['conf_model'];

        // default assigns

        $this->_assignData["model_path"] = $this->_modelPath;
        // plugin config
        //$this->_plugin_config = $this->__models['entity_plugin_model']->getPluginSchema($this->_entity_id, $this->_plugin_identifier);
    }


}
