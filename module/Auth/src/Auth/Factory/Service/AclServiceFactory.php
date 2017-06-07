<?php

namespace Auth\Factory\Service;

use Application\Factory\Basic\MyDefaultFactory;
use Application\Service\CacheService;
use Auth\Service\AclService;
use Zend\ServiceManager\ServiceLocatorInterface;

class AclServiceFactory extends MyDefaultFactory
{
    /** @var  CacheService */
    private $cache;

    public function createService(ServiceLocatorInterface $sm)
    {
        parent::createService($sm);
        $this->cache = $this->get('CacheService');
        $acl = null;
        if ($this->cache->hasCache('acl')) {
            $acl = $this->cache->getCache('acl');
        } else {
            $acl = new AclService();

            //get Roles
            $roles = $this->_getAllRoles();
            $resources = $this->_getAllResources();
            $rolePermission = $this->_getRolePermissions();

            $acl->initAcl($roles, $resources, $rolePermission);
            $this->cache->setCache('acl', $acl);
        }
        return $acl;
    }

    protected function _getAllRoles() {
        $roleTable = $this->get('Auth\Model\RoleTable');
        return $roleTable->getUserRoles();
    }

    protected function _getAllResources() {
        $resourceTable = $this->get('Auth\Model\ResourceTable');
        return $resourceTable->getAllResources();
    }

    protected function _getRolePermissions() {
        $rolePermissionTable = $this->get('Auth\Model\RolePermissionTable');
        return $rolePermissionTable->getRolePermissions();
    }

}