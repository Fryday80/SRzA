<?php
namespace Application\Factory\Service;

use Application\Service\MessageService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MessageServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $mailTemplatesTable = $sm->get('Application\Model\MailTemplatesTable');
        return new MessageService($mailTemplatesTable);
    }
}