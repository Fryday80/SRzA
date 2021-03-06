<?php
namespace Cast\Model\Tables;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;

class JobTable extends AbstractTableGateway
{

    public $table = 'job';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function getAll() {
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
        if (!$this->insert(array(
            'job' => $data['job'],
            'blazon_id' => $data['blazon_id'],
        )))
            return false;
        return $this->getLastInsertValue();
    }
    
    public function save($id, $data) {
        if (!$this->update(array(
            'job' => $data['job'],
            'blazon_id' => $data['blazon_id'],
        ), array('id' => (int)$id)))
            return false;
        return $id;
    }
    
    public function remove($id) {
        return ($this->delete(array('id' => (int)$id)))? $id : false;
    }

}
