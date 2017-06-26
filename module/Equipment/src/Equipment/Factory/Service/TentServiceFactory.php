<?php
namespace Equipment\Factory\Service;

use Equipment\Service\TentService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TentServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
//        $pL = $sm->getServiceLocator();
        $tentTable = $sm->get('Equipment\Model\TentTable');
        $tentTypesTable = $sm->get('Equipment\Model\TentTypesTable');
        $userService = $sm->get('UserService');
        $cacheService = $sm->get('CacheService');

        return new TentService($tentTable, $tentTypesTable, $userService, $cacheService);
    }
}