<?php

namespace Application\Model;


class SystemLog
{
    private $id;
    public $type;
    public $time;
    public $msg;
    public $userId;
    public $data;
    
    function __construct($id, $type, $time, $msg, $userId, $data)
    {
        $this->id = $id;
        $this->type = $type;
        $this->time = $time;
        $this->msg = $msg;
        $this->userId = $userId;
        $this->data = $data;
    }
    
    public function getId(){
        return $this->id;
    }

}