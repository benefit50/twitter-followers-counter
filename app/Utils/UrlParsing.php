<?php
namespace App\Utils;

/**
 * Class UrlParsing
 * @package App\Utils
 */
class UrlParsing
{
    /**
     * @param string $url
     * @return mixed
     */
    public function getTweetId(string $url) : string
    {
        $_url = '';

        if (strpos($url, 'http://') !== false)
            $_url = str_replace('http://','',$url);

        if (strpos($url, 'https://') !== false)
            $_url = str_replace('https://','',$url);

        $parts = explode('/', $_url);

        return $parts[3];
    }
}