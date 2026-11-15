<?php
namespace App\Http\Controllers;

use App\Models\{LoginEvent, User};
use App\Services\TotpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, RateLimiter};
use Illuminate\Validation\ValidationException;

class TwoFactorController extends Controller
{
    public function __construct(private TotpService $totp) {}

    /* ---------- step 2 of login: ask for the code ---------- */
    public function challenge(Request $r)
    {
        if (!$this->pending($r)) return redirect('/login');
        return view('pages.two-factor');
    }

    public function verify(Request $r, AuthController $auth)
    {
        $p = $this->pending($r);
        if (!$p) return redirect('/login');
        $u = User::find($p['id']);
        $key = '2fa:'.$p['id'];
        if (RateLimiter::tooManyAttempts($key, 5)) throw ValidationException::withMessages(['code' => __('Too many attempts. Try again in 15 minutes.')]);
        $d = $r->validate(['code' => 'required|string|max:20']);
        $ok = false;
        if ($u && $u->hasTwoFactor() && !$u->isBlocked()) {
            if ($step = $this->totp->verify($u->two_factor_secret, $d['code'], $u->two_factor_last_step)) {
                $u->forceFill(['two_factor_last_step' => $step])->save(); $ok = true;
            } else {
                $h = $this->totp->hashRecovery($d['code']);
                $codes = $u->two_factor_recovery_codes ?: [];
                if (in_array($h, $codes, true)) {                     // a recovery code works only once
                    $u->forceFill(['two_factor_recovery_codes' => array_values(array_diff($codes, [$h]))])->save(); $ok = true;
                }
            }
        }
        if (!$ok) {
            RateLimiter::hit($key, 900);
            LoginEvent::record($u, $u?->phone ?? 'unknown', 'login_failed');
            throw ValidationException::withMessages(['code' => __('Wrong code')]);
        }
        RateLimiter::clear($key);
        $r->session()->forget('2fa');
        return $auth->finishLogin($r, $u, (bool) $p['remember']);
    }

    private function pending(Request $r): ?array
    {
        $p = $r->session()->get('2fa');
        if (!$p || time() - $p['at'] > 300) { $r->session()->forget('2fa'); return null; }   // 5 minutes to enter the code
        return $p;
    }

    /* ---------- turn it on / off (logged-in users) ---------- */
    public function setup(Request $r)
    {
        $u = $r->user();
        if (!$u->hasTwoFactor() && !$u->two_factor_secret) $u->forceFill(['two_factor_secret' => $this->totp->generateSecret()])->save();
        return view('pages.two-factor-setup', [
            'u' => $u, 'enabled' => $u->hasTwoFactor(), 'secret' => $u->hasTwoFactor() ? null : $u->two_factor_secret,
            'uri' => $u->hasTwoFactor() ? null : $this->totp->uri($u->two_factor_secret, $u->phone),
            'recovery' => session('recovery_codes'),
        ]);
    }

    public function enable(Request $r)
    {
        $u = $r->user();
        $d = $r->validate(['code' => 'required|string|max:10']);
        if ($u->hasTwoFactor() || !$u->two_factor_secret) return redirect('/two-factor/setup');
        $step = $this->totp->verify($u->two_factor_secret, $d['code']);
        if (!$step) throw ValidationException::withMessages(['code' => __('Wrong code')]);
        $plain = $this->totp->newRecoveryCodes();
        $u->forceFill([
            'two_factor_confirmed_at' => now(), 'two_factor_last_step' => $step,
            'two_factor_recovery_codes' => array_map(fn ($c) => $this->totp->hashRecovery($c), $plain),
        ])->save();
        return redirect('/two-factor/setup')->with('recovery_codes', $plain)->with('ok', __('Two-factor login is now ON'));
    }

    public function disable(Request $r)
    {
        $u = $r->user();
        $d = $r->validate(['password' => 'required|string', 'code' => 'required|string|max:10']);
        if (!Hash::check($d['password'], $u->password)) throw ValidationException::withMessages(['password' => __('Wrong password')]);
        if (!$u->hasTwoFactor() || !$this->totp->verify($u->two_factor_secret, $d['code'], $u->two_factor_last_step)) throw ValidationException::withMessages(['code' => __('Wrong code')]);
        $u->forceFill(['two_factor_secret' => null, 'two_factor_recovery_codes' => null, 'two_factor_confirmed_at' => null, 'two_factor_last_step' => null])->save();
        return redirect('/two-factor/setup')->with('ok', __('Two-factor login is now OFF'));
    }
}
