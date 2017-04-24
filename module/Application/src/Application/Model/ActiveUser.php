<?php

namespace Application\Model;

class ActiveUser
{
    public $id;
    public $sid;     
    public $userId;  
    public $userName;
    public $time;    
    public $data;    
    
    public $ip;
    /**
     * @var string $url
     */
    public $url;

    /**
     * ActiveUser constructor.
     * @param $userId
     * @param $userName
     * @param $mTime microtime()
     * @param $sid
     * @param $ip
     * @param $url
     * @param mixed $data
     */
    function __construct($userId, $userName, $mTime, $sid, $ip, $url, $data = null)
    {
        $this->userId = $userId;
        $this->userName = $userName;
        $this->sid = $sid;
        $this->ip = $ip;
        $this->url = $url;
        $this->data = $data;
        $this->time = (int)$mTime;
        $this->id = $mTime*10000;
    }
}