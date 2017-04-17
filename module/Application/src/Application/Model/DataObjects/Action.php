<?php
namespace Application\Model\DataObjects;


class Action
{
    public $actionType; //string wie  loadPage, SystemLog, PageError ....
    public $title;
    public $msg;
    public $data;
    public $time; // bei loadPage die url, bei SystemLog die log msg ....
    public $user_id;
    public $action_id;

    function __construct()
    {
        $this->id = microtime();
    }
}