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

    private function readFolder($path) {
        $dir = scandir($path);
        $result = array();
        foreach ($dir as $key => $value) {
            if ($value == '.' || $value == '..') continue;
            $relPath = str_replace ($this->uploadPath, '', $path);
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
    function import($path = null) {
        if ($path == null) $path = $this->uploadPath;
        $files = $this->readFolder($path);
        print('<pre>');
        var_dump($files);
        print('</pre>');
        foreach ($files as $file) {
            if ($file['name'] == 'gallery') continue;
            $realPath = realpath($file['fullPath']);
            if ($file['type'] == 'folder') {
                //check if folder exist in data folder
                $targetPath = $this->dataPath.$file['path'].$file['name'];
                if (is_dir($targetPath)) {
                    //allready exists
                    print($targetPath.' -> exists allready');
                    //$this->import(realpath($file['fullPath']));
                } else {
                    rename($realPath, $targetPath);
                }
                //if not move complet folder
                //else run import against this folder path
                //$this->import(realpath($path.'/'.$file['name']));
            } else {

            }
            //rename($dir['fullPath'], $this->dataPath.'/');

        }
        //import alben
        print('<pre>');
        //var_dump($dir);die;
        die;
        //check import folder for files, and if any, import them.
        //if gallery folder has a sub folder check if this folder exists allready
        //then move content images to storage
        //if not move folder and add permission
        //clear import folder
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
    /**
     * @param {string} $path relative path to the data folder
     */
    function getAlbumFiles($albumName) {
        //check if album exists
        //read files
        //return files
    }
}