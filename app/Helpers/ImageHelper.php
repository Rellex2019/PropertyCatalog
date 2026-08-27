<?php

namespace App\Helpers;

class ImageHelper
{
    public static function getImageUrl(string $path): string
    {
        $appUrl = config('app.url');
        
        // Если URL уже содержит http, возвращаем как есть
        if (str_starts_with($path, 'http')) {
            return $path;
        }
        
        // Если путь начинается со слеша, убираем его
        $path = ltrim($path, '/');
        
        return $appUrl . '/' . $path;
    }
    
    public static function getStorageUrl(string $path): string
    {
        $appUrl = config('app.url');
        $path = ltrim($path, '/');
        
        return $appUrl . '/storage/' . $path;
    }
}