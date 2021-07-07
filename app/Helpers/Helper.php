<?php

namespace App\Helpers;

use DateTime;
use DateTimeZone;

class Helper
{
    /**
     * Convert seconds to hours and mins or decimal
     * @param int $seconds
     * @param bool $to_decimal
     * @return mixed
     */
    public static function secondsToHoursMinutes(int $seconds, string $format = 'HH.MM')
    {
        $hours = floor($seconds / 3600);
        $seconds -= $hours * 3600;
        $minutes = floor($seconds / 60);
        $seconds -= $minutes * 60;

        switch($format){
            case 'HH.MM':
                $result = floatval($hours . '.' . $minutes);
                break;
            case 'decimal':
                $result = round($hours + ($minutes / 60) + ($seconds / 3600), 4);
                break;
            case 'HH:MM:SS':
                $hours = $hours < 10 ? '0' . $hours : $hours;
                $minutes = $minutes < 10 ? '0' . $minutes : $minutes;
                $seconds = $seconds < 10 ? '0' . $seconds : $seconds;

                $result = $hours .':'. $minutes .':'. $seconds;
                break;
            default:
                break;
        }

        return $result;
    }

    /**
     * Convert timezone
     * @param string $time
     * @param string $timezone
     * @param string $from_timezone
     * @return DateTime
     */
    public static function adjustTimeZone(string $time,string $timezone, string $from_timezone = 'UTC')
    {
        $change_time = new DateTime($time, new DateTimeZone($from_timezone));
        $change_time->setTimezone(new DateTimeZone($timezone));
        return $change_time;
    }
}
