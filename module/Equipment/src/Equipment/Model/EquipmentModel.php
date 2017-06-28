<?php
namespace Equipment\Model;


use Traversable;

abstract class EquipmentModel
{
    public function toArray(){
        return get_object_vars($this);
    }

    public function setData($data)
    {
        
    }
}