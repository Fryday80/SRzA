<?php
namespace Auth\Factory\Table;

use Application\Factory\Basic\MyDefaultFactory;
use Auth\Model\User;
use Auth\Model\UserTable;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Hydrator\ObjectProperty;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserTableFactory extends MyDefaultFactory
{
    public function createService(ServiceLocatorInterface $sm) {
        parent::createService($sm);
        $resultSetPrototype = new HydratingResultSet();
        $resultSetPrototype->setHydrator(new ObjectProperty());
        $resultSetPrototype->setObjectPrototype(new User());
        return new UserTable($this->get('Zend\Db\Adapter\Adapter'), $resultSetPrototype);
    }
}