<?php

namespace Application\Model;

class ActiveUser extends DataItem
{
    public $firstCall;
    public $userId;  
    public $userName;
    
    public $ip;
    /**
     * @var string $url
     */
    public $url;

    /**
     * ActiveUser constructor.
     * @param int $userId
     * @param string $userName
     * @param float $mTime microtime(true)
     * @param string $ip
     * @param string $url
     * @param mixed $data
     */
    function __construct($userId, $userName, $mTime, $ip, $url, $data = null)
    {
        parent::__construct($mTime, $data);
        $this->firstCall = $mTime;
        $this->userId = $userId;
        $this->userName = $userName;
        $this->ip = $ip;
        $this->url = $url;
    }
}