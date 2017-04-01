<?php
namespace Media\Service;
use Auth\Service\AccessService;
use Media\Utility\FmHelper;
use Zend\Http\Response;


const DATA_PATH = '\data';
const NOT_ALLOWED_IMAGE = 'public/img/imgNotFound.png';
const NOT_FOUND_IMAGE = 'public/img/imgNotFound.png';



class MediaItem {
    public $name;
    public $type;
    public $fullPath;
    public $parentPath;
    public $path;
    public $livePath;
    public $size;
    public $extension = '';
    public $readable  = 0;
    public $writable  = 0;
    public $created   = '';
    public $modified  = '';
    public $timestamp = '';

    function __construct() {}
}
class MediaService {
    protected $dataPath;
    protected $accessService;
    private $metaCache;

    function __construct(AccessService $accessService) {
        $this->accessService = $accessService;
        $rootPath = getcwd();
        $this->dataPath = $rootPath.DATA_PATH;
        $this->metaCache = [];
        $this->getItems('verein/sm');
        $this->getItems('verein/sm');
        $this->getItems('verein/sm');
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

    /**
     * @param $path string
     * @return MediaItem|null
     */
    public function getItem($path) {
        $fullPath = realpath($this->dataPath.'/'.$path);
        if (!$fullPath) {
            return null;
        }

        return $this->loadItem($path);
    }

    /**
     * @param $path string
     * @param $response Response
     * @return Response
     */
    public function createFileResponse($path, $response) {
        $fullPath = realpath($this->dataPath.$path);
        if (!$fullPath || !is_file($fullPath)) {
            if ($this->isImage($path)) {
                $fullPath = realpath(NOT_FOUND_IMAGE);
                $fileContent =  file_get_contents($fullPath);
                $response->setContent($fileContent);
                $response
                    ->getHeaders()
                    ->addHeaderLine('Content-Transfer-Encoding', 'binary')
                    ->addHeaderLine('Content-Type', FmHelper::mime_type_by_extension($fullPath))
                    ->addHeaderLine('Content-Length', strlen($fileContent));
            }
            return $response->setStatusCode(403, 2);
        }
        if(!$this->getPermission($path)['readable']) {
            if ($this->isImage($path)) {
                $fullPath = realpath(NOT_ALLOWED_IMAGE);
                $fileContent =  file_get_contents($fullPath);
                $response->setContent($fileContent);
                $response
                    ->getHeaders()
                    ->addHeaderLine('Content-Transfer-Encoding', 'binary')
                    ->addHeaderLine('Content-Type', FmHelper::mime_type_by_extension($fullPath))
                    ->addHeaderLine('Content-Length', strlen($fileContent));
            }
            return $response->setStatusCode(404);
        }
        $fileContent =  file_get_contents($fullPath);
        $response->setContent($fileContent);
        $response
            ->getHeaders()
            ->addHeaderLine('Content-Transfer-Encoding', 'binary')
            ->addHeaderLine('Content-Type', FmHelper::mime_type_by_extension($path))
            ->addHeaderLine('Content-Length', strlen($fileContent));

        return $response;
    }

    public function isImage($path) {
        $mime = FmHelper::mime_type_by_extension($path);
        $imagesMime = [
            "image/jpeg",
            "image/png",
            "image/gif",
            "image/bmp",
            "image/svg+xml",
        ];
        return in_array($mime, $imagesMime);
    }
    public function getPermission($path) {
        $fullPath = realpath($this->dataPath.'/'.$path);
        if (!$fullPath) return false;
        $isDir = is_dir($fullPath);
        $file = basename($fullPath);
        $dir = dirname($fullPath);
        $role = $this->accessService->getRole();
        if ($isDir) {
            $meta = $this->getFolderMeta($path);
            if ($meta && isset($meta['Permissions']) ) {
                $readable = 0;
                $writable = 0;
                if (isset($meta['Permissions']['folderRead'])) {
                    if (in_array($role, $meta['Permissions']['folderRead']) ) {
                        $readable = 1;
                    }
                }
                if (isset($meta['Permissions']['folderWrite'])) {
                    if (in_array($role, $meta['Permissions']['folderWrite']) ) {
                        $writable = 1;
                    }
                }
                return ['readable' => $readable, 'writable' => $writable];
            }
        } else {
            $meta = $this->getFolderMeta($path);
            if ($meta && isset($meta['Permissions']) ) {
                $readable = 0;
                $writable = 0;
                if (isset($meta['Permissions']['allRead'])) {
                    if (in_array($role, $meta['Permissions']['allRead']) ) {
                        $readable = 1;
                    }
                }
                if (isset($meta['Permissions']['allWrite'])) {
                    if (in_array($role, $meta['Permissions']['allWrite']) ) {
                        $writable = 1;
                    }
                }
                //file permission exeptions
                if ($meta && isset($meta['FileRestrictions']) ) {
                    if (isset($meta['FileRestrictions'][$role.'Read'])) {
                        if (in_array($file, $meta['FileRestrictions'][$role.'Read']) ) {
                            $readable = 0;
                        }
                    }
                    if (isset($meta['FileRestrictions'][$role.'Write'])) {
                        if (in_array($file, $meta['FileRestrictions'][$role.'Write']) ) {
                            $writable = 0;
                        }
                    }
                }

                return ['readable' => $readable, 'writable' => $writable];
            }
        }
        return ['readable' => 0, 'writable' => 0];
    }

    private function parseIniFile($objectivePath) {
        $iniDir = realpath($this->dataPath.'/'.$objectivePath);
        if (!is_dir($iniDir)) {
            $iniDir = dirname($iniDir);
        }
        $iniPath = $iniDir.'/folder.conf';
        $process_sections = true;
        $scanner_mode = INI_SCANNER_TYPED;
        if (in_array($iniPath, $this->metaCache)) {
            return $this->metaCache[$iniPath];
        }
        $ini = [];
        if (is_file($iniPath)) {
            $ini = parse_ini_file($iniPath, $process_sections, $scanner_mode);
            $this->metaCache[$iniPath] = $ini;
        } else {
            $ini = $this->parseIniFile(dirname($objectivePath));
            unset($ini['FileRestrictions']);
            $this->metaCache[$iniPath] = $ini;
        }
        return $ini;
    }

    /**
     * @param $path
     * @return MediaItem|null
     */
    private function loadItem($path) {
        //@todo add caching
        $path = $this->cleanPath($path);

        $permission = $this->getPermission($path);


        $fullPath = realpath($this->dataPath.'/'.$path);
        $item = new MediaItem();
        $item->fullPath = $fullPath;
        $item->path = $path;
        $item->readable = $permission['readable'];
        $item->writable = $permission['writable'];
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
            return null;
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
