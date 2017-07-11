<?php
namespace Cast\Factory\Controller;

use Cast\Controller\JobController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class JobControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $sm = $sm->getServiceLocator();
        $castService = $sm->get('CastService');
        $blazonService = $sm->get('BlazonService');
        return new JobController($castService, $blazonService);
    }
}