<?php
namespace Auth\Factory\Controller;

use Zend\ServiceManager\FactoryInterface;
use Auth\Controller\UserController;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $pL = $sm->getServiceLocator();
        $accessService = $pL->get('AccessService');
        $userTable = $pL->get('Auth\Model\UserTable');
        $roleTable = $pL->get('Auth\Model\RoleTable');
        $mediaService = $pL->get('MediaService');
        $userService = $pL->get('UserService');
        return new UserController($userTable, $accessService, $roleTable, $mediaService, $userService );
    }
}