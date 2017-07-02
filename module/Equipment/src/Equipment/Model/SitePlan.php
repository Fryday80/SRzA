<?php
namespace Equipment\Model;

use Application\Model\AbstractModel;

class SitePlan extends AbstractModel
{
    /** @var  int */
    public $id;
    /** @var  string */
    public $name;
    /** @var  string */
    public $data;
    /** @var  string */
    public $mapType;
    /** @var  float */
    public $longitude;
    /** @var  float */
    public $latitude;
    /** @var  int */
    public $zoom;
    /** @var  int */
    public $scale;
    /** @var  int */
    public $diameter;

    public function setId($id) {
        $this->id = (int)$id;
    }
    public function setData($data) {
        $this->data = stripslashes($data);
    }
    public function setLongitude($value) {
        $this->longitude = (float)$value;
    }
    public function setLatitude($value) {
        $this->latitude = (float)$value;
    }
    public function setZoom($zoom) {
        $this->zoom = (int)$zoom;
    }
    public function setScale($scale) {
        $this->scale = (int)$scale;
    }
    public function setDiameter($diameter) {
        $this->diameter = (int)$diameter;
    }
}