<?php 
namespace scottmasson\elephant\algorithm;
use scottmasson\elephant\algorithm\Int4Bits32\Skip32;
class Int4Bits32 
{
    /** 
     ** Currently Skip Max is encrypted to 4294967295 (Solved now unlimited encryption)
     *? @date 24/08/29 19:32
     */
    const MAX_NUMBER = 4294967295;
    /** 
     ** The current maximum fill of 4294967295
     *? @date 24/08/29 19:32
     */
    const MAX_NUMBER_LENGTH = '0000000000';

    public function result(bool $direction,$integer = 0,string $token = 'SM_INT4BITS32_DEFAULT')
    {
        $this->token = getenv($token);
        $this->integer = $integer;
        return $direction === true ? $this->encrypt() : $this->decrypt();
    }
    protected function encrypt()
    {
        // Overflow (cannot be started while cryptographic calculations are being performed) : A filling mechanism is selected when oversize values are encountered
        $multiple = (int)$this->integer/self::MAX_NUMBER;
        if ($multiple > 1) {
           // Acquisition multiple
           $multiple = (int)ceil($multiple);
  
           // Assume that the numbers you want to encrypt are: 4294967295*20 + 10;  $e = e20;
           $e = '00000'.(string)($multiple - 1).'00000';
  
           // Overflow: 10
           $overflow = self::MAX_NUMBER - abs(self::MAX_NUMBER*$multiple - $this->integer);
           // Encryption overflow
           $env = Skip32::encrypt($this->token,$overflow);
           
           // Fill 0: If there are less than 10 digits after encryption, fill to 10 digits
           $env = substr_replace(self::MAX_NUMBER_LENGTH,$env,-strlen($env));

           return $e.$env;
        }else{
           return Skip32::encrypt($this->token,$this->integer);
        }
    }
    protected function decrypt()
    {
        // The overflow exceeds the value 4294967295
        if (strlen($this->integer) > 10) {
           // EN Number
           $encrypt = substr($this->integer,-10);
           // Multiple of overflow
           $overflow = substr($this->integer,5,-15);
           return $overflow*self::MAX_NUMBER + Skip32::decrypt($this->token,$encrypt);
        }else{
           return (int)Skip32::decrypt($this->token,$this->integer);
        }
    }
}
