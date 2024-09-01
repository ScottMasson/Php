<?php
declare (strict_types = 1);
namespace scottmasson\elephant\base;
class Date{
    /**
     * Class constructor.
     */
    public function __construct(string|int $timing)
    {
        $this->timing = $timing;
    }
    public function lastDay()
    {
        $unix = strlen((int)$this->timing) === 10?$this->timing:strtotime($this->timing);
        return [
            'day'   =>  date('t',$unix),
            'unix'  =>  $unix
        ];
    }
}