<?php
namespace Auth\Factory\Controller;

use Zend\ServiceManager\FactoryInterface;
use Auth\Controller\AuthController;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthControllerFactory implements FactoryInterface
{
    
    public function createService(ServiceLocatorInterface $sm) {
        $pL = $sm->getServiceLocator();
        $storage = $pL->get('Auth\Model\AuthStorage');
        $authService = $pL->get('AuthService');
        $userTable = $pL->get('Auth\Model\UserTable');
        $dynamicHashTable = $pL->get('Application\Model\DynamicHashTable');
        $msgService = $pL->get('MessageService');
        $statisticService = $pL->get('StatisticService');
        return new AuthController($storage, $authService, $userTable, $dynamicHashTable, $msgService, $statisticService);
    }
}