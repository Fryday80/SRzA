<?php
namespace Application\Model;

use Exception;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class TempUrlTable extends AbstractTableGateway
{

    public $table = 'temp_url';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet(ResultSet::TYPE_ARRAY);
        $this->initialize();
    }

    public function getAll() {
        try {
            $sql = new Sql($this->getAdapter());
            $select = $sql->select();
            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $this->resultSetPrototype->initialize($statement->execute())->toArray();
            return $result;
        } catch (Exception $e) {
            throw new Exception($e->getPrevious()->getMessage());
        }
    }

    public function getByID($id) {
        $result = $this->select([
            'id' => $id
        ])->toArray();
        if (count($result) < 1) {
            return null;
        }
        return $result[0];
    }

    public function add(Array $data) {
        try {
            $this->insert($data);
            return $this->lastInsertValue;
        } catch (Exception $e) {
            throw new Exception($e->getPrevious()->getMessage());
        }
    }

    public function deleteByID($id) {
        return $this->delete([
            'id' => $id
        ]);
    }
}
