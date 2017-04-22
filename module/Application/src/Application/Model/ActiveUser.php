<?php

namespace Application\Model;

class ActiveUser
{
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

    function __construct($userId, $userName, $time, $sid, $ip, $url, $data = null)
    {
        $this->userId = $userId;
        $this->userName = $userName;
        $this->sid = $sid;
        $this->ip = $ip;
        $this->url = $url;
        $this->data = $data;
        $this->time = $time;
    }
}