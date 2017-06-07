<?php
namespace Auth\Factory\Table;

use Application\Factory\Basic\MyDefaultFactory;
use Auth\Model\RoleTable;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RoleTableFactory extends MyDefaultFactory
{
    public function createService(ServiceLocatorInterface $sm) {
        parent::createService($sm);
        $adapter = $this->get('Zend\Db\Adapter\Adapter');
        $permissionTable = $this->get('Auth\Model\PermissionTable');
        $rolePermissionTable = $this->get('Auth\Model\RolePermissionTable');
        $resourceTable = $this->get('Auth\Model\ResourceTable');
        return new RoleTable($adapter, $permissionTable, $rolePermissionTable, $resourceTable);
    }
}