<?php
/**
 * Created by IntelliJ IDEA.
 * User: salt
 * Date: 24.04.2017
 * Time: 01:41
 */

namespace Cast\Service;


use Cast\Model\CharacterTable;
use Tracy\Debugger;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CastService implements ServiceLocatorAwareInterface
{
    private $serviceLocator;
    private $loaded = false;
    private $data;

    function __construct() {

    }
    private $chars = [];
    private $depth = 0;
    public function getStanding() {
        $this->loadData();

        foreach ($this->data as $key => &$char) {
            $this->chars[$char['id']] = &$char;
            $char['employ'] = array();
        }
        $root = $this->chars[1];
        $this->buildStandingTree($root);
        return $root;
    }

    private function buildStandingTree(&$parent) {
        $this->depth++;
        foreach ($this->chars as &$value) {
            if ($value['supervisor_id'] == $parent['id']) {
                if ($this->depth < 10) {
                    $this->buildStandingTree($value);
                }
                array_push($parent['employ'], $value);
            }
        }
    }
    private function loadData() {
        if (!$this->loaded) {
            /** @var CharacterTable $charTable */
            $charTable = $this->getServiceLocator()->get('Cast\Model\CharacterTable');
            $this->data = $charTable->getAllCastData();
            $this->loaded = true;
        }
    }


    /**
     * Set service locator
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get service locator
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator() {
        return $this->serviceLocator;
    }
}