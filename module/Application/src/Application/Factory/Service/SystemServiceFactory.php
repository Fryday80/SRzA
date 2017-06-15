<?php
namespace Application\Factory\Service;

use Zend\ServiceManager\FactoryInterface;
use Application\Service\StatisticService;
use Zend\ServiceManager\ServiceLocatorInterface;

class SystemServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
//        $accessService = $sm->get('AccessService');
//        $sysLogTable = $sm->get('Application\Model\SystemLogTable');
        return new StatisticService(null, null);
    }
}