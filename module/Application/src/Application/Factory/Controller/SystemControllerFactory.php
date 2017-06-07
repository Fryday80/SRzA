<?php
namespace Application\Factory\Controller;

use Application\Controller\SystemController;
use Application\Factory\Basic\MyDefaultFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class SystemControllerFactory extends MyDefaultFactory
{
    public function createService(ServiceLocatorInterface $sm) {
        parent::createService($sm);
        $statService = $this->get('StatisticService');
        $mailTemplateTable = $this->get('Application\Model\MailTemplatesTable');
        return new SystemController($statService, $mailTemplateTable);
    }
}