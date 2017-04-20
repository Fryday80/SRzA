<?php
namespace Application\Model;


use Application\Model\BasicModels\StatsDataItem;

class ActionsLog
    extends StatsDataItem
{
    public $itemId;
    public $url;
    public $userId;
    public $time;
    public $data;

    public $actionType;
    public $title;
    public $msg;

    function __construct( $itemId, $url, $time, $userId, $actionType, $title, $msg,  $data = null )
    {
        parent::__construct( $itemId, $url, $time, $userId, $data );

        $this->actionType = $actionType;
        $this->title = $title;
        $this->msg = $msg;
    }

}