<?php

namespace Application\Model;

class ActiveUser extends DataItem
{
    public $sid;
    public $userId;  
    public $userName;
    
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
    function __construct($userId, $userName, $mTime, $ip, $url, $data = null)
    {
        parent::__construct($mTime, $data);
        $this->userId = $userId;
        $this->userName = $userName;
        $this->ip = $ip;
        $this->url = $url;
    }
}