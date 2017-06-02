<?php

namespace Auth\Factory;

use Application\Service\CacheService;
use Auth\Service\AclService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AclServiceFactory implements FactoryInterface
{
    /** @var  CacheService */
    private $cache;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->cache = $serviceLocator->get('CacheService');
        $acl = null;
        if ($this->cache->hasCache('acl')) {
            $acl = $this->cache->getCache('acl');
        } else {
            $acl = new AclService();

            //get Roles
            $roles = $this->_getAllRoles($serviceLocator);
            $resources = $this->_getAllResources($serviceLocator);
            $rolePermission = $this->_getRolePermissions($serviceLocator);

            $acl->initAcl($roles, $resources, $rolePermission);
            $this->cache->setCache('acl', $acl);
        }
        return $acl;
    }

    protected function _getAllRoles(ServiceLocatorInterface $serviceLocator) {
        $roleTable = $serviceLocator->get("Auth\Model\RoleTable");
        return $roleTable->getUserRoles();
    }

    protected function _getAllResources(ServiceLocatorInterface $serviceLocator) {
        $resourceTable = $serviceLocator->get("Auth\Model\ResourceTable");
        return $resourceTable->getAllResources();
    }

    protected function _getRolePermissions(ServiceLocatorInterface $serviceLocator) {
        $rolePermissionTable = $serviceLocator->get("Auth\Model\RolePermissionTable");
        return $rolePermissionTable->getRolePermissions();
    }

}