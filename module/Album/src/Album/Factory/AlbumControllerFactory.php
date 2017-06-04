<?php
namespace Album\Factory;

use Album\Controller\AlbumController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AlbumControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator) {
        $sm = $serviceLocator->getServiceLocator();
        $galleryService = $sm->get('GalleryService');
        return new AlbumController($galleryService);
    }
}