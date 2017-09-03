<?php
namespace Media\Factory;

use Application\Controller\Plugin\ImageUpload;
use Application\Controller\Plugin\MediaPlugin;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MediaPluginFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $config = $sm->getServiceLocator()->get('Config');
        $mediaService = $sm->getServiceLocator()->get('MediaService');
        return new MediaPlugin($config, $mediaService);
    }
}