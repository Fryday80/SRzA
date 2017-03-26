<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace JSCoder;

use Application\View\Helper\MyUrl;
use Application\View\Helper\sraForm;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Application\View\Helper\DataTableHelper;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        date_default_timezone_set ("Europe/Berlin");
    }
//    public function getConfig()
//    {
//        return include __DIR__ . '/config/module.config.php';
//    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'jsModule' => function() {          // I think this is not needed
                    return new JSModule();          // I think this is not needed
                },                                  // I think this is not needed
                'jsRegistration' => function() {    // I think this is not needed
                    return new JSRegistration();    // I think this is not needed
                },                                  // I think this is not needed
                'jsCoder' => function() {
                    return new JSCoder( new JSRegistration() );
                }

            )
        );
    }
}
