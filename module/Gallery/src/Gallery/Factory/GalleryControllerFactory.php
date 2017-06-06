<?php
namespace Gallery\Factory;

use Gallery\Controller\GalleryController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GalleryControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $parentLocator = $sm->getServiceLocator();
        $service = $parentLocator->get('GalleryService');
        return new GalleryController($service);
    }
}