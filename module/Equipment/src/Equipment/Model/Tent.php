<?php

namespace Equipment\Model;

use Auth\Service\UserService;
use Equipment\Model\EnumTentShape;

class Tent
{
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
     * @param array $data possible keys:
     * <br/>                            array (
     * <p style="margin-left: 15px">             'id' => int                                       </p>
     * <p style="margin-left: 15px">             'userId' => int                                   </p>
     * <p style="margin-left: 15px">             'shape' => int         { use TentShape:: }        </p>
     * <p style="margin-left: 15px">             'type' => int                                     </p>
     * <p style="margin-left: 15px">             'width' => int         { in meters }              </p>
     * <p style="margin-left: 15px">             'length' => int        { in meters }              </p>
     * <p style="margin-left: 15px">             'spareBeds' => int                                </p>
     * <p style="margin-left: 15px">             'isShowTent' => int    { 0 = false, 1 = true }    </p>
     * <p style="margin-left: 15px">             'isGroupEquip' => int  { 0 = false, 1 = true }    </p>
     * <p style="margin-left: 15px">             'color1' => string     { hexcolor #123456 }       </p>
     * <p style="margin-left: 15px">             'biColor' => int       { 0 = false, 1 = true }    </p>
     * <p style="margin-left: 15px">             'color2' => string     { hexcolor #123456 }       </p>
     *                                  );
     */
    public function __construct($data = null)
    {
        if ($data !== null)
            $this->setData($data);
    }

    /**
     * get Data as array
     * @return array
     */
    public function getData()
    {
        return get_object_vars($this);
    }

    /**
     * @param array $data possible keys:
     * <br/>                            array (
     * <p style="margin-left: 15px">             'id' => int                                       </p>
     * <p style="margin-left: 15px">             'userId' => int                                   </p>
     * <p style="margin-left: 15px">             'shape' => int         { use TentShape:: }        </p>
     * <p style="margin-left: 15px">             'type' => int                                     </p>
     * <p style="margin-left: 15px">             'width' => int         { in meters }              </p>
     * <p style="margin-left: 15px">             'length' => int        { in meters }              </p>
     * <p style="margin-left: 15px">             'spareBeds' => int                                </p>
     * <p style="margin-left: 15px">             'isShowTent' => int    { 0 = false, 1 = true }    </p>
     * <p style="margin-left: 15px">             'isGroupEquip' => int  { 0 = false, 1 = true }    </p>
     * <p style="margin-left: 15px">             'color1' => string     { hexcolor #123456 }       </p>
     * <p style="margin-left: 15px">             'biColor' => int       { 0 = false, 1 = true }    </p>
     * <p style="margin-left: 15px">             'color2' => string     { hexcolor #123456 }       </p>
     *                                  );
     */
    public function setData($data)
    {
        $oneToOne = array( 'id', 'color1', 'color2', 'shape', 'width', 'length', 'type');
        foreach ($data as $key => $value) {
            if (in_array($key, $oneToOne)) $this->$key = $value;
            if ($key == 'userId'     || $key == 'user_id')       $this->userId = $value;
            if ($key == 'spareBeds'  || $key == 'spare_beds' )   $this->spareBeds = $value;
            if ($key == 'isShowTent' || $key == 'is_show_tent' ) $this->isShowTent = $value;
            if ($key == 'biColor'    || $key == 'bi_color' )     $this->biColor = $value;
        }
    }

    /**
     * is this group equip
     * @return bool
     */
    public function isGroupEquip()
    {
        if ($this->userId == 0) return true;
        return false;
    }
}