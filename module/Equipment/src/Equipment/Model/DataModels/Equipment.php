<?php
namespace Equipment\Model\DataModels;


use Equipment\Model\AbstractModels\EquipmentStdDataItemModel;
use Equipment\Model\Enums\EEquipTypes;

class Equipment extends EquipmentStdDataItemModel
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