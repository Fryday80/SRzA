<?php
namespace Equipment\Factory\Service;

use Equipment\Service\TentService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TentServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $pL = $sm->getServiceLocator();
        $tentTable = $pL->get('Equipment\Model\TentTable');
        $tentTypesTable = $pL->get('Equipment\Model\TentTypesTable');
        $tentColorsTable = $pL->get('Equipment\Model\TentColorsTable');
        $cacheService = $pL->get('CacheService');

        return new TentService($tentTable, $tentTypesTable, $tentColorsTable, $cacheService);
    }
}