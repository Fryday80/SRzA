<?php

namespace Application\Model\DataObjects;


class SystemLog extends DataItem
{
    public $type;
    public $msg;
    public $url;
    public $userId;
    public $userName;

    /**
     * SystemLog constructor.
     * @param $mTime
     * @param null $type
     * @param $msg
     * @param $url
     * @param $userId
     * @param $userName
     * @param null $data
     */
    function __construct($mTime, $type, $msg, $url, $userId, $userName, $data = null)
    {
        parent::__construct($mTime, $data);
        $this->type = $type;
        $this->msg = $msg;
        $this->url = $url;
        $this->userId = $userId;
        $this->userName = $userName;
    }
}