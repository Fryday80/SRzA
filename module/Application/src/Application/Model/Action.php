<?php
namespace Application\Model;

class Action {
    public $itemId;
    public $url;
    public $userId;
    public $time;
    public $data;

    public $actionType;
    public $title;
    public $msg;

    function __construct($url, $userId, $actionType, $title, $msg, $data = null )
    {
        $this->itemId = uniqid();
        $this->time = microtime(true);
        $this->url = $url;
        $this->userId = $userId;
        $this->actionType = $actionType;
        $this->title = $title;
        $this->msg = $msg;
        $this->data = $data;
    }

}