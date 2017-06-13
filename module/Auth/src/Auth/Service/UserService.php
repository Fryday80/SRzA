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
    /** @var array [[idUserName],[userNameID]] */
    private $usersHash;
    /** @var array [ 'name', 'id']  */
    private $defaultInfo = array (
        'name'  => 'Gast',
        'id'    => 0
    );

    /** @var array [ 'name', 'id']  */
    public $clientInfo;
    /** @var User[] */
    private $allUsers = array();

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
        $this->clientInfo['name'] = $this->usersHash['idUserName'][$userId];
    }

    public function getClientInfo($key = null){
        if ($key !== null){
            if (isset($this->clientInfo[$key])) return $this->clientInfo[$key];
        }
        return $this->clientInfo;
    }

    public function getClientName()
    {
        $this->load();
        return $this->getClientInfo('name');
    }
    
    // cached user information
    public function getUserIDByName($name)
    {
        $this->load();
        return $this->usersHash['userNameID'][$name];
    }

    public function getUserNameByID($id)
    {
        $this->load();
        return $this->usersHash['idUserName'][$id];
    }
    
    // user data
    public function getUserDataBy($column, $value)
    {
        if ($column == 'id'){
            $this->load();
            return $this->allUsers[$value];
        }
        return $this->userTable->getUsersBy($column, $value);
    }

    public function getAllUsers()
    {
        $this->load();
        return $this->allUsers;
    }
    
    public function saveUser(User $user)
    {
        $this->userTable->saveUser($user);
        $this->updateCache();
    }

    
    private function load()
    {
        if(!$this->loaded) {
            if ($this->cacheService->hasCache('user/info')) {
                $cache = $this->cacheService->getCache('user/info');
                $this->usersHash = $cache['hash'];
                $this->allUsers = $cache['allUsers'];

            } else {
                $allUsersResultSet = $this->userTable->getUsers();
                foreach ($allUsersResultSet as $user) {
                    $this->allUsers[$user->id] = $user;
                    $this->usersHash['idUserName'][$user->id] = $user->name;
                    $this->usersHash['userNameID'][$user->name] = $user->id;
                }
                $cache['hash'] = $this->usersHash;
                $cache['allUsers'] = $this->allUsers;
                $this->cacheService->setCache('user/info', $cache);
            }
            $this->loaded = true;
        }
    }

    private function clearCache()
    {
        $this->cacheService->clearCache('user/info');
        $this->loaded = false;
    }

    private function updateCache()
    {
        $this->clearCache();
        $this->load();
    }
}