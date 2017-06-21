<?php
namespace Cast\Factory\Controller;

use Cast\Controller\CastController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CastControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        return new CastController($sm->getServiceLocator()->get('CastService'));
    }
}