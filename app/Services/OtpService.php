<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

/** Phone OTP: 6 digits, valid 5 minutes, one-time, 5 wrong tries, 60 s resend cooldown, 3 per phone / 15 min, 10 per IP / hour. */
class OtpService
{
    public function __construct(private SmsService $sms) {}

    public static function normalize(?string $p): string
    {
        $p = preg_replace('/[\s-]/', '', (string) $p);
        return preg_match('/^\+92/', $p) ? '0'.substr($p, 3) : $p;
    }
    public static function validPhone(?string $p): bool { return (bool) preg_match(config('janwar.phone_regex'), preg_replace('/[\s-]/', '', (string) $p)); }

    /** @throws \DomainException */
    public function send(string $phone, string $purpose, string $ip): void
    {
        $phone = self::normalize($phone);
        if (!self::validPhone($phone)) throw new \DomainException(__('Enter a valid phone number'));
        if (!in_array($purpose, ['signup', 'reset'], true)) throw new \DomainException('bad purpose');
        $exists = User::where('phone', $phone)->exists();
        if ($purpose === 'signup' && $exists) throw new \DomainException(__('This phone is already registered'));
        if (RateLimiter::tooManyAttempts("otp-ip:$ip", 10)) throw new \DomainException(__('Too many requests, try later'));
        if (RateLimiter::tooManyAttempts("otp-ph:$phone", 3)) throw new \DomainException(__('Too many codes requested, try later'));
        $row = DB::table('otps')->where(['phone' => $phone, 'purpose' => $purpose])->first();
        if ($row && strtotime($row->sent_at) > time() - 60) {
            throw new \DomainException(__('Wait a minute before asking for another code'));
        }
        RateLimiter::hit("otp-ip:$ip", 3600);
        RateLimiter::hit("otp-ph:$phone", 900);
        if ($purpose === 'reset' && !$exists) return;       // same answer: do not reveal which phones exist
        $code = (string) random_int(100000, 999999);
        DB::table('otps')->updateOrInsert(['phone' => $phone, 'purpose' => $purpose], [
            'code_hash' => $this->hash($phone, $code), 'expires_at' => now()->addMinutes(5), 'attempts' => 0, 'sent_at' => now(),
        ]);
        $this->sms->send($phone, "Janwar Mart code: $code (valid 5 minutes)");
    }

    /** @throws \DomainException */
    public function verify(string $phone, string $purpose, ?string $code): void
    {
        $phone = self::normalize($phone);
        $row = DB::table('otps')->where(['phone' => $phone, 'purpose' => $purpose])->first();
        if (!$row || strtotime($row->expires_at) < time()) throw new \DomainException(__('Code expired or not requested'));
        if ($row->attempts >= 5) throw new \DomainException(__('Too many wrong codes, request a new one'));
        if (!preg_match('/^\d{6}$/', (string) $code) || !hash_equals($row->code_hash, $this->hash($phone, $code))) {
            DB::table('otps')->where(['phone' => $phone, 'purpose' => $purpose])->increment('attempts');
            throw new \DomainException(__('Wrong code'));
        }
        DB::table('otps')->where(['phone' => $phone, 'purpose' => $purpose])->delete();   // single use
    }
    private function hash(string $phone, string $code): string { return hash_hmac('sha256', $phone.'|'.$code, (string) config('app.key')); }
}
