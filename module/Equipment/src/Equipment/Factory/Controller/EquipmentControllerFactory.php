<?php
namespace Equipment\Factory\Controller;

use Equipment\Controller\EquipmentController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EquipmentControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $pL = $sm->getServiceLocator();
        /** @var \Auth\Service\UserService $userService */
        $userService = $pL->get('UserService');
        /** @var \Auth\Service\AccessService $accessService */
        $accessService = $pL->get('AccessService');
        /** @var \Equipment\Service\EquipmentService $equipService */
        $equipService = $pL->get('EquipmentService');
        /** @var array $config */
        $config = $pL->get('config');
        /** @var \Media\Service\ImageProcessor $imageProcessor */
        $imageProcessor = $pL->get('ImageProcessor');

        return new EquipmentController($config, $equipService, $userService, $accessService, $imageProcessor);
    }
}