<?php

namespace Application\Model;


class SystemLog
{
    public $time;
    public $type;
    public $msg;
    public $url;
    public $userId;
    public $userName;
    public $data;

    function __construct($type, $msg, $url, $userId, $userName, $data = null)
    {
        $this->time = microtime(true);
        $this->type = $type;
        $this->msg = $msg;
        $this->url = $url;
        $this->userId = $userId;
        $this->userName = $userName;
        $this->daty = $data;
    }
}