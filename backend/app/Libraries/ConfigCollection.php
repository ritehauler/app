<?php
namespace App\Libraries;

use App\Http\Models\Conf;

/**
 * Class ConfigCollection
 * @package App\Libraries
 */
Class ConfigCollection
{

    private $_modelPath = '';
    private $_model = '';
    /**
     * ProductHelper constructor.
     */
    public function __construct()
    {
        $this->_modelPath = config("system.MODEL_PATH");
        $this->_model = new Conf();

    }

    /**
     * Get Config by key
     * @param $key
     * @param $value
     * @return mixed
     */
    public function getConfigByKey($value)
    {
        return $this->_model->getSchemaByKey($value);

    }

    /**
     * get Site Name
     * @return mixed
     */
    public function getSiteName()
    {
        $conf = $this->getConfigByKey('site');
        return $conf->site_name;
    }

    /**
     * Get Notification placeholders
     * @return mixed
     */
    public function getNotifyPlaceHolder()
    {
        $conf = $this->getConfigByKey('notification_placeholder');
       return $conf;
    }

}
