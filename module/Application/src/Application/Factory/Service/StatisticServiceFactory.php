<?php
namespace Application\Factory\Service;

use Application\Factory\Basic\MyDefaultFactory;
use Application\Service\StatisticService;
use Zend\ServiceManager\ServiceLocatorInterface;

class StatisticServiceFactory extends MyDefaultFactory
{
    public function createService(ServiceLocatorInterface $sm) {
        parent::createService($sm);
        $accessService = $this->get('AccessService');
        $sysLogTable = $this->get('Application\Model\SystemLogTable');
        return new StatisticService($accessService, $sysLogTable);
    }
}