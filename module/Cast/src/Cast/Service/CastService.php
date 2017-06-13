<?php
namespace Cast\Service;

use Auth\Service\UserService;
use Cast\Model\CharacterTable;

class CastService
{
    private $loaded = false;
    private $data;
    private $charsById = [];
    private $depth = 0;
    private $tempFamsHash;

    /** @var CharacterTable $jobTable */
    private $characterTable;
    /** @var UserService $userService */
    private $userService;

    public function __construct(CharacterTable $characterTable, UserService $userService) {
        $this->characterTable = $characterTable;
        $this->userService = $userService;
    }

    public function getAll () {
        $this->loadData();
        return $this->data;
    }
    public function getById($id) {
        $result = $this->characterTable->getById($id);
        $this->processChar($result);
        return $result;
    }
    public function getByUserId($id) {
        $result = $this->characterTable->getByUserId($id);
        $this->processChar($result);
        return $result;
    }
    public function getByTrossId($id) {
        $result = $this->characterTable->getByTrossId($id);
        $this->processChar($result);
        return $result;
    }
    public function getByFamilyId($id) {
        $result = $this->characterTable->getByFamilyId($id);
        $this->processChar($result);
        return $result;
    }

    public function getCharacterData($name, $username){
        if (!$this->loaded) $this->loadData();

        foreach ($this->data as $key => $char){
            if ($char['charURL'] == $name && $char['userName'] == $username){
                return $char;
            }
        }
        return null;
    }

    public function getStanding($withFam = false) {
        $this->loadData();

        foreach ($this->data as $key => &$char) {
            $this->charsById[$char['id']] = &$char;
            $char['employ'] = array();
            $char['type'] = 'char';
            $this->prepareChar($char);
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
                } else if ($char['id'] == $fam['head']) {
                    $fam['members'] = array(&$famMembersByID[$char['id']]);
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

    public function deleteChar($name, $username)
    {
        $char = $this->getCharacterData($name, $username);
        $this->prepareDelete($char);
        //@todo
//        delete
//        $this->characterTable->delete(array('user_id' => $this->userService->getUserIDByName($username)));
    }

    public function deleteAllUserChars($userId)
    {
        //@todo
        $this->characterTable->removeAllCharsFromUser($userId);
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
    private function loadData() {
        if (!$this->loaded) {
            $this->data = $this->characterTable->getAll();
            //insert user names for linking
            $this->processChar($this->data);
            $this->loaded = true;
        }
    }

    private function processChar(&$data)
    {
        foreach ($data as &$char) {
            $this->prepareChar($char);
        }
    }
    private function prepareChar(&$data)
    {
        $this->injectUserNames($data);
        $this->injectURL($data);
    }

    /**
     * Adds the username of the Character to the user objects
     * @throws \Exception
     */
    private function injectUserNames(&$char)
    {
        $char['userName'] = $this->userService->getUserNameByID($char['user_id']);
    }
    private function injectURL(&$char){
            $char['charURL'] = str_replace(" ", "-", $char['name']) . "-" . str_replace(" ", "-", $char['surename']);
            $profileRoot = '/profile/' . $char['userName'];
            $castProfile = $profileRoot . '/' . $char['charURL'];
        
            $char['profileURL'] = $profileRoot;
            $char['charProfileURL'] = $castProfile;
    }

    private function prepareDelete(&$char)
    {
        // set employees employer to own employer
        foreach ($char['employ'] as &$employe){
            $employe['supervisor_id'] = $char['supervisor_id'];
        }
    }
}