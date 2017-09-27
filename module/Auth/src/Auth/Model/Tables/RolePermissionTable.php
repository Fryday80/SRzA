<?php
namespace Auth\Model\Tables;

use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class RolePermissionTable extends AbstractTableGateway
{

    public $table = 'role_permission';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet(ResultSet::TYPE_ARRAY);
        $this->initialize();
    }

    public function getPermissionsByRoleID($roleID)
    {
        if(!is_int($roleID)) {
            throw new \Exception("Role ID musst be an int;");
        }
        $sql = new Sql($this->getAdapter());
        
        $select = $sql->select()
            ->from(array(
            't1' => 'role'
        ))
            ->columns(array(
            'role_name',
            'rid'
        ))
            ->join(array(
            't2' => $this->table
        ), 't1.rid = t2.role_id', array(
            'role_permission_id' => 'id',
            'permission_id'
        ), 'left')
            ->join(array(
            't3' => 'permission'
        ), 't3.id = t2.permission_id', array(
            'permission_name'
        ), 'left')
            ->join(array(
            't4' => 'resource'
        ), 't4.id = t3.resource_id', array(
            'resource_name'
        ), 'left')
            ->where("t1.rid = $roleID and t3.permission_name is not null and t4.resource_name is not null");
            //->order('t1.rid');
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $this->resultSetPrototype->initialize($statement->execute())
            ->toArray();
        return $result;
    }

    public function getRolePermissions()
    {
        $sql = new Sql($this->getAdapter());
        
        $select = $sql->select()
            ->from(array(
            't1' => 'role'
        ))
            ->columns(array(
            'role_name'
        ))
            ->join(array(
            't2' => $this->table
        ), 't1.rid = t2.role_id', array(), 'left')
            ->join(array(
            't3' => 'permission'
        ), 't3.id = t2.permission_id', array(
            'permission_name'
        ), 'left')
            ->join(array(
            't4' => 'resource'
        ), 't4.id = t3.resource_id', array(
            'resource_name'
        ), 'left')
            ->where('t3.permission_name is not null and t4.resource_name is not null')
            ->order('t1.rid');
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $this->resultSetPrototype->initialize($statement->execute())
            ->toArray();
        return $result;
    }
    public function addPermission($roleID, $permissionID) {
        $this->insert(array(
            'role_id' => $roleID,
            'permission_id' => $permissionID
        ));
    }
    public function deletePermission($roleID, $permissionID) {
        $this->delete(array(
            'role_id' => $roleID,
            'permission_id' => $permissionID
        ));
    }
}