<?php
declare (strict_types = 1);
namespace scottmasson\elephant\base;
use scottmasson\elephant\base\Arr;
use GeoIp2\Database\Reader;
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
   public function ip()
   {
      require_once("geoip/geoip2.phar");

      $domain = $this->domain('https://baidu.com/');

      // return $_SERVER['REMOTE_ADDR'];
      $reader = new Reader('/usr/local/var/www/frame/php/laravel/vendor/scottmasson/elephant/src/base/geoip/GeoLite2-City.mmdb');
      // $_SERVER['REMOTE_ADDR']
      $record = $reader->city($domain->ip);
      return [
         'isoCode'   => $record->country->isoCode,
         'name'   => $record->country->name,
         'names'  => $record->country->names
      ];
   }
}