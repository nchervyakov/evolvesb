<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 19.03.2015
 * Time: 13:48
 */


namespace App\Utils;


class Strings 
{
    public static function ucfirst($string) {
        return  mb_strtoupper(mb_substr($string, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($string, 1, mb_strlen($string), 'UTF-8');
    }
}