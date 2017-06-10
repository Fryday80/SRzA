<?php
namespace Application\Factory\Controller;

use Application\Controller\SystemController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SystemControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $statService = $sm->getServiceLocator()->get('StatisticService');
        $mailTemplateService = $sm->getServiceLocator()->get('MessageService');
        return new SystemController($statService, $mailTemplateService);
    }
}