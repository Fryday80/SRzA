<?php
namespace Cast\Factory\Service;

use Cast\Service\BlazonService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BlazonServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $blazonTable = $sm->get('Cast\Model\BlazonTable');
        return new BlazonService($blazonTable, $sm->get('CastService'));
    }
}