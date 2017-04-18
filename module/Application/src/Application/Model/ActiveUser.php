<?php

namespace Application\Model;


// structure:  table: system_log ( sid [string], ip [string], user_id [int], last_action_url[string], time [bigint], data [string|array|object] )
class ActiveUser
{
    public $sid;
    public $ip;
    public $userId;
    public $userName;
    public $lastActionUrl;
    public $time;
    public $data;
    public $expireTime;
    public $expires;

    function __construct($expireTime, $sid, $ip, $userId, $userName, $lastActionUrl, $time, $data = null)
    {
        $this->expireTime = $expireTime;
        $this->sid = $sid;
        $this->ip = $ip;
        $this->userId = $userId;
        $this->userName = $userName;
        $this->astActionUrl = $lastActionUrl;
        $this->time = $time;
        $this->data = $data;
        $this->expires = $time+$expireTime;
    }

    public function update($ip, $userId, $userName, $lastActionUrl, $time, $data = null)
    {
        $this->ip = $ip;
        $this->userId = $userId;
        $this->userName = $userName;
        $this->astActionUrl = $lastActionUrl;
        $this->time = $time;
        $this->data = $data;
        $this->expires = $time+$this->expireTime;
    }

    public function setExpireTime($exp){
        $this->expires = $exp;
    }
}