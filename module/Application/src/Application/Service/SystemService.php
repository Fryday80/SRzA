<?php
namespace Application\Service;

use Auth\Service\AccessService;
use Exception;

class SystemService
{
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
        $this->loadConfig();
    }

    public function getConfig($key) {
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
            switch($this->config[$key]) {
                case 'boolean':
                    if (is_bool($value)) {
                        $this->config[$key]['value'] = $value;
                    } else {
                        throw new Exception('Value must be of type boolean!');
                    }
                break;
                case 'number':
                    if (is_numeric($value)) {
                        $this->config[$key]['value'] = $value;
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
            $this->saveConfig();
        } else {
            throw new Exception('Key not exists!');
        }
    }
    public function logoutUsers() {
        //@todo logout all users except admins
    }

    private function saveConfig() {
        //@todo save config to file
    }
    private function loadConfig() {
        //@todo load config to file
    }
}