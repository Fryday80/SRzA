<?php
namespace Equipment\Model;


interface IEquipment
{
    public function getImage();

    public function getType();

    public function toArray();

}