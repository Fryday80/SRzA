<?php
namespace Application;

use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\Regex;
use Zend\Validator\AbstractValidator;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $sm = $e->getApplication()->getServiceManager();
        date_default_timezone_set ("Europe/Berlin");
        $translator = $sm->get('translator');
        AbstractValidator::setDefaultTranslator($translator);
        $eventManager = $e->getApplication()->getEventManager();
        $statsService = $sm->get('StatisticService');
        $cashService = $sm->get('CacheService');
        $eventManager->attach('dispatch', array($statsService, 'onDispatch'));
        $eventManager->attach('dispatch.error', array($statsService, 'onError'));
        $eventManager->attach('finish', array($statsService, 'onFinish'));
        $eventManager->attach('finish', array($cashService, 'onFinish'));
        $systemService = $sm->get('SystemService');
        $eventManager->attach('finish', array($systemService, 'onFinish'));
        if ($systemService->getConfig('maintenance')) {
            $eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'maintainRoute'), 9999);
        }
        return $this;
    }

    public function maintainRoute(MvcEvent $e){
        $router = $e->getRouter();
        $accessService = $e->getApplication()->getServiceManager()->get('AccessService');
        if ($accessService->getRole() === 'Administrator') {
            return;
        }
        //create route.
        $route = Regex::factory(array(
            'regex' => '/(?<url>(?s).*)',
            'spec' => '/%url%',
            'defaults' => array(
                'controller' => 'Application\Controller\System',
                'action' => 'maintenance',
            ),
        ));
        $router->addRoute('maintenance', $route);
    }
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
//            'Zend\Loader\StandardAutoloader' => array(
//                'namespaces' => array(
//                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
//                ),
//            ),
        );
    }
}
