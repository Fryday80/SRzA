<?php
namespace Media\Factory;

use Media\Controller\FileBrowserController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FileBrowserControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sm = $serviceLocator->getServiceLocator();
        $mediaService = $sm->get('MediaService');
        return new FileBrowserController($mediaService);

    }
}