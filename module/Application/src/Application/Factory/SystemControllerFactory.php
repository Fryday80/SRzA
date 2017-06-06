<?php
namespace Application\Factory;

use Application\Controller\SystemController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SystemControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $statService = $sm->get('StatisticService');
        $MailTemplateTable = $sm->get('Application\Model\MailTemplatesTable');
        return new SystemController($statService, $MailTemplateTable);
    }
}