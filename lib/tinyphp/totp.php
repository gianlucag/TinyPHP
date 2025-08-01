<?php

class TOTP
{
    private static function Base32Decode($b32)
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $b32 = strtoupper($b32);
        $l = strlen($b32);
        $n = 0;
        $j = 0;
        $binary = '';
        for ($i = 0; $i < $l; $i++) {
            $n = ($n << 5) + strpos($alphabet, $b32[$i]);
            $j += 5;
            if ($j >= 8) {
                $j -= 8;
                $binary .= chr(($n & (0xFF << $j)) >> $j);
            }
        }
        return $binary;
    }

    public static function GenerateSecret($length = 16)
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= $chars[random_int(0, 31)];
        }
        return $secret;
    }

    public static function Generate($secret, $timeStep = 30, $digits = 6, $algo = 'sha1')
    {
        $key = self::Base32Decode($secret);
        $time = floor(time() / $timeStep);
        $timeBytes = pack('N*', 0) . pack('N*', $time);
        $hash = hash_hmac($algo, $timeBytes, $key, true);
        $offset = ord($hash[19]) & 0xf;
        $code = (
            ((ord($hash[$offset]) & 0x7f) << 24) |
            ((ord($hash[$offset + 1]) & 0xff) << 16) |
            ((ord($hash[$offset + 2]) & 0xff) << 8) |
            (ord($hash[$offset + 3]) & 0xff)
        ) % pow(10, $digits);
        return str_pad($code, $digits, '0', STR_PAD_LEFT);
    }

    public static function Verify($secret, $code, $timeStep = 30, $digits = 6, $window = 1, $algo = 'sha1')
    {
        $currentTime = floor(time() / $timeStep);
        for ($i = -$window; $i <= $window; $i++) {
            $calcCode = self::Generate($secret, $timeStep, $digits, $algo, $currentTime + $i);
            if (hash_equals($calcCode, $code)) {
                return true;
            }
        }
        return false;
    }

    public static function GetProvisioningUri($accountName, $secret, $issuer, $digits = 6, $period = 30)
    {
        $label = rawurlencode($issuer ? "$issuer:$accountName" : $accountName);
        $query = http_build_query([
            'secret' => $secret,
            'issuer' => $issuer,
            'algorithm' => 'SHA1',
            'digits' => $digits,
            'period' => $period,
        ]);
        return "otpauth://totp/{$label}?{$query}";
    }
}

?>