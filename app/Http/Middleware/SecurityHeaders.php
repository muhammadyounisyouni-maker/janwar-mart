<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $r, Closure $next)
    {
        $res = $next($r);
        $res->headers->set('X-Content-Type-Options', 'nosniff');
        $res->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $res->headers->set('Referrer-Policy', 'same-origin');
        if ($r->isSecure()) $res->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        return $res;
    }
}
