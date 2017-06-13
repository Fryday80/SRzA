<?php
namespace Auth\Factory\Service;

use Zend\ServiceManager\FactoryInterface;
use Auth\Service\AccessService;
use Zend\ServiceManager\ServiceLocatorInterface;

class AccessServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $storage     = $sm->get('Auth\Model\AuthStorage');
        $aclService  = $sm->get('Auth\AclService');
        $authService = $sm->get('AuthService');
        $userService = $sm->get('UserService');
        return new AccessService($aclService, $authService, $storage, $userService);
    }
}