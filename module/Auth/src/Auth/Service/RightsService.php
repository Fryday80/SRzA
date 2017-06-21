<?php
namespace Auth\Service;


use Auth\Model\PermissionTable;
use Auth\Model\ResourceTable;
use Auth\Model\RolePermissionTable;
use Auth\Model\RoleTable;

class RightsService
{
    private $permissionTable;
    private $rolePermissionTable;
    private $roleTable;
    private $resourceTable;

    private $sorted = false;

    function __construct(
        PermissionTable $permissionTable,
        RolePermissionTable $rolePermissionTable,
        RoleTable $roleTable,
        ResourceTable $resourceTable
    )
    {
        $this->permissionTable = $permissionTable;
        $this->rolePermissionTable = $rolePermissionTable;
        $this->roleTable = $roleTable;
        $this->resourceTable = $resourceTable;
    }

//===================================================== permission Table
//===================================================== rolePermission Table
//===================================================== role Table
    public function getAllRoles() {
        return $this->roleTable->getAllRoles();
    }

    public function getRoleByID($id) {
        return $this->roleTable->getRoleByID($id);
    }

    public function editRole($data, $id) {
        return $this->roleTable->edit($data, $id);
    }

    public function addRole($name, $parent, $status = null) {
        //@todo rearrange other roles
        return $this->roleTable->add($name, $parent, $status);
    }

    public function deleteRoleByID($id) {
        //@todo rearrange other roles
        return $this->roleTable->deleteByID($id);
    }

    public function fetchAllRoles(){
        return $this->roleTable->getWhere()->toArray();
    }

    /**
     * sorts all Roles ascending, ending with Administrator role
     * @return mixed bool|array
     */
    public function fetchAllRolesSorted(){
        if (!$this->sorted) {
            $megalomaniac = 'Administrator';
            $rearranged = array();
            $return = array();

            $all = $this->fetchAllRoles();
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

    private function rearrangeRoles()
    {
        $sort = $this->fetchAllRolesSorted();
        bdump($sort);
    }
//===================================================== resource Table
}