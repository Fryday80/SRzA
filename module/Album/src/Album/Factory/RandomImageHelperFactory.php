<?php
namespace Album\Factory;

use Album\Utility\RandomImageHelper;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RandomImageHelperFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $galleryService = $sm->getServiceLocator()->get('GalleryService');
        return new RandomImageHelper($galleryService);
    }
}