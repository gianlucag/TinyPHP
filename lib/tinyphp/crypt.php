<?php

class Crypt
{
    public static function GetRandomHex($length)
    {
        $rnd = substr(hash("sha256", bin2hex(random_bytes(128))), 0, $length);
        return $rnd;
    }

    public static function GetRandomUUID() {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0F | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3F | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public static function Encrypt($message, $key)
    {
        $nonceSize = openssl_cipher_iv_length("aes-256-ctr");
        $nonce = openssl_random_pseudo_bytes($nonceSize);

        $ciphertext = openssl_encrypt(
            $message,
            "aes-256-ctr",
            $key,
            OPENSSL_RAW_DATA,
            $nonce
        );

        return base64_encode($nonce.$ciphertext);
    }

    public static function Decrypt($message, $key)
    {
        $message = base64_decode($message, true);

        $nonceSize = openssl_cipher_iv_length("aes-256-ctr");
        $nonce = mb_substr($message, 0, $nonceSize, '8bit');
        $ciphertext = mb_substr($message, $nonceSize, null, '8bit');

        $plaintext = openssl_decrypt(
            $ciphertext,
            "aes-256-ctr",
            $key,
            OPENSSL_RAW_DATA,
            $nonce
        );

        return $plaintext;
    }
}

?>