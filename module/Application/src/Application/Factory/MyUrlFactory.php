<?php
namespace Application\Factory;

use Application\View\Helper\MyUrl;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MyUrlFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $accessService = $sm->getServiceLocator()->get('AccessService');
        return new MyUrl($accessService);
    }
}