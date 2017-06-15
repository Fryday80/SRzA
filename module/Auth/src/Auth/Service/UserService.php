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

    public function updateUserImage($userID, $tempImageInfo) {
        $dataPath = realpath('./data');
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

        $newPath = realpath('./data/_users/' . $userID . '/pub');
        $newPath = $newPath . $imageName;
        //@todo serach old image and unlink (files can have different extensions)
        @unlink($newPath);
        rename($tempImageInfo['tmp_name'], $newPath);
        $this->createUserThumbnail($newPath);
        return $url;
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

    /**
     * @param $imagePath
     */
    private function createUserThumbnail($imagePath)
    {
        $img = file_get_contents($imagePath);
        $im = imagecreatefromstring($img);

        $width = imagesx($im);
        $height = imagesy($im);

        $thumbSizeLimit = 600;
        if ($width < $thumbSizeLimit && $height < $thumbSizeLimit) {}
        else {
            $newheight = $thumbSizeLimit;
            $newwidth = $newheight*$width/$height;

            $srcInfo = pathinfo($imagePath);
            $thumb = imagecreatetruecolor($newwidth, $newheight);

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
}