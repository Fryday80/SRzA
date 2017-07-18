<?php
namespace Equipment\Model\AbstractModels;

use Application\Model\AbstractModels\AbstractModel;

class AbstractEquipmentDataItemModel extends AbstractModel
{
    /** @var  int */
    public $id;
    /** @var  int */
    public $itemType;
    /** @var  string path to img, used in SitePlanner*/
    public $image;
    /** @var  int */
    public $userId;
    /** @var  string */
    public $userName;
    /** @var  int used as bool */
    public $sitePlannerObject;
    /** @var  int depth in cm used to render in SitePlanner */
    public $depth;
    /** @var  int width in cm used to render in SitePlanner */
    public $width;
    public $shape;
    public $name;
    public $description;
    public $image1;
    public $image2;
    public $color1;
    public $biColor;
    public $color2;
    

    public function __construct($data = null)
    {
        if ($data !== null){
            foreach ($data as $key=>$value)
                $this->$key = $value;
        }
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

    // move to EquipmentResultSet ??
    public function metaDataUpdate($data)
    {
        $this->id = $data['id'];
        $this->itemType = ($data['item_type'] !== null) ? $data['item_type'] : $this->itemType;
        $this->image = ($data['image'] !== null) ? $data['image'] : $this->image;
        $this->userId = ($data['user_id'] !== null) ? $data['user_id'] : $this->userId;
        $this->sitePlannerObject = ($data['site_planner_object'] !== null) ? $data['site_planner_object'] : $this->sitePlannerObject;
        $this->userName = ($data['user_name'] !== null) ? $data['user_name'] : $this->userName;
    }


    // move to AbstractModel ??
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function toArray()
    {
        return $this->getArrayCopy();
    }
}