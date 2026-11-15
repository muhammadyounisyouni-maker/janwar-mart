<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $r, Closure $next)
    {
        $l = session('lang', config('app.locale'));
        app()->setLocale(in_array($l, ['ur', 'en'], true) ? $l : 'ur');
        return $next($r);
    }
}
