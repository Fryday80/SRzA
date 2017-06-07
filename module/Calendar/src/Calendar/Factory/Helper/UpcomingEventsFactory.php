<?php
namespace Calendar\Factory\Helper;

use Calendar\View\Helper\UpcomingEvents;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UpcomingEventsFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $storage    = $sm->getServiceLocator()->get('Auth\Model\AuthStorage');
        $calService = $sm->getServiceLocator()->get('CalendarService');
        return new UpcomingEvents($storage, $calService);
    }
}