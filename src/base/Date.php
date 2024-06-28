<?php
namespace sm\elephant\base;
class Date{
    public function LastDay($date){
        $unix = strlen((int)$date) === 10?$date:strtotime($date);
        return [
            'day'   =>  date('t',$unix),
            'unix'  =>  $unix
        ];
    }
}