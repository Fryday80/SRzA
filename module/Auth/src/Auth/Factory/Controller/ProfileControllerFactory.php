<?php
namespace Auth\Factory\Controller;

use Zend\ServiceManager\FactoryInterface;
use Auth\Controller\ProfileController;
use Zend\ServiceManager\ServiceLocatorInterface;

class ProfileControllerFactory implements FactoryInterface
{
    
    public function createService(ServiceLocatorInterface $sm) {
        $pL = $sm->getServiceLocator();
        $familyTable    = $pL->get('Cast\Model\FamiliesTable');
        $jobTable       = $pL->get('Cast\Model\JobTable');
        $statService    = $pL->get('StatisticService');
        $castService    = $pL->get('CastService');
        $userService    = $pL->get('UserService');
        return new ProfileController($familyTable, $jobTable, $statService, $castService, $userService);
    }
}