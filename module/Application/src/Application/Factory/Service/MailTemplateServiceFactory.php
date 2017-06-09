<?php
namespace Application\Factory\Service;

use Application\Service\MailTemplateService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MailTemplateServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $mailTemplatesTable = $sm->get('Application\Model\MailTemplatesTable');
        return new MailTemplateService($mailTemplatesTable);
    }
}