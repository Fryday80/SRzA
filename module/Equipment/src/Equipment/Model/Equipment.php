<?php
namespace Equipment\Model;


class Equipment extends EquipmentStdDataItemModel
{

    public $itemType = EEquipTypes::EQUIPMENT;

    public $type;
    public $description;
    public $sitePlannerObject;
    public $color;
    public $length;
    public $width;

}