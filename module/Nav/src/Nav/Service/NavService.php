<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 13.04.2017
 * Time: 13:01
 */

namespace Nav\Service;


use Auth\Model\PermissionTable;
use Auth\Model\ResourceTable;
use Auth\Model\RolePermissionTable;
use Auth\Model\RoleTable;

class NavService
{
    /** @var $roleTable RoleTable */
    private $roleTable;
    /** @var $permissionTable PermissionTable */
    private $permissionTable;
    /** @var $resourceTable ResourceTable */
    private $resourceTable;
    /** @var $rolePermissionTable RolePermissionTable */
    private $rolePermissionTable;

    private $navRolesResource = 'Role';
    private $navRolesResourceID;


    function __construct($sm)
    {
        $this->roleTable = $sm->get('Auth\Model\RoleTable');
        $this->permissionTable = $sm->get('Auth\Model\PermissionTable');
        $this->resourceTable = $sm->get('Auth\Model\ResourceTable');
        $this->rolePermissionTable = $sm->get('Auth\Model\RolePermissionTable');
        $this->navRolesResourceID = $this->getResourceID();
    }
    public function addRole($roleName){
        //@todo add role to permissions
        $roleID = $this->roleTable->getRoleIDByName($roleName);
        $this->permissionTable->add($this->navRolesResourceID, $roleName );
        $this->rolePermissionTable->addPermission($roleID, $roleName);
    }
    public function updateRole($rid, $roleName){
        $this->removeRole($rid);
        $this->addRole($roleName);
    }
    public function removeRole($rid){
        //@todo delete role from permissions
        $roleName = $this->roleTable->getRoleByID($rid)['role_name'];
        $this->permissionTable->delete($this->navRolesResourceID, $rid );
        $this->rolePermissionTable->delete($rid, $roleName);
    }

    private function getResourceID(){
        return $this->resourceTable->getByName($this->navRolesResource)['id'];
    }
    private function addPermission(){}
    private function removePermission(){}
}