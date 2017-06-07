<?php
namespace Auth\Factory\Controller;

use Zend\ServiceManager\FactoryInterface;
use Auth\Controller\RoleController;
use Zend\ServiceManager\ServiceLocatorInterface;

class RoleControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $pL = $sm->getServiceLocator();
        $roleTable = $pL->get('Auth\Model\RoleTable');
        $cacheService = $pL->get('CacheService');
        return new RoleController($roleTable, $cacheService);
    }
}