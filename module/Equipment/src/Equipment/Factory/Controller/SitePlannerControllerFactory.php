<?php
namespace Equipment\Factory\Controller;

use Equipment\Controller\SitePlannerController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SitePlannerControllerFactory implements FactoryInterface
{
    
    public function createService(ServiceLocatorInterface $sm) {
//        $pL = $sm->getServiceLocator();
//        $msgService = $pL->get('TentService');
        return new SitePlannerController();
    }
}