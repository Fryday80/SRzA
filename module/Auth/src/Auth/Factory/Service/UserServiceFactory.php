<?php
namespace Auth\Factory\Service;

use Auth\Service\UserService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
    	/** @var \Auth\Model\UserTable $table */
        $table     = $sm->get('Auth\Model\UserTable');
        /** @var \Application\Service\CacheService $cacheService */
        $cacheService  = $sm->get('CacheService');
        /** @var \Cast\Service\CastService $castService */
        $castService   = $sm->get('CastService');
        /** @var \Media\Service\ImageProcessor $imageProcessor */
        $imageProcessor   = $sm->get('ImageProcessor');
        /** @var \Auth\Service\AccessService $imageProcessor */
        $accessService   = $sm->get('AccessService');

        return new UserService($table, $cacheService, $castService, $accessService, $imageProcessor);
    }
}