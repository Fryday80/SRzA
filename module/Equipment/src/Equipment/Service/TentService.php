<?php

namespace Equipment\Service;


use Application\Service\CacheService;
use Equipment\Model\Tent;
use Equipment\Model\TentTable;
use Equipment\Model\TentTypesTable;
use Equipment\Model\TentColorsTable;

class TentService
{
    // tables
    /** @var  TentTable */
    private $tentTable;
    /** @var  TentTypesTable */
    private $typesTable;
    /** @var  TentColorsTable */
    private $colorsTable;

    // services
    /** @var CacheService  */
    private $cache;

    // hashes
    /** @var bool  */
    private $tentHashLoaded = false;
    /** @var  Tent[] */
    private $tentIdTent;
    /** @var  Tent[] */
    private $userIdTent;


    public function __construct(TentTable $tentTable, TentTypesTable $tentTypesTable, TentColorsTable $tentColorsTable, CacheService $cacheService)
    {
        $this->tentTable = $tentTable;
        $this->typesTable = $tentTypesTable;
        $this->colorsTable = $tentColorsTable;
        $this->cache = $cacheService;
    }
    
    //======================================================== Tent Table
    public function getAllTents()
    {
        if (!$this->tentHashLoaded)
        {
            $res = $this->tentTable->getAll();
            foreach ($res as $item) {
                $this->updateTentHash(new Tent($item));
            }
            $this->tentHashLoaded = true;
        }
        //@todo caching and hashing
        return $this->tentIdTent;
    }

    public function getTentsByUserId($id)
    {
        if (!isset($this->userIdTent[$id])) {
            $res = $this->tentTable->getByUserId($id);
            foreach ($res as $item) {
                $this->updateTentHash(new Tent($item));
            }
        }
        return $this->userIdTent[$id];
    }

    public function getTentById($id)
    {
        if (!isset($this->tentIdTent[$id])) {
            $tent = new Tent($this->tentTable->getById($id));
            $this->updateTentHash($tent);
        }
        return $this->tentIdTent[$id];
    }

    public function saveTent(Tent $tentData)
    {
        $tentData->id =  $this->tentTable->save($tentData);
        $this->updateTentHash($tentData);
    }

    public function deleteTentById($id)
    {
        return $this->tentTable->removeById($id);
    }

    public function deleteTentByUserId($userId)
    {
        return $this->tentTable->removeByUserId($userId);
    }

    private function updateTentHash($tentData)
    {
        $replace = false;
        $this->tentIdTent[$tentData->id] = $tentData;
        if (isset($this->userIdTent[$tentData->userId][0]))
        {
            foreach ($this->userIdTent[$tentData->userId] as $key => $item) {
                if ($item->id == $tentData->id) {
                    $this->userIdTent[$tentData->userId][$key] = $tentData;
                    $replace = true;
                }
            }
            if (!$replace)
                $this->userIdTent[$tentData->userId][] = $tentData;
        }
        else $this->userIdTent[$tentData->userId][] = $tentData;
    }
    //======================================================== TentTypes Table
    public function getAllTypes()
    {
        //@todo caching and hashing
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

    public function saveType()
    {
        return;
    }

    public function deleteType()
    {
        return;
    }
    //======================================================== TentColors Table
    public function getAllColors()
    {
        //@todo caching and hashing
        return $this->colorsTable->getAll();
    }

    public function getColorIDColorNameList()
    {
        $return = array();
        $res = $this->getAllColors();
        foreach ($res as $item) {
            $return[$item['id']] = $item['name'];
        }
        return $return;
    }

    public function saveColor()
    {
        return;
    }
    public function deleteColor()
    {
        return;
    }
}