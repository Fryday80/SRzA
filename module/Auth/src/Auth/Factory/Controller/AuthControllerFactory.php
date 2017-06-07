<?php
namespace Auth\Factory\Controller;

use Application\Factory\Basic\MyDefaultFactory;
use Auth\Controller\AuthController;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthControllerFactory extends MyDefaultFactory
{
    
    public function createService(ServiceLocatorInterface $sm) {
        parent::createService($sm);
        $storage = $this->get('Auth\Model\AuthStorage');
        $authService = $this->get('AuthService');
        $userTable = $this->get('Auth\Model\UserTable');
        $dynamicHashTable = $this->get('Application\Model\DynamicHashTable');
        $msgService = $this->get('MessageService');
        return new AuthController($storage, $authService, $userTable, $dynamicHashTable, $msgService);
    }
}