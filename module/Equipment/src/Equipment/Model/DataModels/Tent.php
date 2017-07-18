<?php
namespace Equipment\Model\DataModels;

use Equipment\Model\AbstractModels\AbstractEquipmentDataItemModel;
use Equipment\Model\Enums\EEquipTypes;

class Tent extends EquipmentStdDataItemModel
{
    public $itemType = EEquipTypes::TENT;
    public $name = 'Zelt';
    public $image;

    public $id;
    public $userId;
    public $shape;
    public $type;
    public $width;
    public $length;
    public $spareBeds;
    public $isShowTent = 0;
    public $color1;
    public $biColor;
    public $color2;
    public $sitePlannerObject = 1;
}