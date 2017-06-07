<?php
namespace Application\Factory\Basic;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MyDefaultFactory implements FactoryInterface
{
    protected $sman;
    
    public function createService(ServiceLocatorInterface $sm) {
        $this->sman = $sm;
    }

    protected function get($serviceName)
    {
        if ($this->sman->has($serviceName))return $this->sman->get($serviceName);
        $pL = $this->sman->getServiceLocator();
        if ($pL->has($serviceName))return $pL->get($serviceName);
        return null;
    }
}