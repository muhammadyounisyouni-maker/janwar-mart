<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAdmin
{
    public function handle(Request $r, Closure $next)
    {
        if (!$r->user() || !$r->user()->isAdmin()) abort(403, 'Admins only');
        return $next($r);
    }
}
