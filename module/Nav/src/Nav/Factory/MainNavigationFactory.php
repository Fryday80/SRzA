<?php
namespace Nav\Factory;

use Application\Service\CacheService;
use Zend\Http\Request;
use Zend\Navigation\Service\AbstractNavigationFactory;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zarganwar\PerformancePanel\Register;


class MainNavigationFactory extends AbstractNavigationFactory
{
    private $router;
    /**
     * @var $cache CacheService
     */
    private $cache;

    protected function getName()
    {
        return 'Main';
    }
    protected function getPages(ServiceLocatorInterface $serviceLocator)
    {
        $this->cache = $serviceLocator->get('CacheService');
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

            if ($value['role_id'] == 0) {
                $items[$i]['resource'] = 'Role';
                $items[$i]['privilege'] = $value['role_name'];
            }else {
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
            }
            if (isset($value['pages']) && is_array($value['pages'])) {
                $this->prepareData($value['pages']);
            }
        }
    }
    private function loadPages(ServiceLocatorInterface $serviceLocator) {
        if (null === $this->pages) {
            if ($this->cache->hasCache('nav/main')) {
                $this->pages = $this->cache->getCache('nav/main');
            } else {
                $this->router     = $serviceLocator->get('Router');
                $navTable   = $serviceLocator->get('Nav\Model\NavTable');
                $nav = $navTable->getNav(0);
                $this->prepareData($nav);
                $this->pages = $this->preparePages($serviceLocator, $nav);
//                $this->pages = $nav;

                $this->cache->setCache('nav/main', $nav);
            }
        }
    }
}

