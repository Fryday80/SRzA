<?php
namespace Equipment\Model\AbstractModels;

use Application\Model\AbstractModels\AbstractModel;

class AbstractEquipmentDataItemModel extends AbstractModel
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
    /** @var  int used as bool */
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