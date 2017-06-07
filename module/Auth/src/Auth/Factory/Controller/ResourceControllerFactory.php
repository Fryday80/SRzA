<?php
namespace Auth\Factory\Controller;

use Application\Factory\Basic\MyDefaultFactory;
use Auth\Controller\ResourceController;
use Zend\ServiceManager\ServiceLocatorInterface;

class ResourceControllerFactory extends MyDefaultFactory
{
    public function createService(ServiceLocatorInterface $sm) {
        parent::createService($sm);
        $resTable = $this->get('Auth\Model\ResourceTable');
        $permTable = $this->get('Auth\Model\PermissionTable');
        $cacheService = $this->get('CacheService');
        return new ResourceController($resTable, $permTable, $cacheService);
    }
}