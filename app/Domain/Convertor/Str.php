<?php


namespace App\Domain\Convertor;


use NumberFormatter;

/**
 * Class Str
 * @package App\Domain\Convertor
 */
class Str
{
    /**
     * @param $amount
     * @return mixed|string
     */
    public static function TrimZeroes($amount)
    {
        if ($amount == null) {
            return '';
        }
        return strpos($amount, '.') !== false ? rtrim(rtrim($amount, '0'), '.') : $amount;
    }

    /**
     * @param $float
     * @return string
     */
    public static function Beautify($float): string
    {
        if ($float >= 1) {
            return number_format($float, 2);
        }

        if ($float < 1 && $float >= 0.1) {
            return number_format($float, 3);
        }

        if ($float < 0.1 && $float >= 0.01) {
            return number_format($float, 4);
        }

        if ($float < 0.01 && $float >= 0.001) {
            return number_format($float, 5);
        }

        if ($float < 0.001 && $float >= 0.0001) {
            return number_format($float, 6);
        }

        if ($float < 0.0001 && $float >= 0.00001) {
            return number_format($float, 7);
        }

        if ($float < 0.00001 && $float >= 0.000001) {
            return number_format($float, 8);
        }

        if ($float < 0.000001 && $float >= 0.0000001) {
            return number_format($float, 9);
        }

        if ($float < 0.0000001 && $float >= 0.00000001) {
            return number_format($float, 10);
        }

        if ($float < 0.00000001 && $float >= 0.000000001) {
            return number_format($float, 11);
        }

        if ($float < 0.000000001 && $float >= 0.0000000001) {
            return number_format($float, 12);
        }

        if ($float < 0.0000000001 && $float >= 0.00000000001) {
            return number_format($float, 13);
        }

        if ($float < 0.00000000001 && $float >= 0.000000000001) {
            return number_format($float, 14);
        }

        if ($float < 0.000000000001 && $float >= 0.0000000000001) {
            return "~" . number_format($float, 14);
        }
        return self::TrimZeroes($float);
    }

    /**
     * @return NumberFormatter
     */
    public static function formatter()
    {
        $formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
        $formatter->setSymbol(NumberFormatter::CURRENCY_SYMBOL, '');
        return $formatter;
    }
}
