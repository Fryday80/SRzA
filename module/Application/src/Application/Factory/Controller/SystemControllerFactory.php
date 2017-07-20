<?php
namespace Application\Factory\Controller;

use Application\Controller\SystemController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SystemControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $systemService = $sm->getServiceLocator()->get('SystemService');
        $statService = $sm->getServiceLocator()->get('StatisticService');
        $mailTemplateService = $sm->getServiceLocator()->get('MessageService');
        $cacheService = $sm->getServiceLocator()->get('CacheService');
        $test = $sm->getServiceLocator()->get('ImageProcessor');
        return new SystemController($systemService, $statService, $mailTemplateService, $cacheService, $test);
    }
}