<?php namespace App\Libraries\EC;

use Illuminate\Http\Request;
/*
use App\Http\Models\SYSAttribute;
use App\Http\Models\SYSAttributeOption;*/

/**
 * Simple Fields Library
 *
 *
 * @category   Libraries
 * @package    Fields
 * @subpackage Libraries
 */
class Proxy
{

    private $_libPath = "App\\Libraries\\EC\\";
    private $_lib;

    /**
     * Constructor
     *
     * @param string $url URL
     */
    public function __construct(Request $request)
    {

    }


    /**
     * Constructor
     *
     * @param string $url URL
     */
    private function xx_getClient($base_conf, $class, $method, $request)
    {
        // load lib
        $client = $this->_libPath.$base_conf['DIR_LIBRARY'].'Base';
        return $client->getClient($base_conf);
    }


    /**
     * Constructor
     *
     * @param string $url URL
     */
    public function request($base_conf, $class, $func, $request)
    {
        // load class
        $class_path = $this->_libPath.$base_conf['DIR_LIBRARY'].$class;

        // if class exists
        if(class_exists($class_path)) {
            $class_obj = new $class_path($base_conf);
            $method_exists = method_exists($class_obj,$func);
            if($method_exists) {
                return $data = $class_obj->{$func}($request, $base_conf);
            }
        }
        return array("error" => "not aa found");
    }


}
