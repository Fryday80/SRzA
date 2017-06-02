<?php
namespace Cast\Model;

use Zend\Db\TableGateway\AbstractTableGateway;

class BlazonTable extends AbstractTableGateway
{

    public $table = 'blazon';

    public function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function getAll () {
        $row = $this->select();
        if (!$row)
            return false;

        return $row->toArray();
    }
    
    public function getById($id) {
        $row = $this->select(array('id' => (int) $id));
        if (!$row)
            return false;
        return $row->toArray()[0];
    }

    public function add($data) {
        if (!$this->insert($data))
            return false;
        return $this->getLastInsertValue();
    }
    
    public function save($id, $data) {
        unset($data['id']);
        //cleanfix
//bdump($data);
        if (!$this->update($data, array('id' => (int)$id)))
            return false;
        return $id;
    }
    
    public function remove($id) {
        //@todo remove image
        return ($this->delete(array('id' => (int)$id)))? $id : false;
    }

}
