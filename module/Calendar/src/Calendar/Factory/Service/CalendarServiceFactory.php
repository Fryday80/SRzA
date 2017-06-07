<?php
namespace Calendar\Factory\Service;

use Calendar\Service\CalendarService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CalendarServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $cacheService = $sm->get('CacheService');
        return new CalendarService($cacheService);
    }
}