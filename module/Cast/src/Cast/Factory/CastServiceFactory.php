<?php
namespace Cast\Factory;

use Cast\Service\CastService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CastServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $characterTable = $sm->get('Cast\Model\CharacterTable');
        $userTable = $sm->get('Auth\Model\UserTable');
        return new CastService($characterTable, $userTable);
    }
}