<?php
namespace Cast\Factory\Service;

use Cast\Service\BlazonService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BlazonServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
    	/** @var \Cast\Model\Tables\BlazonTable $blazonTable */
        $blazonTable = $sm->get('Cast\Model\Tables\BlazonTable');
        /** @var \Cast\Service\CastService $castService */
        $castService = $sm->get('CastService');

        return new BlazonService($blazonTable, $castService);
    }
}