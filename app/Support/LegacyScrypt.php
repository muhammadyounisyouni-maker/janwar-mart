<?php

namespace App\Support;

/**
 * Verifies passwords stored by the OLD Python server: "<salt-hex>:<scrypt-hex>"
 * Python: hashlib.scrypt(password, salt=salt_hex.encode(), n=16384, r=8, p=1, dklen=32)
 *
 * PHP has no built-in scrypt, so this is a plain RFC 7914 implementation. It takes a few seconds, which is fine
 * because it runs ONCE per imported user: after a successful login the password is re-saved with bcrypt.
 */
final class LegacyScrypt
{
    public static function verify(string $password, ?string $stored): bool
    {
        if (!$stored || !str_contains($stored, ':')) {
            return false;
        }
        [$salt, $good] = explode(':', $stored, 2);
        if (!preg_match('/^[0-9a-f]{64}$/', $good)) {
            return false;
        }
        return hash_equals($good, bin2hex(self::derive($password, $salt)));
    }

    public static function derive(string $password, string $salt, int $N = 16384, int $r = 8, int $p = 1, int $dkLen = 32): string
    {
        $B = hash_pbkdf2('sha256', $password, $salt, 1, $p * 128 * $r, true);
        $out = '';
        for ($i = 0; $i < $p; $i++) {
            $out .= self::roMix(substr($B, $i * 128 * $r, 128 * $r), $N, $r);
        }
        return hash_pbkdf2('sha256', $password, $out, 1, $dkLen, true);
    }

    private static function roMix(string $block, int $N, int $r): string
    {
        $X = array_values(unpack('V*', $block));
        $V = [];
        for ($i = 0; $i < $N; $i++) {
            $V[$i] = pack('V*', ...$X);          // packed strings keep memory at ~16 MB instead of ~70 MB
            $X = self::blockMix($X, $r);
        }
        $last = (2 * $r - 1) * 16;
        for ($i = 0; $i < $N; $i++) {
            $j = $X[$last] & ($N - 1);
            $Vj = unpack('V*', $V[$j]);
            for ($k = 0; $k < 32 * $r; $k++) {
                $X[$k] ^= $Vj[$k + 1];
            }
            $X = self::blockMix($X, $r);
        }
        return pack('V*', ...$X);
    }

    private static function blockMix(array $B, int $r): array
    {
        $X = array_slice($B, (2 * $r - 1) * 16, 16);
        $even = [];
        $odd = [];
        for ($i = 0; $i < 2 * $r; $i++) {
            for ($k = 0; $k < 16; $k++) {
                $X[$k] ^= $B[$i * 16 + $k];
            }
            $X = self::salsa208($X);
            if ($i % 2 === 0) {
                array_push($even, ...$X);
            } else {
                array_push($odd, ...$X);
            }
        }
        return array_merge($even, $odd);
    }

    private static function salsa208(array $in): array
    {
        $x = $in;
        for ($i = 0; $i < 4; $i++) {
            $t = ($x[0] + $x[12]) & 0xFFFFFFFF;  $x[4]  ^= (($t << 7)  | ($t >> 25)) & 0xFFFFFFFF;
            $t = ($x[4] + $x[0]) & 0xFFFFFFFF;   $x[8]  ^= (($t << 9)  | ($t >> 23)) & 0xFFFFFFFF;
            $t = ($x[8] + $x[4]) & 0xFFFFFFFF;   $x[12] ^= (($t << 13) | ($t >> 19)) & 0xFFFFFFFF;
            $t = ($x[12] + $x[8]) & 0xFFFFFFFF;  $x[0]  ^= (($t << 18) | ($t >> 14)) & 0xFFFFFFFF;
            $t = ($x[5] + $x[1]) & 0xFFFFFFFF;   $x[9]  ^= (($t << 7)  | ($t >> 25)) & 0xFFFFFFFF;
            $t = ($x[9] + $x[5]) & 0xFFFFFFFF;   $x[13] ^= (($t << 9)  | ($t >> 23)) & 0xFFFFFFFF;
            $t = ($x[13] + $x[9]) & 0xFFFFFFFF;  $x[1]  ^= (($t << 13) | ($t >> 19)) & 0xFFFFFFFF;
            $t = ($x[1] + $x[13]) & 0xFFFFFFFF;  $x[5]  ^= (($t << 18) | ($t >> 14)) & 0xFFFFFFFF;
            $t = ($x[10] + $x[6]) & 0xFFFFFFFF;  $x[14] ^= (($t << 7)  | ($t >> 25)) & 0xFFFFFFFF;
            $t = ($x[14] + $x[10]) & 0xFFFFFFFF; $x[2]  ^= (($t << 9)  | ($t >> 23)) & 0xFFFFFFFF;
            $t = ($x[2] + $x[14]) & 0xFFFFFFFF;  $x[6]  ^= (($t << 13) | ($t >> 19)) & 0xFFFFFFFF;
            $t = ($x[6] + $x[2]) & 0xFFFFFFFF;   $x[10] ^= (($t << 18) | ($t >> 14)) & 0xFFFFFFFF;
            $t = ($x[15] + $x[11]) & 0xFFFFFFFF; $x[3]  ^= (($t << 7)  | ($t >> 25)) & 0xFFFFFFFF;
            $t = ($x[3] + $x[15]) & 0xFFFFFFFF;  $x[7]  ^= (($t << 9)  | ($t >> 23)) & 0xFFFFFFFF;
            $t = ($x[7] + $x[3]) & 0xFFFFFFFF;   $x[11] ^= (($t << 13) | ($t >> 19)) & 0xFFFFFFFF;
            $t = ($x[11] + $x[7]) & 0xFFFFFFFF;  $x[15] ^= (($t << 18) | ($t >> 14)) & 0xFFFFFFFF;
            $t = ($x[0] + $x[3]) & 0xFFFFFFFF;   $x[1]  ^= (($t << 7)  | ($t >> 25)) & 0xFFFFFFFF;
            $t = ($x[1] + $x[0]) & 0xFFFFFFFF;   $x[2]  ^= (($t << 9)  | ($t >> 23)) & 0xFFFFFFFF;
            $t = ($x[2] + $x[1]) & 0xFFFFFFFF;   $x[3]  ^= (($t << 13) | ($t >> 19)) & 0xFFFFFFFF;
            $t = ($x[3] + $x[2]) & 0xFFFFFFFF;   $x[0]  ^= (($t << 18) | ($t >> 14)) & 0xFFFFFFFF;
            $t = ($x[5] + $x[4]) & 0xFFFFFFFF;   $x[6]  ^= (($t << 7)  | ($t >> 25)) & 0xFFFFFFFF;
            $t = ($x[6] + $x[5]) & 0xFFFFFFFF;   $x[7]  ^= (($t << 9)  | ($t >> 23)) & 0xFFFFFFFF;
            $t = ($x[7] + $x[6]) & 0xFFFFFFFF;   $x[4]  ^= (($t << 13) | ($t >> 19)) & 0xFFFFFFFF;
            $t = ($x[4] + $x[7]) & 0xFFFFFFFF;   $x[5]  ^= (($t << 18) | ($t >> 14)) & 0xFFFFFFFF;
            $t = ($x[10] + $x[9]) & 0xFFFFFFFF;  $x[11] ^= (($t << 7)  | ($t >> 25)) & 0xFFFFFFFF;
            $t = ($x[11] + $x[10]) & 0xFFFFFFFF; $x[8]  ^= (($t << 9)  | ($t >> 23)) & 0xFFFFFFFF;
            $t = ($x[8] + $x[11]) & 0xFFFFFFFF;  $x[9]  ^= (($t << 13) | ($t >> 19)) & 0xFFFFFFFF;
            $t = ($x[9] + $x[8]) & 0xFFFFFFFF;   $x[10] ^= (($t << 18) | ($t >> 14)) & 0xFFFFFFFF;
            $t = ($x[15] + $x[14]) & 0xFFFFFFFF; $x[12] ^= (($t << 7)  | ($t >> 25)) & 0xFFFFFFFF;
            $t = ($x[12] + $x[15]) & 0xFFFFFFFF; $x[13] ^= (($t << 9)  | ($t >> 23)) & 0xFFFFFFFF;
            $t = ($x[13] + $x[12]) & 0xFFFFFFFF; $x[14] ^= (($t << 13) | ($t >> 19)) & 0xFFFFFFFF;
            $t = ($x[14] + $x[13]) & 0xFFFFFFFF; $x[15] ^= (($t << 18) | ($t >> 14)) & 0xFFFFFFFF;
        }
        $out = [];
        for ($i = 0; $i < 16; $i++) {
            $out[$i] = ($in[$i] + $x[$i]) & 0xFFFFFFFF;
        }
        return $out;
    }
}
