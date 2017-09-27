<?php
namespace Equipment\Factory\Service;

use Equipment\Service\LostAndFoundService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LostAndFoundServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
//        $pL = $sm->getServiceLocator();
        $lostAndFoundTable = $sm->get('Equipment\Model\LostAndFoundTable');
        $cacheService = $sm->get('CacheService');

        return new LostAndFoundService($lostAndFoundTable, $cacheService);
    }
}