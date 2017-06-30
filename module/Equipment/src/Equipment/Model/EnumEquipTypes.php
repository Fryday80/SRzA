<?php
namespace Equipment\Model;

abstract class EnumEquipTypes
{
    const TENT = 0;
    const EQUIPMENT = 1;

    const EQUIP_ITEM_CLASS = array(
        0 => \Equipment\Model\Tent::class,
        1 => \Equipment\Model\Equipment::class,
    );
    
    const TRANSLATE_TO_ID = array(
        'tent' => 0,
        'equipment' => 1
    );
    const TRANSLATE_TO_STRING = array(
        0 => 'tent',
        1 => 'equipment'
    );
}