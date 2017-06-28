<?php
namespace Equipment\Model;


use Application\Model\DataObjects\DataItem;

class DataItemEquipmentModel extends DataItem implements IEquipment
{
    public $name;
    public $itemType;
    public $image;

    public $userId;
    

    public function getImage(){
        return $this->image;
    }

    public function getType(){
        return $this->itemType;
    }
}