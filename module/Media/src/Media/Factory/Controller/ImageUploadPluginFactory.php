<?php
namespace Media\Factory;

use Application\Controller\Plugin\ImageUpload;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ImageUploadPluginFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $config = $sm->getServiceLocator()->get('Config');
        $mediaService = $sm->getServiceLocator()->get('MediaService');
        return new ImageUpload($config, $mediaService);
    }
}