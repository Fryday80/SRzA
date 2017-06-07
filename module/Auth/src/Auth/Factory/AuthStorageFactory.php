<?php
namespace Auth\Factory;

use Application\Factory\Basic\MyDefaultFactory;
use Auth\Model\AuthStorage;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthStorageFactory extends MyDefaultFactory
{
    public function createService(ServiceLocatorInterface $sm) {
        parent::createService($sm);
        $storage = new AuthStorage('sra');
        return $storage;
    }
}