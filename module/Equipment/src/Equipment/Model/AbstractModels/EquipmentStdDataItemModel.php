<?php
namespace Equipment\Model\AbstractModels;

use Application\Model\AbstractModels\AbstractModel;

class EquipmentStdDataItemModel extends AbstractModel
{
    public $id;
    public $itemType;
    public $image;
    public $userId;
    public $userName;
    public $sitePlannerObject;
    

    public function __construct($data = null)
    {
        if ($data !== null)
        foreach ($data as $key=>$value)
            $this->$key = $value;

    }

    public function updateFromDB($data)
    {
        $data['user_name'] = ((int)$data['user_id'] == 0) ? 'Verein' : $data['user_name'];
        $this->id = $data['id'];
        $this->userId = $data['user_id'];
        $this->userName = $data['user_name'];
        $this->sitePlannerObject = $data['site_planner_object'];
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

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function toArray()
    {
        return $this->getArrayCopy();
    }
}