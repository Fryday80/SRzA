<?php
namespace Media\Factory\Service;


use Media\Controller\FileController;
use Media\Service\TeamSpeakService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TeamSpeakServiceFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param $sm $serviceLocator
     * @return FileController
     */
    public function createService(ServiceLocatorInterface $sm)
    {
        $config = $sm->get('Config')['TeamSpeak'];
        return new TeamSpeakService($config);
    }
}