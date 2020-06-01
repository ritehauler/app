<?php

/**
 * Description: this library is create for getting information from google api
 * Author: Samreen <samreen.quyyum@cubixlabs.com>
 * Date: 13-June-2018
 * Time: 05:25 PM
 * Copyright: CubixLabs
 */
namespace App\Libraries;

use App\Http\Models\Setting;

/**
 * Class GoogleApi
 * @package App\Libraries
 */
Class GoogleApi
{
    /**
     * @var string
     */
    private $_apiKey = '';

    /**
     * GoogleApi constructor.
     */
    public function __construct()
    {
        $setting_model = new Setting();
        $google_key = $setting_model->getBy('key','google_api_key');
        $this->_apiKey = (isset($google_key->value)) ? $google_key->value : "";
    }

    /**
     * @param $lat1
     * @param $long1
     * @param $lat2
     * @param $long2
     * @return array
     */
    public function GetDrivingDistance($lat1, $long1, $lat2, $long2)
    {
        //$lat1 = "26.616756"; $long1= "-80.068451";
       // $lat2 = "42.480591"; $long2= "-83.475494";

        $url = config('constants.GOOGLE_API_DISTANCE');
        $url .= "?origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&language=pl-PL&&key=". $this->_apiKey;

        //echo $url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_a = json_decode($response, true);

      //  echo "<pre>"; print_r($response_a); exit;
        $dist = isset($response_a['rows'][0]['elements'][0]['distance']) ? $response_a['rows'][0]['elements'][0]['distance'] : '';
        $time = isset($response_a['rows'][0]['elements'][0]['duration']) ? $response_a['rows'][0]['elements'][0]['duration'] : '';

        return array('distance' => $dist, 'time' => $time);
    }
}