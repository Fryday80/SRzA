<?php
namespace Application\Model;

class Action {
    public $id;
    public $url;
    public $userId;
    public $userName;
    public $time;
    public $data;

    public $actionType;
    public $title;
    public $msg;

    /**
     * Action constructor.
     * @param $mTime microtime()
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
        $this->id = $mTime*10000;
        $this->time = (int)$mTime;
        $this->url = $url;
        $this->userId = $userId;
        $this->userName = $userName;
        $this->actionType = $actionType;
        $this->title = $title;
        $this->msg = $msg;
        $this->data = $data;
    }

}