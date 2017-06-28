<?php

namespace Application\Model\DataObjects;


class StatsDataItem
{
    public $microtime;
    public $time;
    public $data;
    
    function __construct($microtime, $data = null)
    {
        $this->microtime = $microtime;
        $this->time = (int)$microtime;
        $this->data = $data;
    }
    public function updateTime($mTime){

        $this->microtime = $mTime;
        $this->time = (int)$mTime;
    }
}