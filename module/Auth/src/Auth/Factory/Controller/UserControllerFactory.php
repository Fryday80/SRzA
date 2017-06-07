<?php
namespace Auth\Factory\Controller;

use Application\Factory\Basic\MyDefaultFactory;
use Auth\Controller\UserController;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserControllerFactory extends MyDefaultFactory
{
    public function createService(ServiceLocatorInterface $sm) {
        parent::createService($sm);
        $accessService = $this->get('AccessService');
        $userTable = $this->get('Auth\Model\UserTable');
        $roleTable = $this->get('Auth\Model\RoleTable');
        return new UserController($userTable, $accessService, $roleTable);
    }
}