<?php
namespace Cast\Factory;

use Cast\Controller\BlazonController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BlazonControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        return new BlazonController($sm->getServiceLocator()->get('BlazonService'));
    }
}