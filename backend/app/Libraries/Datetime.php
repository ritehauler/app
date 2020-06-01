<?php

namespace App\Libraries;

class Datetime
{
    public static function getTimeZone()
    {
        try {

            $ip = $_SERVER['REMOTE_ADDR'];
            $ipInfo = file_get_contents('http://ip-api.com/json/' . $ip);
            $ipInfo = json_decode($ipInfo);
            $timezone = $ipInfo->timezone;

        } catch (\Exception $e) {
            $timezone = 'Asia/Karachi';
        }

        return $timezone;
    }

    public static function convertLocalToUTC($datetime, $tz_from)
    {
        $tz_to = 'UTC';
        $format = 'Y-m-d H:i:s';

        $dt = new \DateTime($datetime, new \DateTimeZone($tz_from));
        $dt->setTimeZone(new \DateTimeZone($tz_to));
        return $dt->format($format);
    }

    public static function convertUTCToLocal($datetime, $tz_to , $tz_from = 'UTC')
    {

        date_default_timezone_set($tz_from);
        $sTime = date("Y-m-d h:i:s");
        $ts3 = strtotime(date("G:i:s"))-strtotime($sTime);


        $utc = explode(" ",$datetime);
        $time = strtotime($utc[0]);
        date_default_timezone_set($tz_to);
            $time += $ts3;  // Add the difference

        return $utc[0];
    }
}
