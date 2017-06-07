<?php
/**
 * Created by IntelliJ IDEA.
 * User: salt
 * Date: 24.04.2017
 * Time: 01:41
 */

namespace Cast\Service;


use Auth\Model\User;
use Auth\Model\UserTable;
use Cast\Model\CharacterTable;
use Tracy\Debugger;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

//@todo refactoring
class CastService
{
    private $loaded = false;
    private $userSet = false;
    private $data;

    /** @var CharacterTable $jobTable */
    private $characterTable;
    /** @var UserTable $userTable */
    private $userTable;

    public function __construct(CharacterTable $characterTable, UserTable $userTable) {
        $this->characterTable = $characterTable;
        $this->userTable = $userTable;
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
                if (isset($famMembersByID[$char['guardian_id']])) {
                    $famMembersByID[ $char['guardian_id'] ]['dependent'][$char['id']] = &$char;
//                    array_push($famMembersByID[$char['guardian_id']]['dependent'], $char);
                } else if ($char['id'] == $fam['head']) {
                    $fam['members'] = array(&$famMembersByID[$char['id']]);
//                    array_push($fam['members'], array(&$famMembersByID[$char['id']]) );
                }
            }
        }
        return $root;
    }
    public function getFamilyMembers() {

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
                'members' => array()
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
            $this->data = $this->characterTable->getAllCastData();
            //insert user names for linking
            $this->getUserNames();
            $this->loaded = true;
        }
    }

    /**
     * Adds the username of the Character to the user objects
     * @throws \Exception
     */
    private function getUserNames(){
        foreach ($this->data as $key => $char) {
            /** @var User $newData */
            $newData = $this->userTable->getUsersBy('id', $char['user_id']);
            $this->data[$key]['userName'] = $newData->name;
            $this->data[$key]['charURL'] = str_replace(" ", "-", $char['name']) . "-" . str_replace(" ", "-", $char['surename']);
        }
        $this->userSet = true;
    }
    public function getCharacterData($name, $username){
        if (!$this->loaded) $this->loadData();
        if (!$this->userSet) $this->getUserNames();

        foreach ($this->data as $key => $char){
            if ($char['charURL'] == $name && $char['userName'] == $username){
                return $char;
            }
        }
        return null;
    }
}