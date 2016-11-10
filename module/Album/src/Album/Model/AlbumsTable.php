<?php
namespace Album\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class AlbumsTable extends AbstractTableGateway
{

    public $table = 'albums';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function fetchAllAlbums () {
        $row = $this->select()->current();
        if (!$row)
            return false;

        return $row->toArray();
    }
    
    public function getById($id) {
        $row = $this->select(array('id' => (int) $id))->current();
        if (!$row)
            return false;
        
        return $row->getArrayCopy();
    }
    
    //data like: array('columName' => $data['columName'] )
    public function add($data) {
        if (!$this->insert($data))
            return false;
        return $this->getLastInsertValue();
    }
    public function change($id, $data) {
        if (!$this->update($data, array('id' => (int)$id)))
            return false;
        return $id;
    }
    public function remove($id) {
        return $this->delete(array('id' => (int)$id));
    }

}
