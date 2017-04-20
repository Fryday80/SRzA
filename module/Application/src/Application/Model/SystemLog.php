<?php

namespace Application\Model;


use Application\Model\BasicModels\StatsDataItem;

class SystemLog
    extends StatsDataItem
{
    public $type;
    public $msg;

    function __construct( $itemId, $url, $time, $type, $msg, $userId, $userName, $data = null)
    {
        parent::__construct($itemId, $url, $time, $userId, $userName, $data);
        $this->type = $type;
        $this->msg = $msg;
    }
}