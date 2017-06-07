<?php
namespace Calendar\Factory\Controller;

use Calendar\Controller\CalendarController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CalendarControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $calendarService = $sm->getServiceLocator()->get('CalendarService');
        $accessService   = $sm->getServiceLocator()->get('AccessService');
        $roleTable       = $sm->getServiceLocator()->get('Auth\Model\RoleTable');
        return new CalendarController($calendarService, $accessService, $roleTable);
    }
}