<?php
namespace App\Services;

/**
 * Google Authenticator compatible one-time codes (TOTP, RFC 6238: HMAC-SHA1, 6 digits, 30 seconds).
 * Pure PHP: no extra package needed. Works with Google Authenticator, Microsoft Authenticator, Authy, 2FAS...
 */
class TotpService
{
    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    public function generateSecret(int $bytes = 20): string
    {
        return $this->base32Encode(random_bytes($bytes));
    }

    /** otpauth:// link that the authenticator app reads (shown as a QR code) */
    public function uri(string $secret, string $account, string $issuer = 'Janwar Mart'): string
    {
        return 'otpauth://totp/'.rawurlencode($issuer.':'.$account).'?secret='.$secret.'&issuer='.rawurlencode($issuer).'&algorithm=SHA1&digits=6&period=30';
    }

    public function code(string $secret, int $step): string
    {
        $key = $this->base32Decode($secret);
        $hash = hash_hmac('sha1', pack('N*', 0).pack('N', $step), $key, true);
        $o = ord($hash[19]) & 0x0F;
        $bin = ((ord($hash[$o]) & 0x7F) << 24) | (ord($hash[$o + 1]) << 16) | (ord($hash[$o + 2]) << 8) | ord($hash[$o + 3]);
        return str_pad((string) ($bin % 1000000), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Returns the matched time-step (int) or null. Accepts the previous / next 30 s step (clock drift).
     * $lastUsedStep stops the same code from being used twice (replay protection).
     */
    public function verify(string $secret, ?string $input, ?int $lastUsedStep = null, ?int $now = null): ?int
    {
        $input = preg_replace('/\s+/', '', (string) $input);
        if (!preg_match('/^\d{6}$/', $input)) return null;
        $current = intdiv($now ?? time(), 30);
        for ($s = $current - 1; $s <= $current + 1; $s++) {
            if ($lastUsedStep !== null && $s <= $lastUsedStep) continue;
            if (hash_equals($this->code($secret, $s), $input)) return $s;
        }
        return null;
    }

    /** 8 one-time recovery codes like "a1b2-c3d4" (shown once; only hashes are stored) */
    public function newRecoveryCodes(int $n = 8): array
    {
        return array_map(fn () => substr($h = bin2hex(random_bytes(4)), 0, 4).'-'.substr($h, 4, 4), range(1, $n));
    }
    public function hashRecovery(string $code): string
    {
        return hash('sha256', strtolower(preg_replace('/[^a-z0-9]/i', '', $code)));
    }

    public function base32Encode(string $bin): string
    {
        $bits = '';
        foreach (str_split($bin) as $c) $bits .= str_pad(decbin(ord($c)), 8, '0', STR_PAD_LEFT);
        $out = '';
        foreach (str_split($bits, 5) as $chunk) $out .= self::ALPHABET[bindec(str_pad($chunk, 5, '0'))];
        return $out;
    }
    public function base32Decode(string $s): string
    {
        $s = strtoupper(preg_replace('/[^A-Z2-7]/i', '', $s));
        $bits = '';
        foreach (str_split($s) as $c) $bits .= str_pad(decbin(strpos(self::ALPHABET, $c)), 5, '0', STR_PAD_LEFT);
        $out = '';
        foreach (str_split($bits, 8) as $byte) if (strlen($byte) === 8) $out .= chr(bindec($byte));
        return $out;
    }
}
