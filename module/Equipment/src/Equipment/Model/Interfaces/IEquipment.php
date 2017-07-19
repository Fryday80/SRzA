<?php
namespace Equipment\Model\Interfaces;

use Application\Model\Interfaces\IToArray;

interface IEquipment extends IToArray
{
	public function isGroupEquip();

	public function getArrayCopy();
}