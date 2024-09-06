<?php
namespace scottmasson\elephant\algorithm;
use scottmasson\elephant\algorithm\JSONWebToken\JWT;
use scottmasson\elephant\base\Arr;
class JSONWebToken
{
    public function result(array $options = [])
    {

        $Arr = new Arr;

        $options = 
        $Arr->obj($Arr->merge([],[
            'direction' =>  true,
            'bytes' =>  '',
            'key'   =>  getenv('SM_PRIVATE_KEY'),
            'alg'   =>  'HS256'
        ],$options));

        if ($options->direction) {
            return JWT::encode($options->bytes, $options->key, $options->alg);
        } else {
            return JWT::decode($options->bytes, $options->key, array($options->alg));
        }
    }
}