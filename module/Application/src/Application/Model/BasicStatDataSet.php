<?php

namespace Application\Model;

class BasicStatDataSet
{
    protected $liveUserId;
    protected $liveUserName;

    protected function userId(){
        return $this->liveUserId;
    }
    protected function userName() {
        return $this->liveUserName;
    }
    public function setUserId($id){
        $this->liveUserId = $id;
    }
    public function setUserName($name) {
        $this->liveUserName = $name;
    }
}