<?php
namespace Auth\Factory\Service;

use Auth\Service\UserService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $table     = $sm->get('Auth\Model\UserTable');
        $cacheService  = $sm->get('CacheService');
        $castService   = $sm->get('CastService');
        return new UserService($table, $cacheService, $castService);
    }
}