<?php
namespace Application\Factory\Helper;

use Zend\ServiceManager\FactoryInterface;
use Application\View\Helper\MyUrl;
use Zend\ServiceManager\ServiceLocatorInterface;

class MyUrlFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $accessService = $sm->getServiceLocator()->get('AccessService');
        return new MyUrl($accessService);
    }
}