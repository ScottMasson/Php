<?php
namespace scottmasson\elephant;
class App extends Autoload
{
    public function __construct()
    {
        $this->autoload = new Autoload();
    }
    public function initialize(
        array $nodelist)
    {
        return $this->autoload->pack($nodelist);
    }
}