<?php namespace App\Libraries\EC\Magento2;

/*
use App\Http\Models\SYSAttribute;
use App\Http\Models\SYSAttributeOption;*/

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
class Base
{
    /**
     * Constructor
     *
     * @param string $url URL
     */
    public function __construct(Request $request)
    {
    }


    /**
     * get client
     *
     * @param string $url URL
     */
    protected function _getClient($base_conf)
    {
        $consumer_key = $base_conf['CONSUMER_KEY'];
        $consumer_secret = $base_conf['CONSUMER_SECRET'];
        $access_token = $base_conf['ACCESS_TOKEN'];
        $access_token_secret = $base_conf['ACCESS_TOKEN_SECRET'];

        $configuration = new Configuration;

        // set keys
        $configuration->setConsumerKey($consumer_key);
        $configuration->setConsumerSecret($consumer_secret);
        $configuration->setToken($access_token);
        $configuration->setTokenSecret($access_token_secret);
        $apiFactory = new ApiFactory($configuration);
        $client = $apiFactory->getApiClient();
        return $client;
    }


    /**
     * Constructor
     *
     * @param string $url URL
     */
    private function myUrlEncode($string)
    {
        $entities = array('+', '%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D');
        $replacements = array(' ', '!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]");
        return str_replace($entities, $replacements, urlencode($string));
    }

}
 