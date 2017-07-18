<?php

namespace Application\Model\DataModels;

use Application\Model\AbstractModels\StatsDataItem;
use Application\Model\Enums\LogType;

class SystemLog extends StatsDataItem
{
    public $type;
    public $msg;
    public $url;
    public $userId;
    public $userName;

    /**
     * SystemLog constructor.
     * @param int $mTime
     * @param LogType $type
     * @param string $msg
     * @param string $url
     * @param int $userId
     * @param string $userName
     * @param array $data
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