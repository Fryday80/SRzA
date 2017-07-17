<?php
namespace Equipment\Model;


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