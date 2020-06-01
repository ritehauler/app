<?php

/**
 * Description: this library is to get truck information
 * Author: Samreen <samreen.quyyum@cubixlabs.com>
 * Date: 27-June-2018
 * Time: 05:00 PM
 * Copyright: CubixLabs
 */
namespace App\Libraries;


Class OrderAddress
{
    /**
     * Validate Addresses
     * @param $address
     * @return mixed
     */
    public function validateAddress($address)
    {
        $address = is_array($address) ? (object)$address : $address;
        if(!isset($address->address) || $address->address == ''){
            $response['error'] = TRUE;
            $response['message'] = trans('api_errors.field_required',array('field' => 'address'));
            return $response;
        }

        if(!isset($address->latitude) || $address->latitude == ''){
            $response['error'] = TRUE;
            $response['message'] = trans('api_errors.field_required',array('field' => 'latitude'));
            return $response;
        }

        if(!isset($address->longitude) || $address->longitude == ''){
            $response['error'] = TRUE;
            $response['message'] = trans('api_errors.field_required',array('field' => 'longitude'));
            return $response;
        }

        if(!isset($address->city_id) || $address->city_id == ''){
            $response['error'] = TRUE;
            $response['message'] = trans('api_errors.field_required',array('field' => 'city'));
            return $response;
        }

        $response['error'] = 0;
        return $response;
    }
}