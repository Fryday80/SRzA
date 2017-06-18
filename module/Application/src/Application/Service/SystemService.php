<?php
namespace Application\Service;

use Auth\Service\AccessService;
use Exception;
use Zend\Mvc\MvcEvent;

class SystemService
{
    public $configPath = '/storage/system.json';
    /** @var  AccessService */
    private $accessService;

    private $config = array(
        'maintenance' => array(
            'type' => 'boolean',
            'value' => false
        ),
        'logoutUsers' => array(
            'type' => 'function',
            'value' => array()
        ),
    );

    public function __construct(AccessService $accessService) {
        $this->accessService = $accessService;
        $this->configPath = getcwd().$this->configPath;
        $this->loadConfig();
    }

    public function logoutUsers() {
        //@todo logout all users except admins
    }


    public function getConfig($key = null) {
        if ($key === null) {
            return $this->config;
        }
        if (key_exists($key, $this->config)) {
            if ($this->config[$key]['type'] === 'function') {
                throw new Exception('Config key not readable!');
            }
            return $this->config[$key]['value'];
        } else {
            throw new Exception('Config key not exist!');
        }
    }

    public function setConfig($key, $value) {
        if (key_exists($key, $this->config)) {
            switch($this->config[$key]['type']) {
                case 'boolean':
                    if (is_bool($value)) {
                        $this->config[$key]['value'] = ($value)? true: false;
                    } else {
                        throw new Exception('Value must be of type boolean!');
                    }
                break;
                case 'number':
                    if (is_numeric((float)$value)) {
                        $this->config[$key]['value'] = (float)$value;
                    } else {
                        throw new Exception('Value must be of a numeric type!');
                    }
                    break;
                case 'string':
                    if (is_string($value)) {
                        $this->config[$key]['value'] = $value;
                    } else {
                        throw new Exception('Value must be of type string!');
                    }
                    break;
                case 'function':
                    if (method_exists($this, $value)) {
                        $this->$value($value);
                    } else {
                        throw new Exception('No function with this name!');
                    }
                    break;
            }
        } else {
            throw new Exception('Key not exists!');
        }
    }

    public function onFinish(MvcEvent $e) {
        $this->saveConfig();
    }

    private function saveConfig() {
        $content = serialize($this->config);
        file_put_contents($this->configPath, $content);
    }

    private function loadConfig() {
        if (!file_exists($this->configPath) ) {
            $this->saveConfig();
        }
        $content = file_get_contents(realpath($this->configPath));
        $this->config = unserialize($content);
    }
}