<?php
namespace Equipment\Model\DataModels;

use Equipment\Model\AbstractModels\AbstractEquipmentDataItemModel;
use Equipment\Model\Enums\EEquipTypes;
use Equipment\Model\Enums\ETentShape;

class Tent extends AbstractEquipmentDataItemModel
{
    public $itemType = EEquipTypes::TENT;
    public $name = 'Zelt';

    public $shape;
    public $spareBeds;
    public $isShowTent = 0;
    public $sitePlannerObject = 1;

    public function __construct($data)
    {
        parent::__construct($data);
        $this->image = ETentShape::IMAGES[$this->shape];
    }
}