<?php
namespace Cast\Factory\Controller;

use Cast\Controller\FamilyController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FamilyControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $sm = $sm->getServiceLocator();
        $castService = $sm->get('CastService');
        $blazonService = $sm->get('BlazonService');
        return new FamilyController($castService, $blazonService);
    }
}