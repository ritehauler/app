<?php namespace App\Http\Hooks\Magento2;

// models
#use App\Http\Models\Achievement;

class CategoryResponse
{

    protected $__modelPath = "\App\Http\Models\\";

    public function __construct()
    {

    }




    /**
     * Post
     * @param object $request
     * @param array $base_data
     * @return Object
     */
    public function get($request, $base_data)
    {
        /*// replace with arrays
        $base_data['data']['status_code'] = 'ok';
        // array change
        if(isset($base_data['data']['data']->custom_attributes)) {
            array_walk($base_data['data']['data']->custom_attributes, function (&$item) {
                // mask attribute code
                $item->attributeCode = $item->attribute_code;

                // unset which isnt needed
                unset($item->attribute_code);
            });
        }*/


        /*// replace with regular expressions
        $json = json_encode($base_data);
        $keys = array(
            '/(\"status_code\":)/siu',
            '/(\"attribute_code\":)/siu',
        );
        $replacers = array(
            '"statusCode":',
            '"attributeCode":'
        );
        $d = preg_replace($keys, $replacers, $json);
        $base_data = json_decode($d,true);
        */

        return $base_data;
    }

}