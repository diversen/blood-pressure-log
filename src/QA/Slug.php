<?php

namespace App\QA;

class Slug
{

    public static function rawurlencodeStripSpaces($str)
    {
        $str = str_replace(' ', '-', $str);
        return rawurlencode($str);
    }

    public static function sanitizeUrlRigid($string, $force_lowercase = true, $remove_special = false)
    {
        $strip = array("~", "\n", "\t", "\r", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
            "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
            "â€”", "â€“", "<", ">", "/", "?", ",", ".");
        return $clean = trim(str_replace($strip, "", strip_tags($string)));
    }

    public static function utf8SlugString($title)
    {
        $title = self::sanitizeUrlRigid($title);
        $title = self::rawurlencodeStripSpaces($title);
        return $title;
    }

}