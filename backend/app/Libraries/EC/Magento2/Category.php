<?php namespace App\Libraries\EC\Magento2;

/*
use App\Http\Models\SYSAttribute;
use App\Http\Models\SYSAttributeOption;*/

use App\Libraries\EC\Magento2\Base;

use Illuminate\Http\Request;
use springimport\magento2\apiv1\Configuration;
use springimport\magento2\apiv1\ApiFactory;

/**
 * Simple Fields Library
 *
 *
 * @category   Libraries
 * @package    Fields
 * @subpackage Libraries
 */
class Category extends Base
{
    private $_entityConf = 'CONF_CATEGORY';
    private $_client;
    private $_apiUrl;


    /**
     * Constructor
     *
     * @param string $url URL
     */
    public function __construct($base_conf)
    {
        $this->_entityConf = $base_conf[$this->_entityConf];
        $this->_client = $this->_getClient($base_conf);
        $this->_apiUrl = trim($base_conf['API_BASE_URL'],'/').'/';
    }


    /**
     * Constructor
     *
     * @param string $url URL
     */
    public function get(Request $request, $base_conf)
    {
        // request method
        $request_method = 'GET';
        // get endpoint
        $endpoint = trim($this->_entityConf['API_ENDPOINT_GET'],'/');
        // set request params
        $endpoint .= '/'.$request->input("categoryId",0); // category ID
        $endpoint .= $request->input("storeId",NULL) ? '/'.$request->input("storeId",NULL) : ''; // store ID

        // rest url
        $endpoint = $this->_apiUrl.$endpoint;

        // cal API
        $res = $this->_client->request($request_method, $endpoint, ['verify' => false]);
        $response['status_code'] = $res->getStatusCode();
        $response['data'] = json_decode($res->getBody());

        return $response;
    }

}
 