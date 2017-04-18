<?php
namespace Application\Model;


class ActionsLog
{
    public $actionType; //string wie  loadPage, SystemLog, PageError ....
    public $title;
    public $msg;
    public $data;
    public $time; // bei loadPage die url, bei SystemLog die log msg ....
    public $userID;
    public $actionID;

    function __construct( $actionType, $title, $msg, $time, $userID, $data = null )
    {
        $this->actionID = uniqid();

        $this->actionType = $actionType;
        $this->title = $title;
        $this->msg = $msg;
        $this->time = $time;
        $this->userID = $userID;
        $this->data = $data;
    }

}