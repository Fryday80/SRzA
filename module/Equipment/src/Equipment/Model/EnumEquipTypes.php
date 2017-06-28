<?php
namespace Equipment\Model;

abstract class EnumEquipTypes
{
    const TENT = 0;
    const TENT_TYPES = 1;
    const EQUIP_ITEM_CLASS = array(
        0 => \Equipment\Model\Tent::class,
        1 => \Equipment\Model\DataItemEquipmentModel::class,
    );
}