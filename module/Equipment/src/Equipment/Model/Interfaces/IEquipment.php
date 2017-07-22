<?php
namespace Equipment\Model\Interfaces;

use Application\Model\Interfaces\IObjectToArray;

interface IEquipment extends IObjectToArray
{
	public function isGroupEquip();

	public function getArrayCopy();
}