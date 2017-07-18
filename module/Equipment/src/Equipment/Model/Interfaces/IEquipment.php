<?php
namespace Equipment\Model\Interfaces;

interface IEquipment
{
    public function getImage();

    public function getType();

    public function toArray();

}