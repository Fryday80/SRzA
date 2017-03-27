<?php
namespace JSCoder;

use JSCoder\Config\JSCoderConfig;
use JSCoder\Utility\JSCoder;
use JSCoder\Utility\JSCoderHelper;


class Module
{
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

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
//
//    public function getServiceConfig()
//    {
//        return array(
//            'factories' => array(
//                'JSCoder\Config\JSCoderConfig' =>  function($sm) {
//                    $parentLocator = $sm->getServiceLocator();
//                    return new JSCoderConfig($parentLocator);
//                }
//            ),
//        );
//    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'jsCoder' => function($pm) {
                    $parentLocator = $pm->getServiceLocator();
                    $helper =  new JSCoder( new JSCoderConfig($parentLocator) );
                    $pm->injectRenderer($helper);
                    return $helper;
                }
            )
        );
    }
}
