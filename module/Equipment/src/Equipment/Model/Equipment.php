<?php
namespace Equipment\Model;


class Equipment extends EquipmentStdDataItemModel
{

    public $itemType = EEquipTypes::EQUIPMENT;

    public $type;
    public $description;
    public $sitePlanner;
    public $color;
    public $length;
    public $width;

}