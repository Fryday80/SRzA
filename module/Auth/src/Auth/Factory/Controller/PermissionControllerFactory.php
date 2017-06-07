<?php
namespace Auth\Factory\Controller;

use Zend\ServiceManager\FactoryInterface;
use Auth\Controller\PermissionController;
use Zend\ServiceManager\ServiceLocatorInterface;

class PermissionControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $pL = $sm->getServiceLocator();
        $roleTable           = $pL->get('Auth\Model\RoleTable');
        $rolePermissionTable = $pL->get('Auth\Model\RolePermissionTable');
        $permissionTable     = $pL->get('Auth\Model\PermissionTable');
        $cacheService        = $pL->get('CacheService');
        return new PermissionController($roleTable, $rolePermissionTable, $permissionTable, $cacheService);
    }
}