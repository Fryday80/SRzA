<?php
namespace Auth\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class PermissionTable extends AbstractTableGateway
{

    public $table = 'permission';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet(ResultSet::TYPE_ARRAY);
        $this->initialize();
    }

    public function getResourcePermissions()
    {
        try {
            $sql = new Sql($this->getAdapter());
            $select = $sql->select()
                ->from(array(
                'p' => $this->table
            ))
                ->columns(array(
                'id',
                'permission_name'
            ))
                ->join(array(
                "r" => "resource"
            ), "p.resource_id = r.id", array(
                'resource_name',
                'resource_id' => 'id'
            ))
                ->order('resource_name');
            
            $statement = $sql->prepareStatementForSqlObject($select);
            $resources = $this->resultSetPrototype->initialize($statement->execute())
                ->toArray();
            return $resources;
        } catch (\Exception $err) {
            throw $err;
        }
    }
    public function getByResourceID($resourceID) {
        $result = $this->select("resource_id = $resourceID");
        return $result->toArray();
    }
    public function add($resourceID, $permissionName)
    {
        return $this->insert([
            'resource_id' => $resourceID,
            'permission_name' => $permissionName,
        ]);
    }
    public function deleteByResourceID($resourceID)
    {
        return $this->delete(['resource_id' => $resourceID]);
    }
}
