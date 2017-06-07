<?php
namespace Auth\Factory\Controller;

use Zend\ServiceManager\FactoryInterface;
use Auth\Controller\ResourceController;
use Zend\ServiceManager\ServiceLocatorInterface;

class ResourceControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $pL = $sm->getServiceLocator();
        $resTable = $pL->get('Auth\Model\ResourceTable');
        $permTable = $pL->get('Auth\Model\PermissionTable');
        $cacheService = $pL->get('CacheService');
        return new ResourceController($resTable, $permTable, $cacheService);
    }
}