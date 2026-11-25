<?php

namespace App\Support;

class PhoneNumber
{
    public static function normalize(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);

        return '+'.$digits;
    }

    public static function isValid(string $phone): bool
    {
        $digits = preg_replace('/\D+/', '', $phone);

        return strlen($digits) >= 7 && strlen($digits) <= 15;
    }
}
