<?php
namespace Auth\Factory\Table;

use Zend\ServiceManager\FactoryInterface;
use Auth\Model\User;
use Auth\Model\UserTable;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Hydrator\ObjectProperty;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserTableFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        
        $resultSetPrototype = new HydratingResultSet();
        $resultSetPrototype->setHydrator(new ObjectProperty());
        $resultSetPrototype->setObjectPrototype(new User());
        return new UserTable($sm->get('Zend\Db\Adapter\Adapter'), $resultSetPrototype);
    }
}