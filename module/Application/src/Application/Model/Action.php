<?php
namespace Application\Model;

class Action {
    public $url;
    public $userId;
    public $userName;
    public $time;
    public $data;

    public $actionType;
    public $title;
    public $msg;

    function __construct($time, $url, $userId, $userName, $actionType, $title, $msg, $data = null )
    {
        $this->time = $time;
        $this->url = $url;
        $this->userId = $userId;
        $this->userName = $userName;
        $this->actionType = $actionType;
        $this->title = $title;
        $this->msg = $msg;
        $this->data = $data;
    }

}