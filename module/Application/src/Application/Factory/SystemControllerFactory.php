<?php
namespace Application\Factory;

use Application\Controller\SystemController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SystemControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $parentLocator = $sm->getServiceLocator();
        $statService = $parentLocator->get('StatisticService');
        $mailTemplateTable = $parentLocator->get('Application\Model\MailTemplatesTable');
        return new SystemController($statService, $mailTemplateTable);
    }
}