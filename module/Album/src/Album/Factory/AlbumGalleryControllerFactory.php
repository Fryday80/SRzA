<?php
namespace Album\Factory;

use Album\Controller\GalleryController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AlbumGalleryControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator) {
        $sm = $serviceLocator->getServiceLocator();
        $galleryService = $sm->get('GalleryService');
        return new GalleryController($galleryService);
    }
}