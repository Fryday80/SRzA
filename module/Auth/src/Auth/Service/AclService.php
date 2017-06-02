<?php
namespace Auth\Service;

use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;
use Zend\Permissions\Acl\Role\Registry;

class AclService extends Acl
{

    const DEFAULT_ROLE = 'Guest';

    public $_roleTableObject;
    public $roles;
    public $permissions;
    public $resources;
    public $rolePermission;
    public $commonPermission;

    public function __construct() { }

    public function initAcl($roles, $resources, $rolePermissions) {
        $this->roles = $roles;
        $this->resources = $resources;
        $this->rolePermission = $rolePermissions;

        // common to all user
        $this->commonPermission = array(
            'Auth\Controller\Auth' => array(
                'logout',
                'login'
            )
        );

        $this->_addRoles()
            ->_addResources()
            ->_addRoleResources();

        if ($this->hasRole('Administrator') ) {
            //@todo sollen wir dem admin einfach alles erlauben? dann kann man sich nicht aussperren
//             $this->allow('Administrator');
        }
    }

    public function isAccessAllowed($role, $resource, $permission) {
        if (! $this->hasResource($resource)) {
            return false;
        }
        if ($this->isAllowed($role, $resource, $permission)) {
            return true;
        }
        return false;
    }
//@todo remove
//    public function fetchAllRoles () {
//        $roleTable = $this->serviceLocator->get("Auth\Model\RoleTable");
//        return $roleTable->getUserRoles();
//    }

    protected function _addRoles()
    {
        $this->addRole(new Role(self::DEFAULT_ROLE));
        if (! empty($this->roles)) {
            foreach ($this->roles as $role) {
                $roleName = $role['role_name'];
                if (! $this->hasRole($roleName)) {
                    $this->addRole(new Role($roleName), ($role['role_parent_name'])? $role['role_parent_name']: self::DEFAULT_ROLE);
                }
            }
        }
        return $this;
    }

    protected function _addResources()
    {

        if (! empty($this->resources)) {
            foreach ($this->resources as $resource) {
                if (! $this->hasResource($resource['resource_name'])) {
                    $this->addResource(new Resource($resource['resource_name']));
                }
            }
        }

        // add common resources
        if (! empty($this->commonPermission)) {
            foreach ($this->commonPermission as $resource => $permissions) {
                if (! $this->hasResource($resource)) {
                    $this->addResource(new Resource($resource));
                }
            }
        }

        return $this;
    }

    protected function _addRoleResources()
    {
        // allow common resource/permission to guest user
        if (! empty($this->commonPermission)) {
            foreach ($this->commonPermission as $resource => $permissions) {
                foreach ($permissions as $permission) {
                    $this->allow(self::DEFAULT_ROLE, $resource, $permission);
                }
            }
        }

        if (! empty($this->rolePermission)) {
            foreach ($this->rolePermission as $rolePermissions) {
                $this->allow($rolePermissions['role_name'], $rolePermissions['resource_name'], $rolePermissions['permission_name']);
            }
        }

        return $this;
    }

    /**
     * Returns the Role registry for this ACL
     *
     * If no Role registry has been created yet, a new default Role registry
     * is created and returned.
     *
     * @return Registry
     */
    public function getRoleRegistry()
    {
        if (null === $this->roleRegistry) {
            $this->roleRegistry = new Registry();
        }
        return $this->roleRegistry;
    }
}