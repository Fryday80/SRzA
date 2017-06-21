<?php
namespace Cast\Factory\Service;

use Cast\Service\CastService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CastServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $characterTable = $sm->get('Cast\Model\CharacterTable');
        $jobTable       = $sm->get('Cast\Model\JobTable');
        $familiesTable  = $sm->get('Cast\Model\FamiliesTable');
        $userTable      = $sm->get('Auth\Model\UserTable');
        return new CastService( $characterTable, $jobTable, $familiesTable, $userTable );
    }
}