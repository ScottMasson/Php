<?php
declare (strict_types = 1);
namespace scottmasson\elephant\base;
use scottmasson\elephant\base\Arr;
use scottmasson\elephant\database\Geoip;
class Request
{
   public function domain(string $url = '') :object 
   {

      $url = $url === '' ? $_SERVER['HTTP_HOST'] : $url;

      $parse = parse_url($url);

      $scheme = '';
      $host = '';
      $top = '';
      $ip = '127.0.0.1';

      if (!empty($parse['host'])) {
         $array = explode('.',$parse['host']);
         if (count($array) === 3) {
            $scheme = $parse['scheme'];
            $host = $array[1].'.'.$array[2];
            $top = $parse['host'];
         }else{
            $host = $parse['host'];
         }
      }

      return (new Arr())->obj([
         'scheme' => $scheme,
         'host'   => $host,
         'top' => $top,
         'ip'  => gethostbyname($host)
      ]);
   }
   public function ip(string $ip = '',string $lang = 'zh-CN')
   {
      $arr = new Arr;

      if ($ip === '') {
         $ip = $_SERVER['REMOTE_ADDR'];
         if ($ip === '127.0.0.1') return $ip;
      }
      
      $verification = $arr->verification($ip);

      if ($verification->isIp() === false) return $arr->obj($arr->ERROR(10011));


      $geoip = (new Geoip)->internetProtocol($ip);

      switch ($lang) {
         case 'zh-CN':
            $zhCH = (new \scottmasson\elephant\database\IpipNet)->city($ip);
            $geoip['province']['names']['zh-CN'] = $zhCH[1];
            $geoip['city']['names']['zh-CN'] = $zhCH[2];
            break;
         
         default:
            # code...
            break;
      }

      return (new Arr)->obj($geoip);
   }
}