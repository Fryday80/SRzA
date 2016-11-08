<?php
namespace Media\Service;

class MediaService {
    protected $dataPath;
    protected $uploadPath;

    function __construct() {
        $rootPath = getcwd();
        $this->dataPath = $rootPath.'\Data';
        $this->uploadPath = $rootPath.'\Upload';
    }

    function import($path = null) {
        if ($path == null) $path = $this->uploadPath;
        $files = $this->readUploadFolder($path);
        $errors = [];
        foreach ($files as $file) {
            $realPath = realpath($file['fullPath']);
            $targetPath = $this->dataPath.$file['path'].$file['name'];
            if ($file['type'] == 'folder') {
                //check if folder exist in data folder
                if (is_dir($targetPath)) {
                    //exists allready -> go on recursive
                    $this->import($realPath);
                } else {
                    //do not exists -> move hole folder
                    rename($realPath, $targetPath);
                }
            } else {
                if (!file_exists($targetPath)) {
                    //move file to target
                    rename($realPath, $targetPath);
                } else {
                    array_push($errors, array(
                        'msg' => 'File exists allready',
                        'file' =>$file
                    ));
                }
            }
        }
        return $errors;
    }
    function getAlbumFolderNames() {
        $albumPath = realpath($this->dataPath.'/gallery/');
        $dir = scandir($albumPath);
        $result = array();
        foreach ($dir as $key => $value) {
            if ($value == '.' || $value == '..') continue;
            print($albumPath.'\ '.$value);
            print('<br>');
            if( is_dir ($albumPath.'/'.$value) ) {
                array_push($result, $value);
            }
        }
        return $result;
    }
    /**
     * @param {string} $albumName or better the folder name where the images life in
     * @return {array} array with all images and there paths'
     */
    function getAlbumFiles($albumName) {
        $albumPath = realpath($this->dataPath.'/gallery/'.$albumName);
        if (is_dir($albumPath) ) {
            //album folder exists -> read all containing files
            $dir = scandir($albumPath);
            $result = array();
            foreach ($dir as $key => $value) {
                if ($value == '.' || $value == '..') continue;
                $relPath = str_replace($this->dataPath, '', $albumPath);
                $relPath = str_replace("\\", "/", $relPath);
                $fileInfo = pathinfo($relPath.'/'.$value);
                if ($relPath == '') $relPath = '/';
                $type = (is_dir($albumPath.'\\'.$value))? 'folder': 'file';
                $fileInfo['url'] = '/media/image'.$relPath.'/'.$value;
                $fileInfo['fullPath'] = $albumPath.'\\'.$value;

                array_push($result, $fileInfo);
            }
            return $result;
        }
        //check if album exists
        //read files
        //return files
        return array();
    }

    /**
     * @param $path absolute path to folder
     * @return array array with the folder content
     */
    private function readUploadFolder($path) {
        $dir = scandir($path);
        $result = array();
        foreach ($dir as $key => $value) {
            if ($value == '.' || $value == '..') continue;
            $relPath = str_replace($this->uploadPath, '', $path);
            if ($relPath == '') $relPath = '/';
            $type = (is_dir($path.'\\'.$value))? 'folder': 'file';
            array_push($result, array(
                'name' => $value,
                'path' => $relPath,
                'fullPath' => $path.'\\'.$value,
                'type' => $type
            ));
        }
        return $result;
    }
    /**
     * returns a multidimensional array with folders
     */
    function getAllFolders() {
        return array(
            'name',
            'path',
            'folders' => array(
                'name',
                'path',
                'folders' => array())
        );
    }
    /**
     * @param {string} $path relative path to the data folder
     */
    function getFolder($path) {
        //path exists
        //load folder.perm
        //check permission   permission template = recource is media  privileg is /path/to/folder
        //if no permission ?? redirect to login ?? or return false
        //return array with file paths'
    }

}
