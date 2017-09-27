<?php
namespace Media;

use Media\Model\FileTable;
use Media\Service\MediaService;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
//            'Zend\Loader\StandardAutoloader' => array(
//                'namespaces' => array(
//                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
//                )
//            )
        );
    }

    public function getConfig()
    {
    	$a1 = include __DIR__ . '/config/module.config.php';
    	$a2 = include __DIR__ . '/config/route.config.php';
    	$a3 = include __DIR__ . '/config/configuration.config.php';
        return array_merge_recursive($a1, $a2 , $a3) ;
    }
}