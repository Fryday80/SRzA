<?php
namespace Media\Factory;

use Media\Service\MediaService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MediaServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $accessService = $sm->get('AccessService');
        return new MediaService($accessService);
    }
}