<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureNotBlocked
{
    public function handle(Request $r, Closure $next)
    {
        $u = $r->user();
        if ($u && $u->isBlocked()) {
            Auth::logout(); $r->session()->invalidate(); $r->session()->regenerateToken();
            return redirect('/login')->withErrors(['login' => __('This account is blocked')]);
        }
        return $next($r);
    }
}
