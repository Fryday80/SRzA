<?php

namespace Application\Model;


// structure:  table: system_log ( sid [string], ip [string], user_id [int], last_action_url[string], time [bigint], data [string|array|object] )
use Application\Model\BasicModels\StatsDataItem;

class ActiveUser
    extends StatsDataItem
{
    public $sid;
    public $ip;
    public $expireDuration;
    public $expires;

    /** userId used as itemId */
    function __construct($itemId, $url, $userId, $time, $sid, $ip, $userName, $expireDuration, $data = null)
    {
        parent::__construct($itemId, $url, $time, $userId, $data );
        $this->expireDuration = $expireDuration;
        $this->sid = $sid;
        $this->ip = $ip;
        $this->expires = $time+$expireDuration;
    }

    public function update($ip, $userId, $userName, $url, $time, $data = null)
    {
        $this->ip = $ip;
        $this->userId = $userId;
        $this->userName = $userName;
        $this->url = $url;
        $this->time = $time;
        $this->data = $data;
        $this->expires = $time+$this->expireDuration;
    }
}