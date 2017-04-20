<?php

namespace Application\Model;


// structure:  table: system_log ( sid [string], ip [string], user_id [int], last_action_url[string], time [bigint], data [string|array|object] )
use Application\Model\BasicModels\StatsDataItem;

class ActiveUser
    extends StatsDataItem
{
    public $sid;
    public $ip;
    public $userName;
    public $expireTime;
    public $expires;

    /** userId used as itemId */
    function __construct($itemId, $url, $userId, $time, $sid, $ip, $userName, $expireTime, $data = null)
    {
        parent::__construct($userId, $url, $time, $userId, $data );
        $this->expireTime = $expireTime;
        $this->sid = $sid;
        $this->ip = $ip;
        $this->userName = $userName;
        $this->expires = $time+$expireTime;
    }

    public function update($ip, $userId, $userName, $url, $time, $data = null)
    {
        $this->ip = $ip;
        $this->userId = $userId;
        $this->userName = $userName;
        $this->url = $url;
        $this->time = $time;
        $this->data = $data;
        $this->expires = $time+$this->expireTime;
    }
}