<?php

namespace Auth\Service;


use Application\Service\CacheService;
use Application\Utility\URLModifier;
use Auth\Model\User;
use Auth\Model\UserSet;
use Auth\Model\UserTable;
use Cast\Service\CastService;

class UserService
{
    private $loaded = false;
    /** @var UserTable  */
    private $userTable;
    /** @var CacheService  */
    private $cacheService;
    /** @var CastService  */
    private $castService;
    /** @var  array ['id'] */
    private $idNameHash;

    function __construct(UserTable $userTable, CacheService $cacheService, CastService $castService)
    {
        $this->userTable = $userTable;
        $this->cacheService = $cacheService;
        $this->castService = $castService;
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

    public function updateUserImage($userID, $tempImageInfo) {
        $dataPath = realpath('./Data');
        @mkdir($dataPath . '/_users', 0755);
        @mkdir($dataPath . '/_users/' . $userID, 0755);
        @mkdir($dataPath . '/_users/' . $userID . '/pub', 0755);

        $items = scandir($dataPath . '/_users/' . $userID . '/pub');
        foreach ($items as $item)
            if (strlen($item) < 3)
                continue;
            else
                @unlink($dataPath . '/_users/' . $userID . '/pub/' . $item);

        $imageName = '/profileImage.' . pathinfo($tempImageInfo['name'], PATHINFO_EXTENSION);
        $url = '/media/file/_users/' . $userID . '/pub' . $imageName;

        $newPath = realpath('./Data/_users/' . $userID . '/pub');
        $newPath = $newPath . $imageName;
        //@todo serach old image and unlink (files can have different extensions)
        @unlink($newPath);
        rename($tempImageInfo['tmp_name'], $newPath);
        $this->createUserThumbnail($newPath);
        return $url;
    }

    /**
     * @param $imagePath
     */
    private function createUserThumbnail($imagePath)
    {
        $img = file_get_contents($imagePath);
        $im = imagecreatefromstring($img);

        ImageAlphaBlending($im, true);
        
        $width = imagesx($im);
        $height = imagesy($im);

        $thumbSizeLimit = 600;
        if ($width < $thumbSizeLimit && $height < $thumbSizeLimit) {}
        else {
            $newheight = $thumbSizeLimit;
            $newwidth = $newheight*$width/$height;

            $srcInfo = pathinfo($imagePath);
            $thumb = imagecreatetruecolor($newwidth, $newheight);
            $transparent = imagecolortransparent($thumb, imagecolorallocatealpha($thumb, 255, 255, 255, 127));
            imagefill($thumb, 0, 0, $transparent);

            imagecopyresized($thumb, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
            switch($srcInfo['extension']) {
                case 'jpg':
                case 'jpeg':
                    imagejpeg($thumb, $imagePath);
                    break;
                case 'png':
                    imagepng($thumb, $imagePath);
                    break;
                case 'gif':
                    imagegif($thumb, $imagePath);

            }
            imagedestroy($thumb);
            imagedestroy($im);
        }
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