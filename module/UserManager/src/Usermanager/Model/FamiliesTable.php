<?php
namespace Usermanager\Model;

use Zend\Db\TableGateway\AbstractTableGateway;

class FamiliesTable extends AbstractTableGateway
{

    public $table = 'families';

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
        if (!$this->insert(array('name' => $data['name'])))
            return false;
        return $this->getLastInsertValue();
    }
    
    public function save($id, $data) {
        if (!$this->update(array('name' => $data['name']), array('id' => (int)$id)))
            return false;
        return $id;
    }
    
    public function remove($id) {
        return ($this->delete(array('id' => (int)$id)))? $id : false;
    }

}
