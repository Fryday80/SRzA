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
    /** @var $roleTable             RoleTable           */
    /** @var $permissionTable       PermissionTable     */
    /** @var $resourceTable         ResourceTable       */
    /** @var $rolePermissionTable   RolePermissionTable */
    private $roleTable;
    private $permissionTable;
    private $resourceTable;
    private $rolePermissionTable;


    function __construct($sm)
    {
        $this->roleTable = $sm->get('Auth\Model\RoleTable');
        $this->permissionTable = $sm->get('Auth\Model\PermissionTable');
        $this->resourceTable = $sm->get('Auth\Model\ResourceTable');
        $this->rolePermissionTable = $sm->get('Auth\Model\RolePermissionTable');
    }
    public function addRole($role_name){}
    public function updateRole($rid){}
    public function removeRole($rid){}
}