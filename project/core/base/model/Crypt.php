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

    protected function cryptCombine($str, $iv, $hmac)
    {
        $new_str = '';
        $str_len = strlen($str);
        $counter = (int)ceil(strlen(CRYPT_KEY) / ($str_len + strlen($hmac)));
        $progress = 1;

        if ($counter >= $str_len) $counter = 1;
        
        for ($i = 0; $i < $str_len; $i++) {

            if ($counter < $str_len) {

                if ($counter === $i) {
                    $new_str .= substr($iv, $progress - 1, 1);
                    $progress++;
                    $counter += $progress;
                }

            } else {
                break;
            }
            $new_str .= substr($str, $i, 1);
        }
    }

}