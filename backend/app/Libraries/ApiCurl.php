<?php namespace App\Libraries;
use App\Http\Models\SYSAttribute;
use App\Http\Models\SYSAttributeOption;
use Illuminate\Http\Request;
/**
 * Simple Fields Library
 *
 *
 * @category   Libraries
 * @package    Fields
 * @subpackage Libraries
 */
class ApiCurl
{
    /**
     * Constructor
     *
     * @param string $url URL
     */
    public function __construct(){
	}
  
    /**
     * internal call
     * @param string $url
     * @param string $method
     * @param string $params
     * @return count
     */
    public function apiPostRequest($url, $type, $parameter = array(), $is_external = false) {
        if($is_external) {
            //$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
            //$url = $protocol.$url;
            $curl_init = curl_init();
            curl_setopt($curl_init, CURLOPT_USERPWD, API_ACCESS_USER . ":" .API_ACCESS_PASS);
            curl_setopt($curl_init, CURLOPT_VERBOSE, 1);
            curl_setopt($curl_init, CURLOPT_HEADER, false);
            curl_setopt($curl_init, CURLOPT_CUSTOMREQUEST, $type);

            if ($type == "POST") {
                curl_setopt($curl_init, CURLOPT_URL, $url);
                //curl_setopt($curl_init, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));
                curl_setopt($curl_init, CURLOPT_POST, 1);
                curl_setopt($curl_init, CURLOPT_POSTFIELDS, $parameter);
            } else {
                curl_setopt($curl_init, CURLOPT_URL, $url . "?" . http_build_query($parameter));
                curl_setopt($curl_init, CURLOPT_POST, 0);
            }

            curl_setopt($curl_init, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl_init);
            return json_decode($response);
        }

        $request = Request();
        return CustomHelper::internalCall($request, $url, $type, $parameter);
    }


}
 