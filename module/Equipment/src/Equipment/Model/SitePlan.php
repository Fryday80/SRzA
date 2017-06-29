<?php
namespace Equipment\Model;

use Application\Model\AbstractModel;

class SitePlan extends AbstractModel
{
    public $id;
    public $name;
    public $data;
    public $longitude;
    public $latitude;

    public function setId($id) {
        $this->id = $id;
    }
    public function getName() {
        return $this->name;
    }

}