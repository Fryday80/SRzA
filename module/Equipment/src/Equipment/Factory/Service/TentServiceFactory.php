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
        $tentColorsTable = $sm->get('Equipment\Model\TentColorsTable');
        $cacheService = $sm->get('CacheService');

        return new TentService($tentTable, $tentTypesTable, $tentColorsTable, $cacheService);
    }
}