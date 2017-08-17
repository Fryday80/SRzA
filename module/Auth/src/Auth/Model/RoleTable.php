<?php
namespace Auth\Model;

use Exception;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

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
    
    public function __construct(Adapter $adapter, $permissionTable, $rolePermissionTable, $resourceTable)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet(ResultSet::TYPE_ARRAY);
        $this->initialize();
        $this->permissionTable = $permissionTable;
        $this->rolePermissionTable = $rolePermissionTable;
        $this->resourceTable = $resourceTable;
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
        $res = $this->getWhere("role.role_name = '$name'")->toArray();
        if (count($res) > 0) {
            return $res[0]['rid'];
        }
        return null;
    }

    public function edit($data, $id) {
        $oldRole = $this->getRoleByID($id);
        if (!$oldRole) {
            throw new Exception("can't edit role because Role not found");
        }
        if ($data['role_name'] !== $oldRole['role_name']){
			$permID = $this->permissionTable->getPermIDByResourceIDAndPermName($this->navRolesResourceID, $oldRole['role_name'] );
			$this->permissionTable->editById($permID, $this->navRolesResourceID, $data['role_name']);

			$this->update($data, array('rid' => $id));
		}
    }

    public function add($name, $parent, $status = null) {
        $status = ($status === null)? 'Inactive' : 'Active';

        $this->insert(array('role_name' => $name, 'role_parent' => $parent, 'status' => $status));
        $roleID = $this->getLastInsertValue();
        $this->permissionTable->add($this->navRolesResourceID, $name );
        $permID = $this->permissionTable->getPermIDByResourceIDAndPermName($this->navRolesResourceID, $name );
        if (!$roleID) {
            throw new Exception("Role '$roleID' doesn't exists");
        }
        if (!$roleID) {
            throw new Exception("Permission '$name' doesn't exists");
        }
        $this->rolePermissionTable->addPermission($roleID, $permID);
        return $roleID;

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


	// =============================00000 refactoring
	public function onAdd($name, $parent, $status = null)
	{
		$status = ($status === null)? 'Inactive' : 'Active';
		// ADD
		$this->insert(array('role_name' => $name, 'role_parent' => $parent, 'status' => $status));
		$newId = $this->getRoleByID($this->getRoleIDByName($name))['rid'];
		// manage parents and childes
		$this->swapParents($parent, $newId);
		// permissions
		$permID = $this->addPermissionToRoleResource($name);
		$this->rolePermissionTable->addPermission($newId, $permID);
    }
	public function onDelete($id)
	{
		$oldRole = $this->getRoleByID($id);
		// manage parents and childes
		$this->swapParents($id, $oldRole['role_parent']);
		// permissions
		$permID = $this->deletePermissionFromRoleResource($oldRole['role_name']);
		$this->rolePermissionTable->deletePermission($id, $permID);
		// DELETE
		$this->deleteByID($id);
    }
	public function onEdit($data, $id)
	{
		$oldRole = $this->getRoleByID($id);
		$nameChange   = ($oldRole['role_name']   == $data['role_name'])   ? false : true;
		$parentChange = ($oldRole['role_parent'] == $data['role_parent']) ? false : true;
		// manage parents and childes
		if ($parentChange){
			// old child
			$this->swapParents($id, $oldRole['role_parent']);
			// new child
			$this->swapParents($data['role_parent'], $id);
		}
		// permissions
		if ($nameChange){
			$permID = $this->deletePermissionFromRoleResource($oldRole['role_name']);
			$this->rolePermissionTable->deletePermission($id, $permID);
			$permID = $this->addPermissionToRoleResource($data['role_name']);
			$this->rolePermissionTable->addPermission($id, $permID);
		}
		// EDIT
		$this->edit($data, $id);
    }

	private function getChild($id)
	{
		$res = $this->getWhere("role.role_parent = '$id'")->toArray();
		if (count($res) > 0) {
			return $res[0];
		}
		return null;
	}
	private function swapParents($oldParent, $newParent)
	{
		$child = $this->getChild($oldParent);
		bdump($child);
		if ($child) {
			$child['role_parent'] = $newParent;
			$this->edit($child, $child['rid']);
		}
    }

	private function addPermissionToRoleResource($roleName)
	{
		$this->permissionTable->add($this->navRolesResourceID, $roleName );
		return $this->permissionTable->getPermIDByResourceIDAndPermName($this->navRolesResourceID, $roleName );
		
    }
	private function deletePermissionFromRoleResource($roleName)
	{
		$oldId = $this->permissionTable->getPermIDByResourceIDAndPermName($this->navRolesResourceID, $roleName );
		$this->permissionTable->remove($this->navRolesResourceID, $roleName );
		return $oldId;

    }




//	public function onAdd($name, $parent, $status = null)
//	{
//		$newId = $this->add($name, $parent, $status);
//		$this->swapParents($parent, $newId);
////		$replaceChildRole = $this->getChild($parent);
////		if($replaceChildRole) {
////			$replaceChildRole['parent_role'] = $newId;
////			$this->edit($replaceChildRole, $replaceChildRole['rid']);
////		}
//	}
//	public function onDelete($id)
//	{
//		$oldRole = $this->getRoleByID($id);
//		$this->swapParents($id, $oldRole['role_parent']);
//
//	}
//	public function onEdit($data, $id)
//	{
//		$oldRole = $this->getRoleByID($id);
//		$nameChange   = ($oldRole['role_name']   == $data['role_name'])   ? false : true;
//		$parentChange = ($oldRole['role_parent'] == $data['role_parent']) ? false : true;
//		if ($parentChange){
//			// old child
//			$this->swapParents($id, $oldRole['role_parent']);
////			$oldChildRole = $this->getChild($id);
////			$oldChildRole['role_parent'] = $oldRole['role_parent'];
//
//			// new child
//			$this->swapParents($data['role_parent'], $id);
////			$newChild = $this->getChild($data['role_parent']);
////			if ($newChild)
////				$newChild['role_parent'] = $id;
//		}
//		if ($nameChange){
//
//		}
//
////		if ($newChild)
////			$this->edit($newChild, $newChild['rid']);
////		$this->edit($oldChildRole, $oldChildRole['rid']);
//		$this->edit($data, $id);
//	}
}
