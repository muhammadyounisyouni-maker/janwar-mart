<?php
if (!function_exists('city_name')) {
    function city_name(?string $k): string { return config('janwar.cities')[$k][app()->getLocale()] ?? (string) $k; }
}
if (!function_exists('pkr')) {
    function pkr($n): string { return number_format((int) $n); }
}
if (!function_exists('is_ur')) {
    function is_ur(): bool { return app()->getLocale() === 'ur'; }
}
