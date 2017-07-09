<?php
namespace Cast\Service;

use Application\Utility\URLModifier;
use Auth\Model\UserTable;
use Cast\Model\CharacterTable;
use Cast\Model\FamiliesTable;
use Cast\Model\JobTable;

class CastService
{
    private $loaded = false;
    private $data;
    private $charsById = [];
    private $depth = 0;
    private $tempFamsHash;

    /** @var CharacterTable $jobTable */
    private $characterTable;
    /** @var JobTable  */
    private $jobTable;
    /** @var FamiliesTable  */
    private $familiesTable;
    /** @var UserTable  */
    private  $userTable;

    public function __construct
    ( CharacterTable $characterTable, JobTable $jobTable, FamiliesTable $familiesTable, UserTable $userTable )
    {
        $this->characterTable = $characterTable;
        $this->jobTable       = $jobTable;
        $this->familiesTable  = $familiesTable;
        $this->userTable    = $userTable;
    }

    // =========================================================== char table
    public function getAllChars () {
        $this->loadData();
        return $this->data;
    }

    public function getCharById($id) {
        $result = $this->characterTable->getById( $id );
        $this->prepareChar($result);
        return $result;
    }
    public function getCharsByUserId($id) {
        $result = $this->characterTable->getByUserId( $id );
        $this->processChars($result);
        return $result;
    }
    public function getCharByTrossId($id) {
        $result = $this->characterTable->getByTrossId( $id );
        $this->processChars($result);
        return $result;
    }
    public function getCharByFamilyId($id) {
        $result = $this->characterTable->getByFamilyId( $id );
        $this->processChars($result);
        return $result;
    }

    public function getCharacterDataById($id){
        if (!$this->loaded) $this->loadData();

        foreach ($this->data as $key => $char){
            if ($char['id'] == $id){
                return $char;
            }
        }
        return null;
    }
    public function getCharacterDataByName($wholeNameLikeURL, $username){
        if (!$this->loaded) $this->loadData();

        foreach ($this->data as $key => $char){
            if ($char['charURL'] == $wholeNameLikeURL && $char['userName'] == $username){
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
        $this->loadData();
        $result = [];
        foreach ($this->data as $char) {
            if ($char['family_id'] == $id) {
                $result[$char['id']] = $char;
            }
        }
        return $result;
    }
    public function getAllPossibleSupervisorsFor($familyID){
        return $this->characterTable->getAllPossibleSupervisorsFor($familyID);
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
            $this->processChars($this->data);
            $this->loaded = true;
        }
    }

    /**
     * prepares data for multiple chars
     * for single char use ->prepareChar
     */
    private function processChars(&$data)
    {
        foreach ($data as &$char) {
            $this->prepareChar($char);
        }
    }
    /**
     * prepares data for ONE char
     */
    private function prepareChar(&$char)
    {
        $url = new URLModifier();
        // inject usernames
        $char['userName'] = $this->userTable->getUser($char['user_id'])->name;
        // injectURL(&$char)
        $char['charURL'] = $url->toURL($char['name']) . "-" . $url->toURL($char['surename']);
        $profileRoot = '/profile/' . $url->toURL($char['userName']);
        $castProfile = $profileRoot . '/' . $char['charURL'];
        $char['profileURL'] = $profileRoot;
        $char['charProfileURL'] = $castProfile;
    }

    private function trimDataForCharTable(&$char)
    {
        $charTableFields = array(
            //'id',
            'user_id',
            'name',
            'surename',
            'gender',
            'birthday',
            'job_id',
            'family_id',
            'guardian_id',
            'supervisor_id',
            'tross_id',
            'vita',
            'active',
        );
        foreach ($char as $key => $field){
            if(!in_array($key, $charTableFields)) {
                unset($char[$key]);
            }
        }
    }

    private function prepareDelete(&$char)
    {
        $this->loadData();
        $dependents = array(
            'charIsSupervisorOf' => array(),
            'charIsGuardianOf'   => array(),
        );
        foreach ($this->data as $character){
            if ($character['supervisor_id'] == $char['id']) array_push($dependents['charIsSupervisorOf'], $character);
            if ($character['guardian_id'] == $char['id'])   array_push($dependents['charIsGuardianOf'], $character);
        }
        foreach($dependents['charIsSupervisorOf'] as $dependent){
            $dependentData = $dependent;
            $this->trimDataForCharTable($dependentData);
            $dependentData['supervisor_id'] = $char['supervisor_id'];
            $this->saveChar($dependent['id'], $dependentData);
        }
        foreach($dependents['charIsGuardianOf'] as $dependent){
            $dependentData = $dependent;
            $this->trimDataForCharTable($dependentData);
            $dependentData['guardian_id'] = $char['guardian_id'];
            $this->saveChar($dependent['id'], $dependentData);
        }
        $this->removeChar($char['id']);
    }

    public function deleteCharById($id)
    {
        $char = $this->getCharacterDataById($id);
        $this->prepareDelete($char);
    }

    public function deleteAllUserChars($userId)
    {
        $usersChars = $this->getCharsByUserId($userId);
        foreach ($usersChars as $char)
            $this->deleteCharById($char['id']);
    }
    /////// std
    public function addChar($data)
    {
        if ($data['user_id'] == "0") $data['user_id'] = 1;
        return $this->characterTable->add($data);
    }
    public function saveChar($id, $data)
    {
        if ($data['user_id'] == "0") $data['user_id'] = 1;
        return $this->characterTable->save($id, $data);
    }
    private function removeChar($id)
    {
        return $this->characterTable->remove($id);
    }

    // =========================================================== family table
    public function getAllFamilies()
    {
        return $this->familiesTable->getAll();
    }

    public function getFamilyById ($id){
        return $result = $this->familiesTable->getById($id);
    }

    public function getFamilyByName($familyName)
    {
        return $result = $this->familiesTable->getByName($familyName);
    }

    /////// std
    public function addFamily ($data)
    {
        return $this->familiesTable->add($data);
    }
    public function saveFamily ($id, $data)
    {
        return $this->familiesTable->save($id, $data);
    }
    public function removeFamily ($id)
    {
        return $this->familiesTable->remove($id);
    }

    // =========================================================== jobs table
    public function getAllJobs()
    {
        return $this->jobTable->getAll();
    }

    public function getJobById($id)
    {
        return $this->jobTable->getById($id);
    }

    /////// std
    public function addJob ($data)
    {
        return $this->jobTable->add($data);
    }
    public function saveJob ($id, $data)
    {
        return $this->jobTable->save($id, $data);
    }
    public function removeJob ($id)
    {
        return $this->jobTable->remove($id);
    }

    // =========================================================== user table
    public function getAllUsers()
    {
        return $this->userTable->getUsers();
    }

    public function getUserNameById($id)
    {
        return $this->userTable->getUser($id);
    }
}