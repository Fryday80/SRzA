<?php

namespace Application\Model;

class ActiveUser
{
    public $sid;        // unique
    public $userId;     // unique if not Guest  // updates if log in action
    public $userName;
    public $time;       // unique due microtime // updates onDispatch
    public $data;       // optional             // updates onDispatch
    public $ip;
    public $url;                                // updates onDispatch

    function __construct($userId, $userName, $sid, $ip, $url, $data = null)
    {
        $this->userId = $userId;
        $this->userName = $userName;
        $this->sid = $sid;
        $this->ip = $ip;
        $this->url = $url;
        $this->data = $data;
        $this->time = microtime(true) * 1000;
    }
}