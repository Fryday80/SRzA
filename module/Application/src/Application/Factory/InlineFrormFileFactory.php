<?php
namespace Application\Factory;

use Application\View\Helper\InlineFromFile;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class InlineFromFileFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        return new InlineFromFile($sm);
    }
}