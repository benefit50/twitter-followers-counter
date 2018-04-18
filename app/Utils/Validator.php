<?php
namespace App\Utils;

use Carbon\Carbon;

/**
 * Class Validator
 * @package App\Utils
 */
class Validator
{
    /**
     * @param string $url
     * @return bool
     */
    public function validateUrl(string $url) : bool
    {
        $pattern = '~http(s?):\/\/(\bwww.)?twitter.com\/(#!\/)?([^\/]*)/status/(\d{10,})~';

        return preg_match($pattern, $url);
    }

    /**
     * @param Carbon $time
     * @param $hours
     * @return bool
     */
    public function timeExpired(Carbon $time, $hours) : bool
    {
        return $time->diffInMinutes(now()) > $hours * 60;
    }
}