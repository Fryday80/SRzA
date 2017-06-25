<?php

namespace Equipment\Model;

use Equipment\Model\EnumTentShape;

class Tent
{
    public $id = null;
    public $userId;
    public $shape;
    public $type;
    public $width;
    public $length;
    public $spareBeds;
    public $isShowTent = 0;
    public $isGroupEquip = 0;
    public $color;

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
    public function __construct($data)
    {
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
    public function setData(Array $data)
    {
        foreach ($data as $key => $value) {
            if ($key == 'id') $this->$key = $value;
            if ($key == 'color') $this->$key = $value;
            if ($key == 'userId') $this->$key = $value;
            if ($key == 'shape') $this->$key = $value;
            if ($key == 'width') $this->$key = $value;
            if ($key == 'length') $this->$key = $value;
            if ($key == 'spareBeds') $this->$key = $value;
            if ($key == 'isShowTent') $this->$key = $value;
            if ($key == 'isGroupEquip') $this->$key = $value;
        }
    }

    /**
     * @param int $shapeType use EnumTentShape::
     */
    public function setshapeType($shapeType)
    {
        $this->shape = $shapeType;
    }

    public function setSize($width, $length = null)
    {
        if ($this->shape == EnumTentShape::ROUND) {
            $this->width = $this->length = $width;
        } else {
            $this->width = $width;
            $this->length = $length;
        }
    }

}