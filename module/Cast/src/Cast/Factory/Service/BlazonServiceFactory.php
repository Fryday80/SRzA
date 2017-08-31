<?php
namespace Cast\Factory\Service;

use Cast\Service\BlazonService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BlazonServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
    	/** @var \Cast\Model\BlazonTable $blazonTable */
        $blazonTable = $sm->get('Cast\Model\BlazonTable');
        /** @var \Cast\Service\CastService $castService */
        $castService = $sm->get('CastService');

        return new BlazonService($blazonTable, $castService);
    }
}