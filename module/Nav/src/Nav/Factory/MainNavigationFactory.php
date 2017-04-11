<?php
namespace Nav\Factory;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Zend\Http\Request;
use Zend\Navigation\Service\AbstractNavigationFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class MainNavigationFactory extends AbstractNavigationFactory
{
    /**
     * @var
     */
    private $router;

    protected function getName()
    {
        return 'Main';
    }
    protected function getPages(ServiceLocatorInterface $serviceLocator)
    {
        if (null === $this->pages) {
            $this->loadPages($serviceLocator);
        }
        return $this->pages;
    }
    private function prepareData(&$items) {
        for ($i = 0; $i < count($items); $i++) {
            $value = $items[$i];
            $request = new Request();
            $request->setUri($value['uri']);

            $routeMatch = $this->router->match( $request );
            if($routeMatch !== null ) {
                $namespace = $routeMatch->getParam('__NAMESPACE__');
                $controller = $routeMatch->getParam('controller');
                $action = $routeMatch->getParam('action');
                if ($namespace == null) {
                    $items[$i]['resource'] = $controller;
                } else {
                    $items[$i]['resource'] = $namespace.'\\'.$controller;
                }
                $items[$i]['privilege'] = $action;
            }
            if (isset($value['pages']) && is_array($value['pages'])) {
                $this->prepareData($value['pages']);
            }
        }
    }
    private function loadPages(ServiceLocatorInterface $serviceLocator) {
        if (null === $this->pages) {
            if (file_exists('/dsa.dsa')) {
                //@todo load from cache
            } else {
                $this->router     = $serviceLocator->get('Router');
                $navTable   = $serviceLocator->get('Nav\Model\NavTable');
                $nav = $navTable->getNav(0);
                bdump($nav);

                $a = $this->prepareData($nav);
                $this->pages = $nav;
                bdump($nav);
                //@todo write to cache
            }
        }
    }
}

