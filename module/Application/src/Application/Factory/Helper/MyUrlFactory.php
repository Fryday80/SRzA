<?php
namespace Application\Factory\Helper;

use Application\Factory\Basic\MyDefaultFactory;
use Application\View\Helper\MyUrl;
use Zend\ServiceManager\ServiceLocatorInterface;

class MyUrlFactory extends MyDefaultFactory
{
    public function createService(ServiceLocatorInterface $sm) {
        parent::createService($sm);
        $accessService = $this->getServiceLocator()->get('AccessService');
        return new MyUrl($accessService);
    }
}