<?php
namespace Cast\Factory\Controller;

use Cast\Controller\CharacterController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CharacterControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $pL = $sm->getServiceLocator();
        $accessService = $pL->get('AccessService');
        $castService = $pL->get('CastService');
        return new CharacterController($accessService, $castService);
    }
}