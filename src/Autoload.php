<?php
declare (strict_types = 1);
namespace scottmasson\elephant;
class Autoload
{
   /**
    * Class constructor.
    */
   public function __construct()
   {
      $this->rootPath = realpath(dirname(__DIR__,4));
      $this->publicPath = realpath(dirname(__DIR__,4)). DIRECTORY_SEPARATOR .'public';
   }
   public function ERROR(int $k)
   {
      try {
         return [
            'ERRORCODE' => $k,
            'ERRORMESSAGE' => [
            10010 => '内容格式错误!'
         ][$k]];
      } catch (\Throwable ) {
         return 'The error number does not exist';
      }
   }
}