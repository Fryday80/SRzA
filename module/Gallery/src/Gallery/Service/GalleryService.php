<?php
namespace Gallery\Service;

use Gallery\Model\AlbumModel;
use Application\Service\CacheService;
use Media\Service\MediaException;
use Media\Service\MediaService;

Class GalleryService
{
    /**
     * @var MediaService
     */
    private $mediaService;
    /** @var  CacheService */
    private $cacheService;
    private $galleryPath = "/gallery";


    public function __construct(MediaService $mediaService, CacheService $cacheService) {
        $this->mediaService = $mediaService;
        $this->cacheService = $cacheService;
    }

    public function getAllAlbums() {
        if ($this->cacheService->hasCache('album')) {
            return $this->cacheService->getCache('album');
        }
        $result = array();
        $galleryDirs = $this->mediaService->getItems($this->galleryPath);
        if ($galleryDirs instanceof MediaException) {
            return $galleryDirs;
        }
        foreach ($galleryDirs as $key => $value) {
            if ($value->readable == 0) continue;
            $album = $this->loadAlbum($value->path);
            if ($album == null) continue;
            array_push($result,  $album);
        }
        $this->cacheService->setCache('album', $result);
        return $result;
    }

    private function loadAlbum($path) {
        $meta = $this->mediaService->getFolderMeta($path);
        if ($meta instanceof MediaException) {
            //@todo trigger system warning $meta->msg;
            return null;
        }
        if (is_array($meta) && isset($meta['Album'])) {
            $a = new AlbumModel($path, $meta);

            if (!($a instanceof AlbumModel)) return null;
            $items = $this->mediaService->getItems($path);
            $result = [];
            foreach ($items as $key => $value) {
                if ($value->readable == 0) continue;
                if ($value->type != 'folder' && $value->type != 'conf') {
                    array_push($result, $value);
                }
            }
            $a->images = $result;


//            $a->loadImages();
            return $a;
        }
    }
    public function getAlbum($name) {
        $path = $this->galleryPath.'/'.$name;
        $meta = $this->mediaService->getFolderMeta($path);
        if (is_array($meta) && isset($meta['Album'])) {
            $album = $this->loadAlbum($path);
            return $album;
        } else {
            if ($meta instanceof MediaException) {
                dump($meta->msg);
            } else {
                dump("path not exists  -  " . $path);
            }
            return null;
        }
    }

    public function getRandomImage($count = 1) {
        //get random album
        $galleryDirs = $this->getAllAlbums();
        if (count($galleryDirs) == 0) return [];
        $randomIndex = rand(0, count($galleryDirs) -1);
        $album = $galleryDirs[$randomIndex];
        if (!$album) return [];
        if (count($album) <= $count) {
            return $album->getAllImages();
        }
        $allImages = $album->getAllImages();
        shuffle($allImages);
        $result = [];
        for($i = 0; $i < $count; $i++) {
            array_push($result, $allImages[$i]);
        }
        return $result;
    }

}