<?php


namespace base\model;


use base\controller\traits\Singleton;

class Crypt
{
    use Singleton;

    private $cryptMethod = "AES-128-CBC";
    private $hashAlgorithm = 'sha256';
    private $hashLength = 32;

    public function encrypt($str)
    {
        $ivlen = openssl_cipher_iv_length($this->cryptMethod);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $cipherText = openssl_encrypt($str, $this->cryptMethod, CRYPT_KEY, OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac($this->hashAlgorithm, $cipherText, CRYPT_KEY, true);

        return base64_encode($iv . $hmac . $cipherText);
    }

    public function decrypt($str)
    {
        $crypt_str = base64_decode($str);
        $ivlen = openssl_cipher_iv_length($this->cryptMethod);
        $iv = substr($crypt_str, 0, $ivlen);
        $hmac = substr($crypt_str, $ivlen, $this->hashLength);
        $cipherText = substr($crypt_str, $ivlen + $this->hashLength);
        $originalText = openssl_decrypt($cipherText, $this->cryptMethod, CRYPT_KEY, OPENSSL_RAW_DATA, $iv);

        $calcmac = hash_hmac($this->hashAlgorithm, $cipherText, CRYPT_KEY, true);

        if (hash_equals($hmac, $calcmac)) {
            return $originalText;
        }
        return false;
    }

}