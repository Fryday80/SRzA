<?php

namespace Auth\Service;


use Application\Service\CacheService;
use Auth\Model\User;
use Auth\Model\UserTable;

class UserService
{
    private $loaded = false;
    /** @var UserTable  */
    private $userTable;
    /** @var CacheService  */
    private $cacheService;
    /** @var  array ['id'] */
    private $idNameHash;

    function __construct(UserTable $userTable, CacheService $cacheService)
    {
        $this->userTable = $userTable;
        $this->cacheService = $cacheService;
        $this->load();
    }

    // cached user information
    public function getUserNameByID($id)
    {
        return $this->idNameHash[$id];
    }
    
    // user data
    public function getUserDataBy($column, $value)
    {
        return $this->userTable->getUsersBy($column, $value);
    }

    public function getAllUsers()
    {
        return $this->userTable->getUsers();
    }
    
    public function saveUser(User $user)
    {
        $this->userTable->saveUser($user);
        $this->clearCache();
        $this->load();
    }


    private function load()
    {
        if(!$this->loaded) {
            if ($this->cacheService->hasCache('user/info')) {
                $this->idNameHash = $this->cacheService->getCache('user/info');

            } else {
                $allUsersResultSet = $this->userTable->getUsers();
                foreach ($allUsersResultSet as $user) {
                    $this->idNameHash[$user->id] = $user->name;
                }
                $this->cacheService->setCache('user/info', $this->idNameHash);
            }
            $this->loaded = true;
        }
    }

    private function clearCache()
    {
        $this->cacheService->clearCache('user/info');
        $this->loaded = false;
    }
}