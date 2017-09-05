<?php
namespace Media\Factory;

use Application\Controller\Plugin\ImagePlugin;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ImagePluginFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $config = $sm->getServiceLocator()->get('Config');
        $mediaService = $sm->getServiceLocator()->get('MediaService');
        return new ImagePlugin($config, $mediaService);
    }
}