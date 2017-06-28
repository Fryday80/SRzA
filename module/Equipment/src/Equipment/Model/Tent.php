<?php

namespace Equipment\Model;

class Tent extends DataItemEquipmentModel
{
    protected $dbColumns = array(
        'user_id',
        'shape',
        'type',
        'color1',
        'bi_color',
        'color2',
        'width',
        'length',
        'spare_beds',
        'is_show_tent',
    );

    public $name = 'Zelt';
    public $itemType;
    public $image;

    public $id;
    public $userId;
    public $shape;
    public $type;
    public $width;
    public $length;
    public $spareBeds;
    public $isShowTent = 0;
    public $color1;
    public $biColor;
    public $color2;

    // readables
    public $readableUser;
    public $readableShape;
    public $readableType;
    public $shapeImg;
    public $colorField;
    public $isShowTentValue;

    /**
     * is this group equip
     * @return bool
     */
    public function isGroupEquip()
    {
        if ($this->userId == 0) return true;
        return false;
    }

    public function setData($data)
    {
        if ($data['biColor']== "0") $data['color2'] = $data['color1'];
        parent::setData($data);
    }
}