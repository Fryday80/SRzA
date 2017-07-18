<?php
namespace Equipment\Model\AbstractModels;

use Application\Model\AbstractModels\AbstractModel;
use Equipment\Model\AbstractModels\AbstractEquipmentDataItemModel;

class EquipDBObject extends AbstractModel
{
    /** @var  int */
    public $id;
    /** @var  EquipmentStdDataItemModel */
    public $data;
    /** @var  int */
    public $type;
    /** @var  string path to img */
    public $image;
    /** @var  int */
    public $userId;
    /** @var  string */
    public $userName;
}