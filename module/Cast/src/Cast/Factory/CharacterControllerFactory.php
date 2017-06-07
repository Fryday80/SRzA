<?php
namespace Cast\Factory;

use Cast\Controller\CharacterController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CharacterControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $sm = $sm->getServiceLocator();
        $characterTable = $sm->get('Cast\Model\CharacterTable');
        $jobTable = $sm->get('Cast\Model\JobTable');
        $familiesTable = $sm->get('Cast\Model\FamiliesTable');
        $userTable = $sm->get('Auth\Model\UserTable');
        $accessService = $sm->get('AccessService');
        return new CharacterController($characterTable, $jobTable, $familiesTable, $userTable, $accessService);
    }
}