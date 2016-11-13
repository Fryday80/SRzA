<?php
namespace Album\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class AlbumsTable extends AbstractTableGateway
{

    public $table = 'profile';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function fetchAllUser () {
        $row = $this->select();
        if (!$row)
            return false;

        return $row->toArray();
    }
    
    public function getUserByID($id) {
        $row = $this->select(array('id' => (int) $id));
        if (!$row)
            return false;
        
        return $row->toArray();
    }
    
    //data like: array('columName' => $data['columName'] )
    public function add($data) {
        if (!$this->insert($data))
        return false;
        return $this->getLastInsertValue();
    }
    
    public function change($user_id, $data) {
        if (!$this->update($data, array('user_id' => (int)$user_id)))
            return false;
        return $user_id;
    }
    
    public function remove($user_id) {
        return ($this->delete(array('user_id' => (int)$user_id)))? $user_id : false;
    }

}
