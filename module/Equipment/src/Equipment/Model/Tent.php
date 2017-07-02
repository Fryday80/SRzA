<?php

namespace Equipment\Model;

class Tent extends EquipmentStdDataItemModel
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

    public $itemType = EEquipTypes::TENT;
    public $name = 'Zelt';
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


    public function setData($data)
    {
        if ($data['biColor']== "0") $data['color2'] = $data['color1'];
        parent::setData($data);
    }
}