<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 25.04.2017
 * Time: 15:26
 */

namespace Application\Model;


class DataItem
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