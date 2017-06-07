<?php
namespace Cast\Factory;

use Cast\Controller\JobController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class JobControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $sm = $sm->getServiceLocator();
        $jobTable = $sm->get('Cast\Model\JobTable');
        return new JobController($jobTable);
    }
}