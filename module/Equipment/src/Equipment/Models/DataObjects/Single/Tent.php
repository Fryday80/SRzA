<?php

namespace Equipment\Models\DataObjects\Single;

use Equipment\Models\Abstracts\TentShape;

class Tent
{
    public $shapeType;
    public $shape = false;
    public $width;
    public $length;

    public function __construct($data)
    {
        $this->setData($data);
    }

    public function getData()
    {
        return array(
            'shapeType' => $this->shapeType,
            'shape'     => $this->shape, 
            'width'     => $this->width,
            'length'    => $this->length
        );
    }

    public function setData($data)
    {
        foreach ($data as $key => $item) {
            if ($key = 'shapeType' || $key = 'shape'|| $key = 'type'){
                // if value is string
                if (is_string($item) && strlen($item) > 1){
                    $this->shape = TentShape::translateToConst($item);
                    $this->shapeType = TentShape::translateToConst($this->shape);
                }
                // if value is int or string with length 1 (e.g. "1")
                if (is_int($item) || strlen($item) == 1){
                    $this->shapeType = (int)$item;
                    $this->shape = TentShape::translateFromConst($item);
                }
            }
            if ($key = 'width')  $this->width = $item;
            if ($key = 'lenght') $this->length = $item;
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