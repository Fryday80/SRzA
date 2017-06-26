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
    public $isGroupEquip = 0;
    public $color1;
    public $biColor;
    public $color2;

    public $readable;

    /**
     * Tent constructor.
     * @param array $data possible keys:
     * <br/>                            array (
     * <p style="margin-left: 15px">             'id' => int                                       </p>
     * <p style="margin-left: 15px">             'userId' => int                                   </p>
     * <p style="margin-left: 15px">             'shape' => int         { use EnumTentShape:: }    </p>
     * <p style="margin-left: 15px">             'width' => int         { in meters }              </p>
     * <p style="margin-left: 15px">             'length' => int        { in meters }              </p>
     * <p style="margin-left: 15px">             'spareBeds' => int                                </p>
     * <p style="margin-left: 15px">             'isShowTent' => int    { 0 = false, 1 = true }    </p>
     * <p style="margin-left: 15px">             'isGroupEquip' => int  { 0 = false, 1 = true }    </p>
     * <p style="margin-left: 15px">             'color' => int                                    </p>
     *                                  );
     */
    public function __construct($data = null)
    {
        if ($data !== null)
            $this->setData($data);
    }

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
     * <p style="margin-left: 15px">             'color' => int                                    </p>
     *                                  );
     */
    public function setData($data)
    {
        $oneToOne = array( 'id', 'color1', 'color2', 'biColor', 'shape', 'width', 'length', 'type');
        foreach ($data as $key => $value) {
            if (in_array($key, $oneToOne)) $this->$key = $value;
            if ($key == 'userId'       || $key == 'user_id') $this->userId = $value;
            if ($key == 'spareBeds'    || $key == 'spare_beds' ) $this->spareBeds = $value;
            if ($key == 'isShowTent'   || $key == 'is_show_tent' ) $this->isShowTent = $value;
            if ($key == 'isGroupEquip' || $key == 'is_group_equip' ) $this->isGroupEquip = $value;
        }
    }

}