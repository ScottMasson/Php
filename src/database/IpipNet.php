<?php 
declare (strict_types = 1);
namespace scottmasson\elephant\database;
use scottmasson\elephant\database\ipipnet\City as ipdbCity;
use scottmasson\elephant\base\Arr;
class IpipNet
{
    public function city(string $ip)
    {

        if ((new Arr)->verification($ip)->isIp() === false) return false;

        return (new ipdbCity(__DIR__.'/ipipnet/free.ipdb'))->find($ip,'CN');
    }
}