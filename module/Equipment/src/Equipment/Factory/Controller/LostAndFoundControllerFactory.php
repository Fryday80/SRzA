<?php
namespace Equipment\Factory\Controller;

use Equipment\Controller\LostAndFoundController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LostAndFoundControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $pL = $sm->getServiceLocator();
        /** @var \Auth\Service\UserService $userService */
        $userService = $pL->get('UserService');
        /** @var \Auth\Service\AccessService $accessService */
        $accessService = $pL->get('AccessService');
        /** @var \Equipment\Service\EquipmentService $lostAndFoundService */
        $lostAndFoundService = $pL->get('LostAndFoundService');
        /** @var \Media\Service\ImageProcessor $imageProcessor */
        $imageProcessor = $pL->get('ImageProcessor');

        return new LostAndFoundController($lostAndFoundService, $userService, $accessService, $imageProcessor);
    }
}