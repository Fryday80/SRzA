<?php

namespace Equipment\Service;


use Application\Service\CacheService;
use Auth\Service\UserService;
use Equipment\Model\EnumTentShape;
use Equipment\Model\Tent;
use Equipment\Model\TentTable;
use Equipment\Model\TentTypesTable;

class TentService
{
    // tables
    /** @var  TentTable */
    private $tentTable;
    /** @var  TentTypesTable */
    private $typesTable;

    // services
    /** @var UserService  */
    private $userService;
    /** @var CacheService  */
    private $cache;


    public function __construct (
        TentTable $tentTable, TentTypesTable $tentTypesTable,
        UserService $userService, CacheService $cacheService )
    {
        $this->tentTable = $tentTable;
        $this->typesTable = $tentTypesTable;
        $this->userService = $userService;
        $this->cache = $cacheService;
    }
    //==========================================================
    
    public function makeTentReadable(Tent $tent)
    {
        $tent->readable['User'] = $this->userService->getUserById($tent->userId);
        $tent->readable['Form'] = EnumTentShape::TRANSLATION[$tent->shape];
        $tent->readable['Type'] = $this->typesTable->getById($tent->type);
    }
    
    //======================================================== Tent Table
    public function getAllTents()
    {
        return $this->tentTable->getAll();
    }

    public function getTentsByUserId($id)
    {
        return $this->tentTable->getByUserId($id);
    }

    public function getTentById($id)
    {
        return $this->tentTable->getById($id);
    }

    public function saveTent(Tent $tentData)
    {
        $tentData->id =  $this->tentTable->save($tentData);
    }

    public function deleteTentById($id)
    {
        return $this->tentTable->removeById($id);
    }

    public function deleteTentByUserId($userId)
    {
        return $this->tentTable->removeByUserId($userId);
    }

    //======================================================== TentTypes Table
    public function getAllTypes()
    {
        return $this->typesTable->getAll();
    }

    public function getTypeIDTypeNameList()
    {
        $return = array();
        $res = $this->getAllTypes();
        foreach ($res as $item) {
            $return[$item['id']] = $item['name'];
        }
        return $return;
    }

    public function saveType($data)
    {
        if ($data['id'] == "") return $this->typesTable->add($data);
        return $this->typesTable->save($data);
    }

    public function deleteType($id)
    {
        return $this->typesTable->remove($id);
    }
}