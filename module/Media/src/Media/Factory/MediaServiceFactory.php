<?php
namespace Media\Factory;

use Exception;
use Media\Service\MediaService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MediaServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        try {
            $accessService = $sm->get('AccessService');
            return new MediaService($accessService);
        }catch (Exception $e) {
            bdump($e);
        }
    }
}