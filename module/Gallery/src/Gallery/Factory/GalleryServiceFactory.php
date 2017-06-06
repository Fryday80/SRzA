<?php
namespace Gallery\Factory;

use Gallery\Service\GalleryService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GalleryServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $mediaService = $sm->get('MediaService');
        $cacheService = $sm->get('CacheService');
        return new GalleryService($mediaService, $cacheService);
    }
}