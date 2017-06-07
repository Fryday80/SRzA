<?php
namespace Auth\Factory\Controller;

use Application\Factory\Basic\MyDefaultFactory;
use Auth\Controller\PermissionController;
use Zend\ServiceManager\ServiceLocatorInterface;

class PermissionControllerFactory extends MyDefaultFactory
{
    public function createService(ServiceLocatorInterface $sm) {
        parent::createService($sm);
        $roleTable           = $this->get('Auth\Model\RoleTable');
        $rolePermissionTable = $this->get('Auth\Model\RolePermissionTable');
        $permissionTable     = $this->get('Auth\Model\PermissionTable');
        $cacheService        = $this->get('CacheService');
        return new PermissionController($roleTable, $rolePermissionTable, $permissionTable, $cacheService);
    }
}