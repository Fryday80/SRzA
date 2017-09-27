<?php
namespace Auth\Model\Tables;

use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class ResourceTable extends AbstractTableGateway
{

    public $table = 'resource';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet(ResultSet::TYPE_ARRAY);
        $this->initialize();
    }

    public function getAllResources()
    {
        try {
            $sql = new Sql($this->getAdapter());
            $select = $sql->select()->from(array(
                'rs' => $this->table
            ));
            $select->columns(array(
                'id',
                'resource_name'
            ));
            $statement = $sql->prepareStatementForSqlObject($select);
            $resources = $this->resultSetPrototype->initialize($statement->execute())
                ->toArray();
            return $resources;
        } catch (\Exception $e) {
            throw new \Exception($e->getPrevious()->getMessage());
        }
    }

    public function getByID($id)
    {
        $result = $this->select([
            'id' => (int)$id
        ])->toArray();
        if (count($result) < 1) {
            return null;
        }
        return $result[0];
    }
    public function getByName($name)
    {
        $result = $this->select([
            'resource_name' => $name
        ])->toArray();
        if (count($result) < 1) {
            return null;
        }
        return $result[0];
    }

    public function add($name)
    {
        $this->insert([
            'resource_name' => $name
        ]);
        return $this->lastInsertValue;
    }

    public function deleteByID($id)
    {
        return $this->delete([
            'id' => $id
        ]);
    }
}
