<?php
namespace Auth\Factory;

use Zend\ServiceManager\FactoryInterface;
use Auth\Model\AuthStorage;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthStorageFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        
        $storage = new AuthStorage('sra');
        return $storage;
    }
}