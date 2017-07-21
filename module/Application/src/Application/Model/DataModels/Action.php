<?php
namespace Application\Model\DataModels;

use Application\Model\AbstractModels\StatsDataItem;

class Action extends StatsDataItem
{
    public $url;
    public $userId;
    public $userName;

    public $actionType;
    public $title;
    public $msg;

    /**
     * Action constructor.
     * @param $mTime
     * @param $url
     * @param $userId
     * @param $userName
     * @param $actionType
     * @param $title
     * @param $msg
     * @param null $data
     */
    function __construct($mTime, $url, $userId, $userName, $actionType, $title, $msg, $data = null )
    {
        parent::__construct($mTime, $data);
        $this->url = $url;
        $this->userId = $userId;
        $this->userName = $userName;
        $this->actionType = $actionType;
        $this->title = $title;
        $this->msg = $msg;
    }

}