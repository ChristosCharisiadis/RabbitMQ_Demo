<?php


namespace App\Utils;

/**
 *
 * This class contains some static helper methods for doing math operations.
 * @package App\Utils
 */
class MathUtils
{
    private function __construct() {}

    /**
     * Transforms a hexadecimal number to a decimal number. This works for big numbers that hexdec has issues with.
     * @param $hex
     * @return int|string
     */
    public static function bcHexDec($hex) {
        $dec = 0;
        $len = strlen($hex);
        for ($i = 1; $i <= $len; $i++) {
            $dec = bcadd($dec, bcmul(strval(hexdec($hex[$i - 1])), bcpow('16', strval($len - $i))));
        }
        return $dec;
    }

    /**
     * Transforms a decimal number to a hexadecimal number. This works for big numbers that dechex has issues with.
     * @param $number
     * @return string
     */
    public static function bcDecHex($number) {
        $hexValues = array('0','1','2','3','4','5','6','7',
            '8','9','a','b','c','d','e','f');
        $hexVal = '';
        while($number != '0')
        {
            $hexVal = $hexValues[bcmod($number,'16')].$hexVal;
            $number = bcdiv($number,'16',0);
        }
        return $hexVal;
    }
}