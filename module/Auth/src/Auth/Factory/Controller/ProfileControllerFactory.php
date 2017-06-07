<?php
namespace Auth\Factory\Controller;

use Application\Factory\Basic\MyDefaultFactory;
use Auth\Controller\ProfileController;
use Zend\ServiceManager\ServiceLocatorInterface;

class ProfileControllerFactory extends MyDefaultFactory
{
    
    public function createService(ServiceLocatorInterface $sm) {
        parent::createService($sm);
        $userTable      = $this->get('Auth\Model\UserTable');
        $characterTable = $this->get('Cast\Model\CharacterTable');
        $familyTable    = $this->get('Cast\Model\FamiliesTable');
        $jobTable       = $this->get('Cast\Model\JobTable');
        $accessService  = $this->get('AccessService');
        $statService    = $this->get('StatisticService');
        $castService    = $this->get('CastService');
        return new ProfileController($userTable, $characterTable, $familyTable, $jobTable, $accessService, $statService, $castService);
    }
}