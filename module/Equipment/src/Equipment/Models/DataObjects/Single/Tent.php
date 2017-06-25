<?php

namespace Equipment\Models\DataObjects\Single;

use Equipment\Models\Abstracts\TentShape;

class Tent
{
    public $ownerId;
    public $shape;
    public $width;
    public $length;
    public $spareBeds;
    public $isShowTent = 0;
    public $isGroupEquip = 0;

    /**
     * Tent constructor.
     * @param array $data possible keys:
     * <br/>                            array (
     * <p style="margin-left: 15px">            'ownerId' => int                                   </p>
     * <p style="margin-left: 15px">             'shape' => int         { use TentShape:: }        </p>
     * <p style="margin-left: 15px">             'width' => int         { in meters }              </p>
     * <p style="margin-left: 15px">             'length' => int        { in meters }              </p>
     * <p style="margin-left: 15px">             'spareBeds' => int                                 </p>
     * <p style="margin-left: 15px">             'isShowTent' => int    { 0 = false, 1 = true }    </p>
     * <p style="margin-left: 15px">             'isGroupEquip' => int  { 0 = false, 1 = true }    </p>
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
     * <p style="margin-left: 15px">            'ownerId' => int                                   </p>
     * <p style="margin-left: 15px">             'shape' => int         { use TentShape:: }        </p>
     * <p style="margin-left: 15px">             'width' => int         { in meters }              </p>
     * <p style="margin-left: 15px">             'length' => int        { in meters }              </p>
     * <p style="margin-left: 15px">             'spareBeds' => int                                 </p>
     * <p style="margin-left: 15px">             'isShowTent' => int    { 0 = false, 1 = true }    </p>
     * <p style="margin-left: 15px">             'isGroupEquip' => int  { 0 = false, 1 = true }    </p>
     *                                  );
     */
    public function setData(Array $data)
    {
        foreach ($data as $key => $value) {
            if ($key == 'ownerId') $this->$key = $value;
            if ($key == 'shape') $this->$key = $value;
            if ($key == 'width') $this->$key = $value;
            if ($key == 'length') $this->$key = $value;
            if ($key == 'spareBeds') $this->$key = $value;
            if ($key == 'isShowTent') $this->$key = $value;
            if ($key == 'isGroupEquip') $this->$key = $value;
        }
    }

    /**
     * @param int $shapeType use Tentshape::
     */
    public function setshapeType($shapeType)
    {
        $this->shapeType = $shapeType;
    }

    public function setSize($width, $lenght = null)
    {
        if ($this->shape == TentShape::ROUND || $this->shape == TentShape::SQUARE) {
            $this->width = $this->length = $width;
        } else {
            $this->width = $width;
            $this->length = $lenght;
        }
    }

}