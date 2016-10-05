<?php
namespace Nav\Factory;

use Zend\Navigation\Service\AbstractNavigationFactory;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Navigation\Navigation;

class MainNavigationFactory extends AbstractNavigationFactory
{

    protected function getName()
    {
        return 'Main';
    }

    protected function getPages(ServiceLocatorInterface $serviceLocator)
    {
        if (null === $this->pages) {
            $navTable = $serviceLocator->get('Nav\Model\NavTable');
            $this->pages = $this->preparePages($serviceLocator, $navTable->getNav(0));
        }
        return $this->pages;
    }
}

