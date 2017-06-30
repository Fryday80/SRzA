<?php
namespace Equipment\Model;


class Equipment extends EquipmentStdDataItemModel
{

    public $itemType = EnumEquipTypes::EQUIPMENT;

    public $type;
    public $width;
    public $length;
    public $color;
}