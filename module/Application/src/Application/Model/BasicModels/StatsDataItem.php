<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 20.04.2017
 * Time: 03:07
 */

namespace Application\Model\BasicModels;


class StatsDataItem
{
    public $itemId;
    public $url;
    public $userId;
    public $time;
    public $data;

    function __construct($itemId, $url, $time, $userId, $data = null)
    {
        $this->itemId = $itemId;
        $this->url = $url;
        $this->userId = $userId;
        $this->time = $time;
        $this->data = $data;
    }

}