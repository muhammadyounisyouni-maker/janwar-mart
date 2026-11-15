<?php
namespace App\Http\Controllers;

use App\Http\Requests\SignupRequest;
use App\Models\{AdminNotification, LoginEvent, User};
use App\Services\OtpService;
use App\Support\LegacyScrypt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash, RateLimiter};
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(private OtpService $otp) {}

    public function form(Request $r) { return view('pages.auth', ['tab' => $r->routeIs('register') ? 'signup' : ($r->routeIs('password.reset') ? 'reset' : 'login')]); }

    public function sendOtp(Request $r)
    {
        $d = $r->validate(['phone' => 'required|string|max:20', 'purpose' => 'required|in:signup,reset']);
        try { $this->otp->send($d['phone'], $d['purpose'], $r->ip()); }
        catch (\DomainException $e) { return response()->json(['error' => $e->getMessage()], 422); }
        return response()->json(['ok' => true]);
    }

    public function signup(SignupRequest $r)
    {
        $d = $r->validated();
        try { $this->otp->verify($d['phone'], 'signup', $d['otp']); }
        catch (\DomainException $e) { throw ValidationException::withMessages(['otp' => $e->getMessage()]); }
        $u = User::create(['name' => $d['name'], 'phone' => $d['phone'], 'email' => $d['email'] ?? null, 'password' => $d['password'], 'role' => $d['role'], 'city' => 'lhr']);
        LoginEvent::record($u, $u->phone, 'signup');
        AdminNotification::push('👤', "New {$u->role}: {$u->name} ({$u->phone})");
        Auth::login($u); $r->session()->regenerate();
        $u->update(['last_login_at' => now(), 'login_count' => 1]);
        if ($r->boolean('enable_2fa')) return redirect('/two-factor/setup')->with('ok', __('Welcome to Janwar Mart').' — '.__('now scan the QR code to turn on two-factor login'));
        return redirect($u->role === 'doctor' ? '/doctor/register' : '/')->with('ok', __('Welcome to Janwar Mart'));
    }

    public function login(Request $r)
    {
        $d = $r->validate(['login' => 'required|string|max:120', 'password' => 'required|string|max:100']);
        $id = strtolower(trim($d['login']));
        $key = 'login:'.$r->ip().'|'.$id;
        if (RateLimiter::tooManyAttempts($key, 10)) throw ValidationException::withMessages(['login' => __('Too many attempts. Try again in 15 minutes.')]);
        $u = str_contains($id, '@') ? User::where('email', $id)->first() : User::where('phone', OtpService::normalize($id))->first();
        $ok = false;
        if ($u) {
            if ($u->legacy_pw && LegacyScrypt::verify($d['password'], $u->legacy_pw)) {
                $u->forceFill(['password' => $d['password'], 'legacy_pw' => null])->save();   // upgrade old Python hash to bcrypt
                $ok = true;
            } elseif (!$u->legacy_pw && Hash::check($d['password'], $u->password)) {
                $ok = true;
            }
        }
        if (!$ok) {
            RateLimiter::hit($key, 900);
            LoginEvent::record($u, $id, 'login_failed');
            throw ValidationException::withMessages(['login' => __('Wrong phone/email or password')]);
        }
        if ($u->isBlocked()) throw ValidationException::withMessages(['login' => __('This account is blocked')]);
        RateLimiter::clear($key);
        if ($u->hasTwoFactor()) {   // password sahi: ab Google Authenticator ka code
            $r->session()->put('2fa', ['id' => $u->id, 'remember' => $r->boolean('remember'), 'at' => time()]);
            return redirect('/two-factor');
        }
        return $this->finishLogin($r, $u, $r->boolean('remember'));
    }

    /** Normal login aur 2FA step dono yahin se sign in karte hain */
    public function finishLogin(Request $r, User $u, bool $remember)
    {
        Auth::login($u, $remember); $r->session()->regenerate();
        $u->update(['last_login_at' => now(), 'login_count' => $u->login_count + 1]);
        LoginEvent::record($u, $u->phone, $u->isAdmin() ? 'admin_login' : 'login');
        return redirect()->intended($u->isAdmin() ? '/admin' : '/');
    }

    public function reset(Request $r)
    {
        $d = $r->validate(['phone' => 'required|string|max:20', 'otp' => 'required|digits:6', 'password' => 'required|string|min:6|max:100']);
        $phone = OtpService::normalize($d['phone']);
        try { $this->otp->verify($phone, 'reset', $d['otp']); }
        catch (\DomainException $e) { throw ValidationException::withMessages(['otp' => $e->getMessage()]); }
        $u = User::where('phone', $phone)->first();
        if ($u) {
            $u->forceFill(['password' => $d['password'], 'legacy_pw' => null, 'pw_changed_at' => now()])->save();
            LoginEvent::record($u, $u->phone, 'password_reset');
        }
        return redirect('/login')->with('ok', __('Password changed. Please log in.'));
    }

    public function logout(Request $r)
    {
        Auth::logout(); $r->session()->invalidate(); $r->session()->regenerateToken();
        return redirect('/');
    }
}