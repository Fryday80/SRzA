<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */

chdir(dirname(__DIR__));

//tracy debug
$logPath = realpath(__DIR__.'/../logs');
require 'vendor/tracy/tracy/src/tracy.php';
use Tracy\Debugger;

Debugger::enable(Debugger::DEVELOPMENT, $logPath);
Debugger::$strictMode = true;
require 'vendor/zarganwar/performance-panel/src/Panel.php';
require 'vendor/zarganwar/performance-panel/src/Register.php';

//use Zarganwar\PerformancePanel\Register;
use Zarganwar\PerformancePanel;

Debugger::getBar()->addPanel(new PerformancePanel\Panel());








// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}
//require_once 'config/application.config.php';
//echo "sers"; die;

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
//try {
    Zend\Mvc\Application::init(require 'config/application.config.php')->run();
//} catch (Exception $e ){
//    echo $e->getMessage();
//}
