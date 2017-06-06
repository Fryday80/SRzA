<?php
namespace Application\Factory;

use Application\Service\StatisticService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DefaultTablesFactory implements FactoryInterface
{
    
    public function createService(ServiceLocatorInterface $sm) {
        $accessService = $sm->get('AccessService');
        $sysLogTable = $sm->get('Application\Model\SystemLog');
        return new StatisticService($accessService, $sysLogTable);
    }
}