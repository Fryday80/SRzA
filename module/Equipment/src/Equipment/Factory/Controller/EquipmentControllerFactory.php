<?php
namespace Equipment\Factory\Controller;

use Equipment\Controller\EquipmentController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EquipmentControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $pL = $sm->getServiceLocator();
        $userService = $pL->get('UserService');
        $accessService = $pL->get('AccessService');
        $equipService = $pL->get('EquipmentService');
        $config = $pL->get('config');

        return new EquipmentController($config, $equipService, $userService, $accessService);
    }
}