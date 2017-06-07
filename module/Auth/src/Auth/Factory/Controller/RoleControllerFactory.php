<?php
namespace Auth\Factory\Controller;

use Application\Factory\Basic\MyDefaultFactory;
use Auth\Controller\RoleController;
use Zend\ServiceManager\ServiceLocatorInterface;

class RoleControllerFactory extends MyDefaultFactory
{
    public function createService(ServiceLocatorInterface $sm) {
        parent::createService($sm);
        $roleTable = $this->get('Auth\Model\RoleTable');
        $cacheService = $this->get('CacheService');
        return new RoleController($roleTable, $cacheService);
    }
}