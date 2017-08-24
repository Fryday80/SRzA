<?php
namespace Equipment\Factory\Controller\Plugin;

use Application\Controller\Plugin\EquipmentImageUpload;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EquipmentImageUploadPluginFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $config = $sm->getServiceLocator()->get('Config');
        $mediaService = $sm->getServiceLocator()->get('MediaService');
		$imageProcessor = $sm->getServiceLocator()->get('ImageProcessor');
        return new EquipmentImageUpload($config, $mediaService, $imageProcessor);
    }
}