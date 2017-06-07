<?php
namespace Application\Factory\Controller;

use Application\Controller\SystemController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SystemControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $statService = $sm->getServiceLocator()->get('StatisticService');
        $mailTemplateTable = $sm->getServiceLocator()->get('Application\Model\MailTemplatesTable');
        return new SystemController($statService, $mailTemplateTable);
    }
}