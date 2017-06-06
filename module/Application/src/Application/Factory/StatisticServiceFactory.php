<?php
namespace Application\Factory;

use Application\Service\StatisticService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class StatisticServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $accessService = $sm->get('AccessService');
        $sysLogTable = $sm->get('Application\Model\SystemLogTable');
        return new StatisticService($accessService, $sysLogTable);
    }
}