<?php

namespace Application\Model\DataModels;

use Application\Model\AbstractModels\StatsDataItem;
use Application\Model\Enums\LogType;

class SystemLog extends StatsDataItem
{
	/** @var int */
    public $type;
    /** @var string  */
	public $msg;
	/** @var string  */
    public $url;
    /** @var int  */
    public $userId;
    /** @var string */
    public $userName;

    /**
     * SystemLog constructor.
     * @param float $mTime
     * @param int LogType $type
     * @param string $msg
     * @param string $url
     * @param int $userId
     * @param string $userName
     * @param array $data
     */
    function __construct($mTime, $type, $msg, $url, $userId, $userName, $data = null)
    {
        parent::__construct($mTime, $data);
        $this->type = (int) $type;
        $this->msg = $msg;
        $this->url = $url;
        $this->userId = (int) $userId;
        bdump($userName);
        $this->userName = ($userName == "") ? "unknown / guest" : $userName;
    }
}