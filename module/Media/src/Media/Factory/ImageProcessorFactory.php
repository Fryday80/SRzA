<?php
namespace Media\Factory;

use Media\Service\ImageProcessor;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ImageProcessorFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $config = $sm->get('Config');
        return new ImageProcessor($config);
    }
}