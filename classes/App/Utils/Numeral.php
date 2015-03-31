<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 19.03.2015
 * Time: 13:41
 */


namespace App\Utils;


class Numeral extends \php_rutils\Numeral
{
    protected static $_FRACTIONS = array(
        array('десятая', 'десятых', 'десятых'),
        array('сотая', 'сотых', 'сотых'),
        array('тысячная', 'тысячных', 'тысячных'),
        array('десятитысячная', 'десятитысячных', 'десятитысячных'),
        array('стотысячная', 'стотысячных', 'стотысячных'),
        array('миллионная', 'милллионных', 'милллионных'),
        array('десятимиллионная', 'десятимилллионных', 'десятимиллионных'),
        array('стомиллионная', 'стомилллионных', 'стомиллионных'),
        array('миллиардная', 'миллиардных', 'миллиардных'),
    ); //Forms (1, 2, 5) for fractions

    public function getRubles($amount, $zeroForKopeck = false, $kopeikiAsNumbers = false)
    {
        if ($amount < 0)
            throw new \InvalidArgumentException('Amount must be positive or 0');

        $words = array();
        $amount = round($amount, 2);

        $iAmount = (int)$amount;
        if ($iAmount)
            $words[] = $this->sumString((int)$amount, RUtils::MALE,
                array('рубль', 'рубля', 'рублей'));

        $remainder = $this->_getFloatRemainder($amount, 2);
        if ($remainder || $zeroForKopeck) {
            if ($remainder < 10 && strlen($remainder) == 1)
                $remainder *= 10;
            $words[] = sprintf('%02d', $remainder).' '.$this->choosePlural($remainder, array('копейка', 'копейки', 'копеек'));
        }

        return trim(implode(' ', $words));
    }

    protected function _getFloatRemainder($value, $signs=9)
    {
        if ($value == (int)$value)
            return '0';

        $signs = min($signs, sizeof(self::$_FRACTIONS));
        $value = number_format($value, $signs, '.', '');
        list(, $remainder) = explode('.', $value);
        $remainder = preg_replace('/0+$/', '', $remainder);
        if (!$remainder)
            $remainder = '0';

        return $remainder;
    }
}