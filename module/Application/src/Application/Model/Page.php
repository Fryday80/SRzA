<?php
namespace Application\Model;


class Page
{
    public $url;
    public $lastUserId;
    public $time;
    public $count;
    public $data;

    function __construct($url, $time, $lastUserId, $data = null)
    {
        $this->url = $url;
        $this->time = $time;
        $this->lastUserId = $lastUserId;
        $this->data = $data;
    }

    public function update($time, $lastUserId){
        $this->time = $time;
        $this->lastUserId = $lastUserId;
        $this->count++;
    }
}