<?php
namespace Media\Factory\Controller;

use Media\Controller\FileController;
use Media\Controller\TeamSpeakController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TeamSpeakControllerFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return FileController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sm = $serviceLocator->getServiceLocator();
        $tsService = $sm->get('TSService');
        $userService = $sm->get('UserService');
        return new TeamSpeakController($tsService, $userService);
    }
}