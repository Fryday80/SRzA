<?php
namespace Cast\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Predicate\PredicateSet;
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
        return $this->getAllCastData();
    }
    public function getById($id) {
        $result = $this->getAllCastData(array('id' => (int) $id));
        return $result;
    }
    public function getByUserId($id) {
        $result = $this->getAllCastData(array('user_id' => (int) $id));
        return $result;
    }
    public function getByTrossId($id) {
        $result = $this->getAllCastData(array('tross_id' => (int) $id));
        return $result;
    }
    public function getByFamilyId($id) {
        $result = $this->getAllCastData(array('family_id' => (int) $id));
        return $result;
    }

    public function getAllPossibleSupervisorsFor($familyID) {
        try {
            $select = $this->sql->select();
            $select->where(array(
                'family_id' => (int) $familyID,
                'tross_id' => (int)$familyID
            ), PredicateSet::OP_OR);

            $statement = $this->sql->prepareStatementForSqlObject($select);
            $result = $this->resultSetPrototype->initialize($statement->execute())
                ->toArray();

            return $result;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * returns all characters and there jobs, families and so on
     * @param array $where
     * @return array results
     * @throws \Exception
     */
    public function getAllCastData(Array $where = array()) {//so meinte ich das
        try {
            $sql = new Sql($this->getAdapter());

            $select = $sql->select()
                ->from(array(
                    'char' => 'characters'
                ))
                ->columns(array(
                    'id' => 'id',
                    'user_id' => 'user_id',
                    'name' => 'name',
                    'surename',
                    'gender',
                    'birthday',
                    'guardian_id',
                    'supervisor_id',
                    'vita',
                    'active'
                ))
                ->join(array(
                    'family' => 'families'
                ), 'char.family_id = family.id', array(
                    'family_id' => 'id',
                    'family_name' => 'name',
                    'blazon_id' => 'blazon_id',
                ), 'left')
                ->join(array(
                    'job' => 'job'
                ), 'job.id = char.job_id', array(
                    'job_id' => 'id',
                    'job_name' => 'job'
                ), 'left')
                ->where($where);

            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $this->resultSetPrototype->initialize($statement->execute())
                ->toArray();
            return $result;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
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
