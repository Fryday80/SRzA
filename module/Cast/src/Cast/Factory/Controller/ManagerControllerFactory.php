<?php
namespace Cast\Factory\Controller;

use Cast\Controller\ManagerController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ManagerControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $sm = $sm->getServiceLocator();
        $characterTable = $sm->get('Cast\Model\CharacterTable');
        $jobTable = $sm->get('Cast\Model\JobTable');
        $familiesTable = $sm->get('Cast\Model\FamiliesTable');
        return new ManagerController($characterTable, $jobTable, $familiesTable);
    }
}