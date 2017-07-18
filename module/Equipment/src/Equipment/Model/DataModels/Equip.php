<?php
namespace Equipment\Model\DataModels;


use Equipment\Model\AbstractModels\AbstractEquipmentDataItemModel;
use Equipment\Model\Enums\EEquipTypes;

class Equip extends EquipmentStdDataItemModel
{

    public $itemType = EEquipTypes::EQUIPMENT;

    public $type = 'Equipment';
    public $description;
    public $sitePlannerObject;
    public $sitePlannerImage;
    public $length;
    public $width;
    public $image1;
    public $image2;
    public $color;

}