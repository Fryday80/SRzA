<?php
namespace Cast\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;

class CharacterTable extends AbstractTableGateway
{

    public $table = 'characters';

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
    
    public function getById($id) {
        $row = $this->select(array('id' => (int) $id));
        if (!$row)
            return false;
        
        return $row->toArray()[0];
    }

    public function getByUserId($id) {
        $row = $this->select(array('user_id' => (int) $id));
        if (!$row)
            return false;

        return $row->toArray();
    }

    public function getByTrossId($id) {
        $row = $this->select(array('tross_id' => (int) $id));
        if (!$row)
            return false;

        return $row->toArray();
    }
    public function getByFamilyId($id) {
        $row = $this->select(array('family_id' => (int) $id));
        if (!$row)
            return false;

        return $row->toArray();
    }
    /**
     * returns all characters and there jobs, families and so on
     */
    public function getAllCastData() {
        try {


            $sql = new Sql($this->getAdapter());

            $select = $sql->select()
                ->from(array(
                    'char' => 'characters'
                ))
                ->columns(array(
                    'id' => 'id',
                    'name' => 'name',
                    'surename',
                    'gender',
                    'vita'
                ))
                ->join(array(
                    'family' => 'families'
                ), 'char.family_id = family.id', array(
                    'family_id' => 'id',
                    'family_name' => 'name',
                ), 'left')
                ->join(array(
                    'job' => 'job'
                ), 'job.id = char.job_id', array(
                    'job_id' => 'id',
                    'job_name' => 'job'
                ), 'left');
//                ->join(array(
//                    't4' => 'resource'
//                ), 't4.id = t3.resource_id', array(
//                    'resource_name'
//                ), 'left')
//                ->where();
            //->order('t1.rid');

            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $this->resultSetPrototype->initialize($statement->execute())
                ->toArray();
            return $result;


        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
            die;
        }




    }
    public function add($data) {
        unset($data['id']);
        unset($data['submit']);
        if (!$this->insert($data) )
            return false;
        return $this->getLastInsertValue();
    }
    
    public function save($id, $data) {
        unset($data['submit']);
        if (!$this->update($data, array('id' => (int)$id)))
            return false;
        return $id;
    }
    
    public function remove($id) {
        return ($this->delete(array('id' => (int)$id)))? $id : false;
    }

}
