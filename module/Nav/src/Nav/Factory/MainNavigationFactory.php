<?php
namespace Nav\Factory;

use Zend\Navigation\Service\AbstractNavigationFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class MainNavigationFactory extends AbstractNavigationFactory
{
//    private $navTable;
//    private $application;
//
//    public function __construct($sm)
//    {
//        $this->navTable = $sm->get('Nav\Model\NavTable');
//        $this->application = $sm->get('Application');
//    }

    protected function getName()
    {
        return 'Main';
    }

//    protected function getPages(ServiceLocatorInterface $serviceLocator)

    protected function getPages(ServiceLocatorInterface $serviceLocator)
    {
        if (null === $this->pages) {
            $navTable = $serviceLocator->get('Nav\Model\NavTable');
            $this->pages = $this->preparePages($serviceLocator, $navTable->getNav(0));
        }
        return $this->pages;
//        dump($serviceLocator);
//        if (null === $this->pages) {
////            $c = new \Zend\Navigation\Navigation();
////            $this->pages = $this->preparePages($serviceLocator, $this->navTable->getNav(0));
////            $this->pages = $this->preparePages($container, $pages);
//
////            $routeMatch  = $this->application->getMvcEvent()->getRouteMatch();
////            $router      = $this->application->getMvcEvent()->getRouter();
////            $request     = $this->application->getMvcEvent()->getRequest();
////
////            // HTTP request is the only one that may be injected
////            if (! $request instanceof Request) {
////                $request = null;
////            }
////
////            return $this->injectComponents($this->navTable->getNav(0), $routeMatch, $router, $request);
//        }
//        return $this->pages;
    }
}

