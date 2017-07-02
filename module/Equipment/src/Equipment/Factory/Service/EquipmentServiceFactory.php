<?php
namespace Equipment\Factory\Service;

use Equipment\Service\EquipmentService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EquipmentServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
//        $pL = $sm->getServiceLocator();
        $equipTable = $sm->get('Equipment\Model\EquipTable');
        $cacheService = $sm->get('CacheService');

        return new EquipmentService($equipTable, $cacheService);
    }
}