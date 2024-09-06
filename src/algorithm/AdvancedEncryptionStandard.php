<?php 
namespace scottmasson\elephant\algorithm;
class AdvancedEncryptionStandard
{
    public function result(bool $direction,$bytes,string $token = '',$salt = null){
        $token = $token === ''? getenv('SM_PUBLIC_KEY') : $token;
        return $direction === true?
        $this->encrypt($bytes, $token, $salt):
        $this->decrypt($bytes, $token);
    }
    protected function encrypt($bytes, $token, $salt = null) {
        if (is_array($bytes)) $bytes = json_encode($bytes);
        $salt = $salt ?: openssl_random_pseudo_bytes(8);
        list($key, $iv) = $this->evpkdf($token, $salt);
        $ct = openssl_encrypt($bytes, 'aes-256-cbc', $key, true, $iv);
        return $this->encode($ct, $salt);
    }
    protected function decrypt($encryption, $token) {
        list($ct, $salt) = $this->decode($encryption);
        list($key, $iv) = $this->evpkdf($token, $salt);
        $bytes = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
        
        // 处理json
        $array = json_decode($bytes,true);
        return $array === null?$bytes:$array;
    }		
    
    protected function evpkdf($token, $salt) {
        $salted = '';
        $dx = '';
        while (strlen($salted) < 48) {
            $dx = md5($dx . $token . $salt, true);
            $salted .= $dx;
        }
        $key = substr($salted, 0, 32);
        $iv = substr($salted, 32, 16);
        return [$key, $iv];
    }		
    
    protected function decode($base64) {
        $bytes = base64_decode($base64);
        if (substr($bytes, 0, 8) !== "Salted__") {
            return;
        }
        $salt = substr($bytes, 8, 8);
        $ct = substr($bytes, 16);
        return [$ct, $salt];
    }

    protected function encode($ct, $salt) {
        return base64_encode("Salted__" . $salt . $ct);
    }
}
