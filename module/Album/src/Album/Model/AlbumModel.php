<?php
namespace Album\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class AlbumModel
{
    public $name = "";
    public $description = "";
    public $images = [];

    public function __construct($name, $description, $images) {
        $this->name = $name;
        $this->description = $description;
        $this->images= $images;
    }
}
