<?php

namespace Auth\Service;


use Application\Service\CacheService;
use Auth\Model\User;
use Auth\Model\UserTable;

class UserService
{
    private $loaded = false;
    /** @var array [ 'name', 'id']  */
    public $clientInfo;
    /** @var  array ['id'] */
    private $idNameHash;
    /** @var UserTable  */
    private $userTable;
    /** @var CacheService  */
    private $cacheService;
    /** @var array [ 'name', 'id']  */
    private $defaultInfo = array (
        'name'  => 'Gast',
        'id'    => 0
    );

    function __construct(UserTable $userTable, CacheService $cacheService)
    {
        $this->userTable = $userTable;
        $this->cacheService = $cacheService;
        $this->clientInfo = $this->defaultInfo;
        $this->load();
    }

    //client = logged in user
    public function updateClientInfo($userId)
    {
        $this->load();
        $this->clientInfo['id'] = $userId;
        $this->clientInfo['name'] = $this->idNameHash[$userId];
    }

    public function getClientInfo($key = null){
        if ($key !== null){
            if (isset($this->clientInfo[$key])) return $this->clientInfo[$key];
        }
        return $this->clientInfo;
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