<?php
namespace Cast\Factory;

use Cast\Controller\FamilyController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FamilyControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $sm = $sm->getServiceLocator();
        $familiesTable = $sm->get('Cast\Model\FamiliesTable');
        $blazonService = $sm->get('BlazonService');
        return new FamilyController($familiesTable, $blazonService);
    }
}