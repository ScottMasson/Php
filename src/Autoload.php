<?php
declare (strict_types = 1);
namespace scottmasson\elephant;
class Autoload
{

   public $rootPath;
   public $publicPath;
   public $smVendorPath;

   /**
    * Class constructor.
    */
   public function __construct()
   {
      $this->rootPath = realpath(dirname(__DIR__,4));
      $this->publicPath = $this->rootPath. DIRECTORY_SEPARATOR .'public';
      $this->smVendorPath = $this->rootPath . DIRECTORY_SEPARATOR . 'sm' . DIRECTORY_SEPARATOR . 'vendor';
   }
   public function ERROR(int $k)
   {
      try {
         return [
            'ERRORCODE' => $k,
            'ERRORMESSAGE' => [
            10010 => '内容格式错误!',
            10011 => 'IP地址格式错误',
         ][$k]];
      } catch (\Throwable ) {
         return 'The error number does not exist';
      }
   }
}