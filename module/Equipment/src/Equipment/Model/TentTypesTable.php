<?php
namespace Equipment\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;

class TentTypesTable extends AbstractTableGateway
{

    public $table = 'tent_types';

    public function __construct(Adapter $adapter)
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

    public function getById($id)
    {
        $row = $this->select(array('id' => $id));
        if (!$row)
            return false;

        return $row->toArray()[0];
    }

    public function add($data) {
        if (!$this->insert(array(
            'name'  => $data['name'],
            'shape' => $data['shape']
        )))
            return false;
        return $this->getLastInsertValue();
    }
    
    public function save($data) {
        if (!$this->update(array(
            'name'  => $data['name'],
            'shape' => $data['shape']
        ), array('id' => (int)$data['id'])))
            return false;
        return $data['id'];
    }
    
    public function remove($id) {
        return ($this->delete(array('id' => (int)$id)))? $id : false;
    }

}
