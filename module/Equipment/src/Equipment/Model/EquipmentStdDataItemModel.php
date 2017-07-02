<?php
namespace Equipment\Model;

use Application\Model\DataObjects\DataItem;

class EquipmentStdDataItemModel extends DataItem implements IEquipment
{
    public $name;
    public $itemType;
    public $image;

    public $userId;
    public $userName;
    

    public function getImage(){
        return $this->image;
    }

    public function getType(){
        return $this->itemType;
    }
    
    /**
     * is this group equip
     * @return bool
     */
    public function isGroupEquip()
    {
        if ($this->userId == 0) return true;
        return false;
    }
}