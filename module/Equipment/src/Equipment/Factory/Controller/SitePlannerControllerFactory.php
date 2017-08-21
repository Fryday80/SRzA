<?php
namespace Equipment\Factory\Controller;

use Equipment\Controller\SitePlannerController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SitePlannerControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $pL = $sm->getServiceLocator();
        $equipService = $pL->get('EquipmentService');
        $sitePlanTable = $pL->get('Equipment\Model\SitePlannerTable');
        $mediaService = $pL->get('MediaService');
        return new SitePlannerController($equipService, $sitePlanTable, $mediaService);
    }
}