<?php
namespace Auth\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RoleTable extends AbstractTableGateway
{
    /** @var $permissionTable PermissionTable */
    private $permissionTable;
    /** @var $rolePermissionTable RolePermissionTable */
    private $rolePermissionTable;
    /** @var $resourceTable ResourceTable */
    private $resourceTable;
    
    private $navRolesResource = 'Role';
    private $navRolesResourceID;

    public $table = 'role';
    private $sorted = false;
    
    public function __construct(Adapter $adapter, $sm)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet(ResultSet::TYPE_ARRAY);
        $this->initialize();
        $this->permissionTable = $sm->get('Auth\Model\PermissionTable');
        $this->rolePermissionTable = $sm->get('Auth\Model\RolePermissionTable');
        $this->resourceTable = $sm->get('Auth\Model\ResourceTable');
        $this->navRolesResourceID = $this->resourceTable->getByName($this->navRolesResource)['id'];
    }
    
    public function getUserRoles($where = array(), $columns = array())
    {
        try {
            $sql = new Sql($this->getAdapter());
            $select = $sql->select()->from(array(
                'role' => $this->table
            ));
            $select->columns(array(
                'rid',
                'role_name',
                'role_parent',
            ));
            $select->join(array(
                    'parent' => $this->table
                ), 
                    'parent.rid = role.role_parent', array('role_parent_name' => 'role_name'), 'left'
                );
            
            if (count($where) > 0) {
                $select->where($where);
            }
            
            if (count($columns) > 0) {
                $select->columns($columns);
            }
            $statement = $sql->prepareStatementForSqlObject($select);
            $roles = $this->resultSetPrototype->initialize($statement->execute())
                ->toArray();
            return $roles;
        } catch (\Exception $e) {
            throw new \Exception($e->getPrevious()->getMessage());
        }
    }
    public function getRoleByID($id) {
        $res = $this->getWhere("role.rid = '$id'")->toArray();
        if (count($res) > 0) {
            return $res[0];
        }
        return null;
    }
    public function getRoleIDByName($name) {
        $res = $this->getWhere("role_name = $name")->toArray();
        if (count($res) > 0) {
            return $res[0]['rid'];
        }
        return null;
    }
    public function edit($data, $id) {
        $this->permissionTable->remove($this->navRolesResourceID, $data['roleName'] );
        $permID = $this->permissionTable->getPermIDByResourceIDAndPermName($this->navRolesResourceID, $data['roleName'] );
        $this->rolePermissionTable->delete($id, $permID);

        $this->permissionTable->add($this->navRolesResourceID, $data['roleName'] );
        $permID = $this->permissionTable->getPermIDByResourceIDAndPermName($this->navRolesResourceID, $data['roleName'] );
        $this->rolePermissionTable->deletePermission($id, $permID);

        $this->update($data, array('rid' => $id));
    }
    public function add($name, $parent, $status) {
        $status = 'Active';//($status)? 'Active': 'Inactive';
        //@todo add permission to Role resource
        $roleID = $this->getRoleIDByName($name);
        $this->permissionTable->add($this->navRolesResourceID, $name );
        $permID = $this->permissionTable->getPermIDByResourceIDAndPermName($this->navRolesResourceID, $name );
        $this->rolePermissionTable->addPermission($roleID, $permID);
        
        $this->insert(array('role_name' => $name, 'role_parent' => $parent, 'status' => $status));
    }
    public function deleteByID($id) {
        $roleName = $this->getRoleByID($id)['role_name'];
        $this->permissionTable->remove($this->navRolesResourceID, $roleName );
        $permID = $this->permissionTable->getPermIDByResourceIDAndPermName($this->navRolesResourceID, $roleName );
        $this->rolePermissionTable->deletePermission($id, $permID);
        
        return $this->delete([
            'rid' => $id
        ]);
    }
    public function fetchAll(){
        return $this->getWhere()->toArray();
    }


    public function fetchAllSorted(){
        if (!$this->sorted) {
            $megalomaniac = 'Administrator';
            $rearranged = array();
            $return = array();

            $all = $this->fetchAll();
            foreach ($all as $key => $role) {
                $rearranged[$role['role_name']] = $role;
            }

            $index = count($rearranged)-1;
            $return[$index] = $rearranged[$megalomaniac];

            for ($i = $index - 1; $i >= 0; $i--) {
                $return[$i] = $rearranged [ $return[$i+1]['role_parent_name']];
            }

            foreach ($return as $role) {
                $this->sorted[$role['rid']] = $role['role_name'];
            }
        }
        return $this->sorted;
    }

    private function getWhere($where = array(), $columns = array())
    {
        try {
            $sql = $this->getSql();
            $select = $sql->select();

            if (count($where) > 0) {
                $select->where($where);
            }
            if (count($columns) > 0) {
                $select->columns($columns);
            }
            $select->join(array(
                'parent' => $this->table
            ),
                'parent.rid = role.role_parent', array('role_parent_name' => 'role_name'), 'left'
            );

            $results = $this->selectWith($select);
            return $results;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
