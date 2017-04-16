<?php
namespace Application\Model\DataObjects;

use Application\Model\DataObjects\BasicDashboardDataSet;

class Action extends BasicDashboardDataSet
{
    public $actionType; //string wie  loadPage, SystemLog, PageError ....
    public $title;
    public $msg;
    public $data;
    public $time; // bei loadPage die url, bei SystemLog die log msg ....
    public $user_id;

    //...
}