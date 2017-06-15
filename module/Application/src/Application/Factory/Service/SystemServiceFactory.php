<?php
namespace Application\Factory\Service;

use Application\Service\SystemService;
use Exception;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SystemServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $accessService = $sm->get('AccessService');
//        $sysLogTable = $sm->get('Application\Model\SystemLogTable');
            return new SystemService($accessService);
    }
}