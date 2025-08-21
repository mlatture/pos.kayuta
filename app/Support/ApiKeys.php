<?php

namespace App\Support;

use Illuminate\Support\Str;

class ApiKeys
{
    public static function generate(int $length = 48): string
    {
        if (! method_exists(Str::class, 'password')) {
            Str::macro('password', function (
                int $length = 32,
                bool $letters = true,
                bool $numbers = true,
                bool $symbols = true
            ) {
                $pool = '';
        
                if ($letters) {
                    $pool .= 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                }
                if ($numbers) {
                    $pool .= '0123456789';
                }
                if ($symbols) {
                    $pool .= '!@#$%^&*()_+-={}[]|:;<>,.?/~';
                }
        
                if ($pool === '') {
                    throw new \InvalidArgumentException('At least one character set must be selected.');
                }
        
                return substr(str_shuffle(str_repeat($pool, (int) ceil($length / strlen($pool)))), 0, $length);
            });
        }
        // 48+ chars random; url-safe
        return Str::password($length, symbols: false);
    }

    public static function hash(string $key): string
    {
        // HMAC-SHA256 with app key; store hex
        return hash_hmac('sha256', $key, config('app.key'));
    }
}