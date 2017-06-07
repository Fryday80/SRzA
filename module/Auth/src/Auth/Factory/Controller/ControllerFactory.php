<?php
namespace Auth\Factory\Controller;

use Application\Factory\Basic\MyDefaultFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class ControllerFactory extends MyDefaultFactory
{
    public function createService(ServiceLocatorInterface $sm) {
        parent::createService($sm);
        return;
    }
}