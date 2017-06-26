<?php
namespace Equipment\Factory\Controller;

use Equipment\Controller\EquipmentController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EquipmentControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $pL = $sm->getServiceLocator();
        $tentService = $pL->get('TentService');
        $userService = $pL->get('UserService');
        $accessService = $pL->get('AccessService');

        return new EquipmentController($tentService, $userService, $accessService);
    }
}