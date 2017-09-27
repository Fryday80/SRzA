<?php
namespace Nav\Factory;


use Media\Controller\FileController;
use Media\Service\TeamSpeakService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TeamSpeakServiceFactory implements FactoryInterface
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
        $config = $sm->get('Config')['TeamSpeak'];
        return new TeamSpeakService($config);
    }
}