<?php
namespace Media\Factory\Service;


use Media\Service\TeamSpeakService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TeamSpeakServiceFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param $sm $serviceLocator
     * @return TeamSpeakService
     */
    public function createService(ServiceLocatorInterface $sm)
    {
        $config = $sm->get('Config')['TeamSpeak'];
        return new TeamSpeakService($config);
    }
}