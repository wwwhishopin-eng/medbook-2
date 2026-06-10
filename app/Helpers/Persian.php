<?php

namespace App\Helpers;

class Persian
{
    private const WESTERN = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    private const PERSIAN = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

    public static function digits(string|int|null $value): string
    {
        if ($value === null) {
            return '';
        }

        return str_replace(self::WESTERN, self::PERSIAN, (string) $value);
    }

    public static function toWestern(string|null $value): string
    {
        if ($value === null) {
            return '';
        }

        return str_replace(self::PERSIAN, self::WESTERN, $value);
    }

    public static function currency(int|null $amount): string
    {
        if ($amount === null) {
            return '';
        }

        $formatted = number_format($amount);

        return self::digits($formatted) . ' تومان';
    }

    public static function date(string|null $date): string
    {
        if ($date === null) {
            return '';
        }

        return self::digits($date);
    }

    public static function phone(string|null $phone): string
    {
        if ($phone === null) {
            return '';
        }

        return self::digits($phone);
    }
}
