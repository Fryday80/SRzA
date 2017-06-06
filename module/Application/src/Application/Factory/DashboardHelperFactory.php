<?php
namespace Application\Factory;

use Application\View\Helper\DashboardHelper;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DashboardHelperFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        return new DashboardHelper($sm);
    }
}