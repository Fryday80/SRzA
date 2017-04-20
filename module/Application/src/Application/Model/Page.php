<?php
namespace Application\Model;


use Application\Model\BasicModels\StatsDataItem;

class Page
    extends StatsDataItem
{
    public $count;
    
    function __construct($itemId, $url, $time, $userId, $data = null)
    {
        parent::__construct($itemId, $url, $time, $userId, $data);
        $this->count = 1;
    }

    public function update($time, $lastUserId, $data = null)
    {
        $this->time = $time;
        $this->userId = $lastUserId;
        $this->data = $data;
        $this->count++;
    }
}