<?php
namespace Cast\Factory;

use Cast\Controller\CharacterController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CharacterControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $pL = $sm->getServiceLocator();
        $characterTable = $pL->get('Cast\Model\CharacterTable');
        $jobTable = $pL->get('Cast\Model\JobTable');
        $familiesTable = $pL->get('Cast\Model\FamiliesTable');
        $accessService = $pL->get('AccessService');
        $userService = $pL->get('UserService');
        return new CharacterController($characterTable, $jobTable, $familiesTable, $accessService, $userService);
    }
}