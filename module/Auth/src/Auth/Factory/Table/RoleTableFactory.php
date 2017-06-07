<?php
namespace Auth\Factory\Table;

use Zend\ServiceManager\FactoryInterface;
use Auth\Model\RoleTable;
use Zend\ServiceManager\ServiceLocatorInterface;

class RoleTableFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        
        $adapter = $sm->get('Zend\Db\Adapter\Adapter');
        $permissionTable = $sm->get('Auth\Model\PermissionTable');
        $rolePermissionTable = $sm->get('Auth\Model\RolePermissionTable');
        $resourceTable = $sm->get('Auth\Model\ResourceTable');
        return new RoleTable($adapter, $permissionTable, $rolePermissionTable, $resourceTable);
    }
}