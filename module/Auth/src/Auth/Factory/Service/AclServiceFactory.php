<?php

namespace Auth\Factory\Service;

use Zend\ServiceManager\FactoryInterface;
use Application\Service\CacheService;
use Auth\Service\AclService;
use Zend\ServiceManager\ServiceLocatorInterface;

class AclServiceFactory implements FactoryInterface
{
    /** @var  CacheService */
    private $cache;

    public function createService(ServiceLocatorInterface $sm)
    {
        $this->cache = $sm->get('CacheService');
        $acl = null;
        if ($this->cache->hasCache('acl')) {
            $acl = $this->cache->getCache('acl');
        } else {
            $acl = new AclService();

            //get Roles
            $roles = $this->_getAllRoles($sm);
            $resources = $this->_getAllResources($sm);
            $rolePermission = $this->_getRolePermissions($sm);

            $acl->initAcl($roles, $resources, $rolePermission);
            $this->cache->setCache('acl', $acl);
        }
        return $acl;
    }

    protected function _getAllRoles($sm) {
        $roleTable = $sm->get('Auth\Model\RoleTable');
        return $roleTable->getUserRoles();
    }

    protected function _getAllResources($sm) {
        $resourceTable = $sm->get('Auth\Model\ResourceTable');
        return $resourceTable->getAllResources();
    }

    protected function _getRolePermissions($sm) {
        $rolePermissionTable = $sm->get('Auth\Model\RolePermissionTable');
        return $rolePermissionTable->getRolePermissions();
    }

}