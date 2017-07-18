<?php
namespace Equipment\Model\DataModels;


use Equipment\Model\AbstractModels\AbstractEquipmentDataItemModel;
use Equipment\Model\Enums\EEquipTypes;

class Equip extends AbstractEquipmentDataItemModel
{
    public $itemType = EEquipTypes::EQUIPMENT;

    public $type = 'Equipment';
}