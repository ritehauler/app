<?php
namespace App\Libraries;

use App\Libraries\ConfigCollection;

/**
 * Class NotificationWildcard
 * @package App\Libraries
 */
Class NotificationWildcard
{
    /**
     * Get Notification wildcards
     * @param $entity
     * @return bool
     */
    public function getNotificationWildCards($entity)
    {
        $conf = new ConfigCollection();
        $placeholders = $conf->getNotifyPlaceHolder();
        $site_name = $conf->getSiteName();

        if($placeholders){

            $wildcard['key'] = $placeholders;
            $wildcard['replace'] = array(
                $site_name, // APP_NAME
                url('/'), //APP_LINK
                isset($entity->user_name) ? $entity->user_name : "", // ENTITY_NAME
                isset($entity->mobile_no) ? $entity->mobile_no : "",
                isset($entity->email) ? $entity->email : "", // EMAIL_ADDRESS
            );

            return $wildcard;
        }

        return false;
    }

    /**
     * Replace Text with wildcards
     * @param $entity
     * @param $text
     * @return mixed
     */
    public function replaceNotifyText($entity,$text)
    {
        $wildcards = $this->getNotificationWildCards($entity);
       // print_r($wildcards); exit;
        if($wildcards)
            return str_replace($wildcards['key'], $wildcards['replace'], $text);
        else
            return $text;

    }
}