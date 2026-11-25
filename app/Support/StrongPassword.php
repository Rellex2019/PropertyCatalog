<?php

namespace App\Support;

class StrongPassword
{
    public static function problems(string $password): array
    {
        $problems = [];

        if (mb_strlen($password) < 12) {
            $problems[] = 'минимум 12 символов';
        }

        if (! preg_match('/\p{Ll}/u', $password) || ! preg_match('/\p{Lu}/u', $password)) {
            $problems[] = 'заглавная и строчная буквы';
        }

        if (! preg_match('/\d/u', $password)) {
            $problems[] = 'хотя бы одна цифра';
        }

        if (! preg_match('/[^\p{L}\p{N}]/u', $password)) {
            $problems[] = 'хотя бы один специальный символ';
        }

        return $problems;
    }

    public static function message(string $password): ?string
    {
        $problems = self::problems($password);

        if (! $problems) {
            return null;
        }

        return 'Пароль не соответствует требованиям: '.implode('; ', $problems).'.';
    }
}
