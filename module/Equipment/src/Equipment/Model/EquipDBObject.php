<?php

namespace Equipment\Model;


use Application\Model\AbstractModel;

class EquipDBObject extends AbstractModel
{
    /** @var  int */
    public $id;
    /** @var  EquipmentStdDataItemModel */
    public $data;
    /** @var  int */
    public $type;
    /** @var  string path to img */
    public $image;
    /** @var  int */
    public $userId;
    /** @var  string */
    public $userName;

//    public function __construct()
//    {
//    }

//    public function setId($value){
//        $this->id = (int) $value;
//    }
//    public function setData($value){
//        $this->data = $value;
//    }
//    public function setType($value){
//        $this->type = (int) $value;
//    }
//    public function setImage($value){
//        $this->image = $value;
//    }
//    public function setUser_id($value){
//        $this->user_id = (int) $value;
//    }
//    public function setUser_name($value){
//        $this->user_id = $value;
//    }

}