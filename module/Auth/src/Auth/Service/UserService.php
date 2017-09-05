<?php

namespace Auth\Service;


use Application\Service\CacheService;
use Application\Service\DataService;
use Application\Utility\URLModifier;
use Auth\Model\User;
use Auth\Model\UserSet;
use Auth\Model\UserTable;
use Cast\Service\CastService;
use Media\Service\ImageProcessor;

class UserService extends DataService
{
    /** @var UserTable  */
    private $userTable;
    /** @var CacheService  */
    private $cacheService;
    /** @var CastService  */
    private $castService;
    /** @var ImageProcessor  */
	private $imageProcessor;

	private $loaded = false;

    /** @var  array ['id'] */
    private $idNameHash;

	function __construct(UserTable $userTable, CacheService $cacheService, CastService $castService, ImageProcessor $imageProcessor)
    {
        $this->userTable = $userTable;
        $this->cacheService = $cacheService;
        $this->castService = $castService;
        $this->imageProcessor = $imageProcessor;
        $this->load();
    }

    // cached user information
    public function getUserNameByID($id)
    {
        return $this->idNameHash[$id];
    }
    
    // user data
    public function getUserDataByName($username)
    {
        $return = $this->userTable->getUserByName($username);
        $return = $this->appendUserURL($return);
        return $return;
    }

    public function getAllUsers()
    {
        $return = array();
        $res = $this->userTable->getUsers();
        foreach ($res as $user)
            $return[] = $user;
        $return = $this->appendUsersURLs($return);
        return $return;
    }

    public function getUserIdUserNameList()
    {
        $return = array();
        $res = $this->userTable->getUsers();
        foreach ($res as $user)
            $return[$user->id] = $user->name;
        return $return;
    }

    public function getUserById($id)
    {
        $return = $this->userTable->getUser($id);
        $return = $this->appendUserURL($return);
        return $return;
    }
    
    public function saveUser(User $user)
    {
        $this->userTable->saveUser($user);
        $this->clearCache();
        $this->load();
    }

    public function deleteUserById($id)
    {
        $this->castService->deleteAllUserChars($id);
        $this->userTable->deleteUser($id);
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

	/**
	 * @deprecated 03.09.2017
	 *
	 * @param $userID
	 * @param $tempImageInfo
	 *
	 * @return string
	 */
    public function updateUserImage($userID, $tempImageInfo) {
		//@todo linux might bug here because of / instead of \
        $dataPath = realpath('./Data');
        @mkdir($dataPath . '/users', 0755);
        @mkdir($dataPath . '/users/' . $userID, 0755);
        @mkdir($dataPath . '/users/' . $userID . '/pub', 0755);

        $items = scandir($dataPath . '/users/' . $userID . '/pub');
        foreach ($items as $item)
            if (strlen($item) < 3)
                continue;
            else
                @unlink($dataPath . '/users/' . $userID . '/pub/' . $item);

        $imageName = '/profileImage.' . pathinfo($tempImageInfo['name'], PATHINFO_EXTENSION);
        $thumbName = '/profileImage_small.' . pathinfo($tempImageInfo['name'], PATHINFO_EXTENSION);
        $bigThumbName = '/profileImage_medium.' . pathinfo($tempImageInfo['name'], PATHINFO_EXTENSION);
        $url = '/media/file/users/' . $userID . '/pub' . $imageName;

        $newBasePath = realpath('./Data/users/' . $userID . '/pub');
		$newPath = $newBasePath . $imageName;
        $thumbPath = $newBasePath . $thumbName;
        $bigThumbPath = $newBasePath . $bigThumbName;
        rename($tempImageInfo['tmp_name'], $newPath);

		$this->imageProcessor->createUserImages($newPath, $thumbPath, $bigThumbPath);

		return $url;
    }

    private function appendUsersURLs($data)
    {
        $return = new UserSet();
        foreach ($data as $key => $user)
            $return->addUser($this->appendUserURL($user));
        return $return;
    }

    private function appendUserURL(User $user)
    {
        $url = new URLModifier();
        $user->userURL = $url->toURL($user->name);
        return $user;
    }
}