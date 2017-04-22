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

    /**
     * SystemLog constructor.
     * @param $type
     * @param $msg
     * @param $url
     * @param $userId
     * @param $userName
     * @param null $data
     */
    function __construct($time, $type, $msg, $url, $userId, $userName, $data = null)
    {
        $this->time = $time;
        $this->type = $type;
        $this->msg = $msg;
        $this->url = $url;
        $this->userId = $userId;
        $this->userName = $userName;
        $this->daty = $data;
    }
}