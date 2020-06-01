<?php
/**
 * Summary : Abstract for System Libraries
 *
 * Created by PhpStorm.
 * User: Salman
 * Date: 12/28/2017
 * Time: 5:42 PM
 */

namespace App\Libraries\System;

use App\Http\Models\SYSEntity;
use App\Http\Models\SYSEntityType;

/**
 * Class Base
 */
abstract class Base
{

    /**
     * Core Model path
     */
    protected $_modelPath;

    /**
     * Core hook path
     */
    protected $_hookPath;

    /**
     * Core library path
     */
    protected $_libPath;

    /**
     * System library path
     */
    protected $_sysLibPath;

    /**
     * entity model
     */
    protected $_entityModel;

    /**
     * Entity type model
     */
    protected $_eTypeModel;

    /**
     * System config
     */
    private $_sysConfig = 'system';


    /**
     * Constructor
     *
     */
    public function __construct()
    {
        // set paths
        $this->_modelPath = config($this->_sysConfig . '.MODEL_PATH');
        $this->_hookPath = config($this->_sysConfig . '.HOOK_PATH');
        $this->_libPath = config($this->_sysConfig . '.LIBRARY_PATH');
        $this->_sysLibPath = config($this->_sysConfig . '.SYS_LIBRARY_PATH');

        // init models
        $this->_entityModel = new SYSEntity();
        $this->_eTypeModel = new SYSEntityType();
    }


    /**
     * Merge php raw params into the given array
     * @param $params
     * @return array
     */
    protected function _mergeRawParams($params)
    {
        $param = is_object($params) ? (array)$params : $params;
        $params = array_merge($params, json_decode(file_get_contents('php://input'), TRUE));

        return $params;
    }
}