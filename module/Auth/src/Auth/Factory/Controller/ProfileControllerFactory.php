<?php
namespace Auth\Factory\Controller;

use Zend\ServiceManager\FactoryInterface;
use Auth\Controller\ProfileController;
use Zend\ServiceManager\ServiceLocatorInterface;

class ProfileControllerFactory implements FactoryInterface
{
    
    public function createService(ServiceLocatorInterface $sm) {
        $pL = $sm->getServiceLocator();
        $userTable      = $pL->get('Auth\Model\UserTable');
        $familyTable    = $pL->get('Cast\Model\FamiliesTable');
        $jobTable       = $pL->get('Cast\Model\JobTable');
        $accessService  = $pL->get('AccessService');
        $statService    = $pL->get('StatisticService');
        $castService    = $pL->get('CastService');
        return new ProfileController($userTable, $familyTable, $jobTable, $accessService, $statService, $castService);
    }
}