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

    private $charsById = [];
    private $depth = 0;
    private $tempFamsHash;
    public function getStanding($withFam = false) {
        $this->loadData();

        foreach ($this->data as $key => &$char) {
            $this->charsById[$char['id']] = &$char;
            $char['employ'] = array();
            $char['type'] = 'char';
        }
        $root = $this->charsById[1];
        $this->tempFamsHash = [];
        $this->buildStandingTree($root);

        //iterate families and add chars
        foreach ($this->tempFamsHash as &$fam) {
            $famMembersByID = $this->getAllCharsFromFamily($fam['id']);
            foreach ($famMembersByID as $key => &$char) {
                $char['dependent'] = array();
            }
            foreach ($famMembersByID as &$char) {
                if (isset($famMembersByID[$char['guardian_id']]) ) {
                    array_push($famMembersByID[$char['guardian_id']]['dependent'], $char);
                } else if ($char['id'] == $fam['head']) {
                    $fam['members'] = array(&$char);
                }
            }
        }
        return $root;
    }
    public function getAllCharsFromFamily($id) {
        $result = [];
        foreach ($this->data as $char) {
            if ($char['family_id'] == $id) {
                $result[$char['id']] = $char;
            }
        }
        return $result;
    }
    private function buildStandingTree(&$parent) {
        $this->depth++;
        if (isset($this->tempFamsHash[$parent['family_id']])) {
            //add char to family
            array_push($this->tempFamsHash[$parent['family_id']]['members'], $parent);
        } else if($parent['family_id'] != 0) {
            //create family
            $parent['family'] = array(
                'id' => $parent['family_id'],
                'type' => 'family',
                'name' => $parent['family_name'],
                'head' => $parent['id'],
                'members' => array($parent)
            );
            $this->tempFamsHash[$parent['family_id']] = &$parent['family'];
        }
        foreach ($this->charsById as &$value) {
            if ($value['supervisor_id'] == $parent['id']) {
                $this->buildStandingTree($value);
                array_push($parent['employ'], $value);
            }
        }
    }
    /*  *

    private $chars = [];
    private $depth = 0;
    public function getStanding($withFam = false) {
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

     * */
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