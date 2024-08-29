<?php
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

   /** 
    ** Run load package
    *? @date 24/06/29 16:41
    *  @param array $nodelist The node list supports multiple levels
    *! @return Array object
    */
   public function pack(
      array $nodelist)
   {
         $apps = 
         [
            'base' => 
            [
                'Arr' => $this->baseArr(),
                'Str' => new base\Str,
                'Request' => new base\Request,
                'Date' => new base\Date
            ],
            'bytes'  => 
            [
               'File'   => new bytes\File,
               'Image'   => new bytes\Image
            ],
            'verification' => 
            [
               'Regex'  => new verification\Regex
            ],
            'algorithm' => 
            [
               'AdvancedEncryptionStandard'  => new algorithm\AdvancedEncryptionStandard,
               'Int4Bits32'   => new algorithm\Int4Bits32
            ]
         ];

         foreach ($nodelist as $key => $value) {
               if (is_array($value)) {
                  foreach ($value as $kc => $vc) {
                     if (is_array($vc)) {
                        foreach ($vc as $vp) {
                           $app[$key][$kc][$vp] = $apps[$key][$kc][$vp];
                        }
                     }else{
                        $app[$key][$vc] = $apps[$key][$vc];
                     }
                  }
               }else{
                  $app[$value] = $apps[$value];
               } 
         }
         return $this->baseArr()->obj($app);
   }
   /** 
    ** Load configuration file
    *? @date 24/06/29 16:41
    *  @param string $operationType Data processing operations such as path, get, set
    *  @param array $data Data to be saved
    *! @return array|string|boolean
    */
   public function config(
      $operationType = 'get',
      array $data = [])
   {
      $configJsonPath =  $this->rootPath.'/scottmasson.json';

      if ($operationType === 'path') return $configJsonPath;
      // return Array
      if ($operationType === 'get') return json_decode(file_get_contents($configJsonPath),true);
      // set JSON
      if ($operationType === 'set') file_put_contents($configJsonPath,json_encode($data));

      // Gets the specified node data
      if (is_string($operationType)) 
      {
         $operationType = explode('::',$operationType);
         if ($operationType[0] === 'get') 
         {
            unset($operationType[0]);
            return $this->baseArr()->findLadderNode($this->config('get'),$operationType);
         }
         return false;
      }

      if (is_object($operationType)) 
      {
         $data = json_encode($operationType);
         file_put_contents($configJsonPath,$data);
         return $data;
      }
      return false;
   }



   protected function baseArr()
   {
      return new base\Arr;
   }
}