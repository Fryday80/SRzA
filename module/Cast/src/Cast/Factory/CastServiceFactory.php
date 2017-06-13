<?php
namespace Cast\Factory;

use Cast\Service\CastService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CastServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $characterTable = $sm->get('Cast\Model\CharacterTable');
        $userService = $sm->get('UserService');
        return new CastService($characterTable, $userService);
    }
}