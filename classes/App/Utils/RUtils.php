<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 19.03.2015
 * Time: 13:41
 */


namespace App\Utils;


class RUtils extends \php_rutils\RUtils
{
    protected static $_numeral;

    public static function numeral()
    {
        if (self::$_numeral === null)
            self::$_numeral = new Numeral();
        return self::$_numeral;
    }
}