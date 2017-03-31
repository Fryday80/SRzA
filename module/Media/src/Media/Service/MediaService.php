<?php
namespace Media\Service;
use Auth\Service\AccessService;
use Media\Utility\FmHelper;
const DATA_PATH = '\data';
const DATA_UPLOAD_PATH  = '\Upload';





class MediaItem {
    public $name;
    public $type;
    public $fullPath;
    public $parentPath;
    public $path;
    public $livePath;
    public $size;
    public $extension = '';
    public $readable  = 1;
    public $writable  = 1;
    public $created   = '';
    public $modified  = '';
    public $timestamp = '';

    function __construct() {}
}
class MediaService {
    protected $dataPath;
    protected $uploadPath;
    protected $accessService;
    private $metaCache;

    function __construct(AccessService $accessService) {
        $this->accessService = $accessService;
        $rootPath = getcwd();
        $this->dataPath = $rootPath.DATA_PATH;
        $this->uploadPath = $rootPath.DATA_UPLOAD_PATH;
        $this->metaCache = [];
        bdump($this->getItems("/"));

    }
    //@todo need to be replaced by getItems -- only used in galleryService.
    /**   DEPRECATED DEPRECATED DEPRECATED DEPRECATED DEPRECATED
     * @param $path
     * @return array
     */
    function getFolderNames($path) {
        $rootPath = realpath($this->dataPath.'/'.$path);
        //check folder restrictions
        $meta = $this->getFolderMeta($path);
        if ($meta && isset($meta['Restrictions']) ) {
            if (isset($meta['Restrictions']['folder'])) {
                if (in_array($this->accessService->getRole(), $meta['Restrictions']['folder']) ) {
                    //@not allowed
                    return [];
                }
            }
        }
        $dir = scandir($rootPath);
        $result = array();
        foreach ($dir as $key => $value) {
            if ($value == '.' || $value == '..') continue;
            if( is_dir ($rootPath.'/'.$value) ) {
                //check folder restrictions in /folder/folder.conf
                $meta = $this->getFolderMeta($path.'/'.$value);
                if ($meta && isset($meta['Restrictions']) ) {
                    if (isset($meta['Restrictions']['folder'])) {
                        if (in_array($this->accessService->getRole(), $meta['Restrictions']['folder']) ) {
                            //@not allowed
                            continue;
                        }
                    }
                }
                array_push($result, array('name' => $value, 'path' => $path.'/'.$value, 'fullPath' => $rootPath.'/'.$value) );
            }
        }
        return $result;
    }

    function fileExists($path) {
        $filePath = realpath($this->dataPath.'/'.$path);
        if (is_file($filePath)) {
            return true;
        }
        return false;
    }

    function getFileContent($path) {
        $filePath = realpath($this->dataPath.'/'.$path);
        if (is_file($filePath)) {
            return file_get_contents($filePath);
        }
        return false;
    }

    function getFolderMeta($path) {
        return $this->parseIniFile($path);
    }

    /**
     * @param $path string
     * @return MediaItem[]
     */
    function getItems($path) {
        $fullPath = realpath($this->dataPath.'/'.$path);
        $result = array();
        if (is_dir($fullPath)) {
           // $rootItem = $this->loadItem($path);
            $dir = scandir($fullPath);
            foreach ($dir as $key => $value) {
                if ($value == '.' || $value == '..') continue;
                $item = $this->loadItem($path.'/'.$value);
                array_push($result, $item);
            }
            return $result;
        } else {
            //@todo error
            return [];
        }
    }

    public function createFileResponse($path, $response) {
        $fullPath = realpath($this->dataPath.'/'.$path);
        if(!$this->checkPermission($path)) {
            return $response->setHttpResponseCode(404);
        }
        /** @var FmHelper */
        $helper = new FmHelper();

        $fileContent =  file_get_contents('Data' . $path);
        $response->setContent($fileContent);
        $response
            ->getHeaders()
            ->addHeaderLine('Content-Transfer-Encoding', 'binary')
            ->addHeaderLine('Content-Type', $helper->mime_type_by_extension($path))
            ->addHeaderLine('Content-Length', strlen($fileContent));

        return $response;
    }

    public function checkPermission($path) {
        $fullPath = realpath($this->dataPath.'/'.$path);
        if (!$fullPath) return false;
        $isDir = is_dir($fullPath);
        $file = basename($fullPath);
        $dir = dirname($fullPath);
        $role = $this->accessService->getRole();
        if ($isDir) {
            $meta = $this->getFolderMeta($path);
            if ($meta && isset($meta['Restrictions']) ) {
                if (isset($meta['Restrictions']['folder'])) {
                    if (in_array($role, $meta['Restrictions']['folder']) ) {
                        //@not allowed
                        return false;
                    }
                }
            }
            return true;
        } else {
            $meta = $this->getFolderMeta($path);
            if ($meta && isset($meta['Restrictions']) ) {
                if (isset($meta['Restrictions']['folder'])) {
                    if (in_array($role, $meta['Restrictions']['folder']) ) {
                        //@not allowed
                        return false;
                    }
                }
            }
            return true;
        }

        return true;
    }

    private function parseIniFile($iniPath) {
        $iniPath .= '/folder.conf';
        $process_sections = true;
        $scanner_mode = INI_SCANNER_TYPED;
        $iniPath = realpath($this->dataPath.'/'.$iniPath);
        if (in_array($iniPath, $this->metaCache)) {
            return $this->metaCache[$iniPath];
        }
        if (is_file($iniPath)) {
            $ini = parse_ini_file($iniPath, $process_sections, $scanner_mode);
            $this->metaCache[$iniPath] = $ini;
            return $ini;
        }
        return false;
    }

    private function loadItem($path) {
        $path = $this->cleanPath($path);
        $fullPath = realpath($this->dataPath.'/'.$path);
        $item = new MediaItem();
        $item->fullPath = $fullPath;
        $item->path = $path;
        if (is_dir($fullPath)) {
            $item->name = basename($path);
            $item->type = 'folder';
//            $item->parentPath = realpath(path . '/..');
        } else if (file_exists($fullPath)){
            $pathInfo = pathinfo($path);
            $item->name = $pathInfo['filename'];
            $item->type = $pathInfo['extension'];
            $item->livePath = $this->cleanPath("/media/file/".$path);
//            $item->parentPath = $pathInfo['dirname'];
        } else {
            //@todo error file not found
        }
        return $item;
    }


    /**
     * Clean path string to remove multiple slashes, etc.
     * @param string $string
     * @return $string
     */
    private function cleanPath($string) {
        // replace backslashes (windows separators)
        $string = str_replace("\\", "/", $string);
        // remove multiple slashes
        $string = preg_replace('#/+#', '/', $string);
        return $string;
    }









//
//
//    function getImportPreview() {
//        $path = $this->uploadPath;
//        $files = $this->readUploadFolder($path);
//        $errors = [];
//        $result = [];
//
//        foreach ($files as $file) {
//            $realPath = realpath($file['fullPath']);
//            $targetPath = $this->dataPath.$file['path'].$file['name'];
//            array_push($result, new MediaItem($realPath));
//            /*
//            if ($file['type'] == 'folder') {
//                //check if folder exist in data folder
//                if (is_dir($targetPath)) {
//                    //exists allready -> go on recursive
//                    $this->import($realPath);
//                } else {
//                    //do not exists -> move hole folder
//                    rename($realPath, $targetPath);
//                }
//            } else {
//                if (!file_exists($targetPath)) {
//                    //move file to target
//                    rename($realPath, $targetPath);
//                } else {
//                    array_push($errors, array(
//                        'msg' => 'File exists allready',
//                        'file' =>$file
//                    ));
//                }
//            }*/
//        }
//        return $result;
//    }
//    function import($path = null) {
//        if ($path == null) $path = $this->uploadPath;
//        $files = $this->readUploadFolder($path);
//        $errors = [];
//        foreach ($files as $file) {
//            $realPath = realpath($file['fullPath']);
//            $targetPath = $this->dataPath.$file['path'].$file['name'];
//            if ($file['type'] == 'folder') {
//                //check if folder exist in data folder
//                if (is_dir($targetPath)) {
//                    //exists allready -> go on recursive
//                    $this->import($realPath);
//                } else {
//                    //do not exists -> move hole folder
//                    rename($realPath, $targetPath);
//                }
//            } else {
//                if (!file_exists($targetPath)) {
//                    //move file to target
//                    rename($realPath, $targetPath);
//                } else {
//                    array_push($errors, array(
//                        'msg' => 'File exists allready',
//                        'file' =>$file
//                    ));
//                }
//            }
//        }
//        return $errors;
//    }
//    function getAlbumFolderNames() {
//        $albumPath = realpath($this->dataPath.'/gallery/');
//        $dir = scandir($albumPath);
//        $result = array();
//        foreach ($dir as $key => $value) {
//            if ($value == '.' || $value == '..') continue;
//            if( is_dir ($albumPath.'/'.$value) ) {
//                array_push($result, $value);
//            }
//        }
//        return $result;
//    }
//    /**
//     * @param {string} $albumName or better the folder name where the images life in
//     * @return {array} array with all images and there paths'
//     */
//    function getAlbumFiles($albumName) {
//        $albumPath = realpath($this->dataPath.'/gallery/'.$albumName);
//        if (is_dir($albumPath) ) {
//            //Cast folder exists -> read all containing files
//            $dir = scandir($albumPath);
//            $result = array();
//            foreach ($dir as $key => $value) {
//                if ($value == '.' || $value == '..') continue;
//                $relPath = str_replace($this->dataPath, '', $albumPath);
//                $relPath = str_replace("\\", "/", $relPath);
//                $fileInfo = pathinfo($relPath.'/'.$value);
//                if ($relPath == '') $relPath = '/';
//                $type = (is_dir($albumPath.'\\'.$value))? 'folder': 'file';
//                $fileInfo['url'] = '/media/image'.$relPath.'/'.$value;
//                $fileInfo['fullPath'] = $albumPath.'\\'.$value;
//
//                array_push($result, $fileInfo);
//            }
//            return $result;
//        }
//        //check if Cast exists
//        //read files
//        //return files
//        return array();
//    }
//
//    /**
//     * @param $path absolute path to folder
//     * @return array array with the folder content
//     */
//    private function readUploadFolder($path) {
//        $dir = scandir($path);
//        $result = array();
//        foreach ($dir as $key => $value) {
//            if ($value == '.' || $value == '..') continue;
//            $relPath = str_replace($this->uploadPath, '', $path);
//            if ($relPath == '') $relPath = '/';
//            $type = (is_dir($path.'\\'.$value))? 'folder': 'file';
//            array_push($result, array(
//                'name' => $value,
//                'path' => $relPath,
//                'fullPath' => $path.'\\'.$value,
//                'type' => $type
//            ));
//        }
//        return $result;
//    }
//    /**
//     * returns a multidimensional array with folders
//     */
//    function getAllFolders() {
//        return array(
//            'name',
//            'path',
//            'folders' => array(
//                'name',
//                'path',
//                'folders' => array())
//        );
//    }
//    /**
//     * @param {string} $path relative path to the data folder
//     */
//    function getFolder($path) {
//        //path exists
//        //load folder.perm
//        //check permission   permission template = recource is media  privileg is /path/to/folder
//        //if no permission ?? redirect to login ?? or return false
//        //return array with file paths'
//    }

}
