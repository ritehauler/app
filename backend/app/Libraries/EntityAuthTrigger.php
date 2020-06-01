<?php
/**
 * Description: This class is create for auth trigger related to project
 * Author: Samreen <samreen.quyyum@cubixlabs.com>
 * Date: 12-Feb-1018
 * Time: 12:30 PM
 * Copyright: CubixLabs
 */

namespace App\Libraries;

use App\Http\Models\SYSEntityAuth;
use App\Http\Models\SYSEntityType;

use App\Libraries\System\Entity;

Class EntityAuthTrigger
{
    /**
     * after confirm signup first check if entity type is customer and business user
     * then update thir statuses as active
     * @param $data
     * @return \StdClass
     */
    public function confirmSignupAfterTrigger($data)
    {
        $return = new \StdClass();
        $return->error = 0;

        $data = is_array($data) ? (object)$data : $data;
       // echo "<pre>"; print_r( $data); exit;
        try {

            $return->message = 'success';

            if (isset($data->entity_auth_id) && isset($data->entity_type_id)) {
                $this->_updateEntityStatus($data->entity_auth_id,$data->entity_type_id);
            }
        }
        catch (\Exception $e) {
            $return->error = 1;
            $return->message = $e->getMessage();
            //  echo $e->getTraceAsString(); exit;
            //  $return->debug = 'File : ' . $e->getFile() . ' : Line ' . $e->getLine(); //" : Stack " . $e->getTraceAsString();
        }
        return $return;

    }

    /**
     * Update user statuses after verify Phone
     * @param $data
     * @return \StdClass
     */
    public function verifyPhoneAfterTrigger($data)
    {
        $return = new \StdClass();
        $return->error = 0;
        $data = is_array($data) ? (object)$data : $data;

        try {
            $return->message = 'success';

            if (isset($data->entity_auth_id) && isset($data->entity_type_id)) {
                $this->_updateEntityStatus($data->entity_auth_id,$data->entity_type_id);
            }
        }
        catch (\Exception $e) {
            $return->error = 1;
            $return->message = $e->getMessage();
            //  echo $e->getTraceAsString(); exit;
            //  $return->debug = 'File : ' . $e->getFile() . ' : Line ' . $e->getLine(); //" : Stack " . $e->getTraceAsString();
        }
        return $return;


    }

    /**
     * Update Entity Status
     * @param $entity_auth_id
     * @param $entity_type_id
     */
    private function _updateEntityStatus($entity_auth_id,$entity_type_id)
    {
        //if entity type id then get entity type data
        $entity_type_model = new SYSEntityType();
        $entity_type_data = $entity_type_model->getEntityTypeById($entity_type_id);

        if ($entity_type_data && isset($entity_type_data->identifier)) {

            if ($entity_type_data->identifier == 'customer' || $entity_type_data->identifier == 'business_user') {

                //Get entity id by auth id and entity type
                $entity_auth_model = new SYSEntityAuth();
                $entity_id = $entity_auth_model->getEntityByAuthAndEntityType($entity_auth_id, $entity_type_id);

                if ($entity_id) {
                    //update entity status
                    $entity_lib = new Entity();
                    $params['entity_type_id'] = $entity_type_id;
                    $params['entity_id'] = $entity_id;
                    $params['user_status'] = 1;
                    $params['is_profile_update'] = 1;
                    $entity_lib->apiUpdate($params);
                }

            }
        }
    }

    /**
     * @param $entity_type
     * @param $entity_id
     * @return \StdClass
     */
    public function loginAfterTrigger($entity_type,$entity_id,$first_login = false)
    {
        $return = new \StdClass();
        $return->error = 0;
        $entity_type = is_array($entity_type) ? (object)$entity_type : $entity_type;

        try {
            $return->message = 'success';

            if (isset($entity_id) && isset($entity_type->entity_type_id)) {

                //entity type is driver then update login status field
                if($entity_type->identifier == 'driver'){

                    $params = [];
                    $params['entity_type_id'] = $entity_type->entity_type_id;
                    $params['entity_id'] = $entity_id;
                    $params['login_status'] = 1;

                    //Check if first time login then on duty
                    if($first_login){
                        $params['on_duty'] = 1;
                    }

                    $entity_lib = new Entity();
                    $response = $entity_lib->apiUpdate($params);
                    $response = json_decode(json_encode($response));

                    if($response->error == 1){
                        $return->error = 1;
                        $return->message = $response->message;
                    }

                }

            }
        }
        catch (\Exception $e) {
            $return->error = 1;
            $return->message = $e->getMessage();
            //  echo $e->getTraceAsString(); exit;
            //  $return->debug = 'File : ' . $e->getFile() . ' : Line ' . $e->getLine(); //" : Stack " . $e->getTraceAsString();
        }
        return $return;
    }

    public function logoutAfterTrigger($entity_id,$entity_type = false)
    {
        $return = new \StdClass();
        $return->error = 0;

        try {
            $return->message = 'success';

            if (isset($entity_id) && isset($entity_type->entity_type_id)) {

                //entity type is driver then update login status field
                if($entity_type->identifier == 'driver'){

                    $params = [];
                    $params['entity_type_id'] = $entity_type->entity_type_id;
                    $params['entity_id'] = $entity_id;
                    $params['login_status'] = 0;
                    $entity_lib = new Entity();
                    $response = $entity_lib->apiUpdate($params);
                    $response = json_decode(json_encode($response));

                    if($response->error == 1){
                        $return->error = 1;
                        $return->message = $response->message;
                    }

                }

            }

        }
        catch (\Exception $e) {
            $return->error = 1;
            $return->message = $e->getMessage();
            //  echo $e->getTraceAsString(); exit;
            //  $return->debug = 'File : ' . $e->getFile() . ' : Line ' . $e->getLine(); //" : Stack " . $e->getTraceAsString();
        }
        return $return;
    }




}