<?php

namespace Auth\Service;


use Application\Service\CacheService;
use Auth\Model\UserTable;

class UserService
{
    /** @var UserTable  */
    private $userTable;
    /** @var CacheService  */
    private $cacheService;
    /** @var array [[idUserName],[userNameID]] */
    private $usersHash;
    /** @var array [ 'userName', 'userID']  */
    private $defaultInfo = array (
        'userName'  => 'Gast',
        'userID'    => 0
    );

    /** @var array [ 'userName', 'userID']  */
    public $clientInfo;

    function __construct(UserTable $userTable, CacheService $cacheService)
    {
        $this->userTable = $userTable;
        $this->cacheService = $cacheService;
        $this->clientInfo = $this->defaultInfo;
        $this->caching();
    }

    public function updateClientInfo($userId)
    {
        // if guest
        if ($userId < 1) {
            $this->clientInfo = $this->defaultInfo;
        }
        // if no changes
        elseif ($this->clientInfo['userID'] == $userId) {}
        // on log in
        else {
            $this->clientInfo['userID'] = $userId;
            $this->clientInfo['userName'] = $this->usersHash['idUserName'][$userId];
        }
        bdump($this->clientInfo);
    }

    public function getUserIDByName($name)
    {
        return $this->usersHash['userNameID'][$name];
    }

    public function getUserNameByID($id)
    {
        return $this->usersHash['idUserName'][$id];
    }

    public function getClientInfo($key = null){
        if ($key !== null){
            if (isset($this->clientInfo[$key])) return $this->clientInfo[$key];
        }
        return $this->clientInfo;
    }

    private function caching()
    {
        if ($this->cacheService->hasCache('user/info')) {
            $this->usersHash = $this->cacheService->getCache('user/info');
        } else {
            $allUsers = $this->userTable->getUsers();
            foreach ($allUsers as $user) {
                $this->usersHash['idUserName'][$user->id] = $user->name;
                $this->usersHash['userNameID'][$user->name] = $user->id;
            }
            $this->cacheService->setCache('user/info', $this->usersHash);
        }
    }
}