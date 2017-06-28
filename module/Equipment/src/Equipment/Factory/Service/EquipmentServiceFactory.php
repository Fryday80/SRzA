<?php
namespace Equipment\Factory\Service;

use Equipment\Service\EquipmentService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EquipmentServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
//        $pL = $sm->getServiceLocator();
        $tentTypesTable = $sm->get('Equipment\Model\TentTypesTable');
        $equipTable = $sm->get('Equipment\Model\EquipTable');
        $userService = $sm->get('UserService');
        $cacheService = $sm->get('CacheService');

        return new EquipmentService($tentTypesTable, $equipTable, $userService, $cacheService);
    }
}