<?php 
declare (strict_types = 1);
namespace scottmasson\elephant\database;
require_once('geoip/geoip2.phar');
use scottmasson\elephant\base\Arr;
use GeoIp2\Database\Reader;
class Geoip
{
    public function __construct()
    {
        $this->geoip2 = new Reader(__DIR__.DIRECTORY_SEPARATOR.'geoip/GeoLite2-City.mmdb');
    }
    public function internetProtocol(string $ip):array
    {

        $record = $this->geoip2->city($ip);

        return [
            'continent' =>  [
                'name'  =>  $record->continent->name,
                'names' =>  $record->continent->names,
                'code'  =>  $record->continent->code
            ],
            'country'   =>  [
                'name'  =>  $record->country->name,
                'names' =>  $record->country->names,
                // 是否加入欧盟
                'isInEuropeanUnion' =>  $record->country->isInEuropeanUnion,
                // iso code
                'code'  =>  $record->country->isoCode,
            ],
            'province'  =>  [
                'names' =>  []
            ],
            'city'  =>  [
                'name'  =>  $record->city->name,
                'names' =>  $record->city->names,
                // iso code
                'code'  =>  $record->mostSpecificSubdivision->isoCode,
            ],
            'location'  =>  [
                // [地理]纬度
                'latitude'  =>  $record->location->latitude,
                // [地理]经度
                'longitude'  =>  $record->location->longitude,
                // 时区
                'timeZone'  =>  $record->location->timeZone
            ],
            'traits'    =>  [
                'ipAddress' =>  $record->traits->ipAddress,
                'network'   =>  $record->traits->network
            ]
            
        ];
    }
}
