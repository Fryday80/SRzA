<?php
namespace Media\Service;
use Auth\Service\AccessService;
use Media\Utility\FmHelper;
use Zend\Http\Response;


const DATA_PATH = '\data';
const NOT_ALLOWED_IMAGE = 'public/img/imgNotFound.png';
const NOT_FOUND_IMAGE = 'public/img/imgNotFound.png';

const ERROR_STRINGS = [
    'No read permission',
    'No write permission',
    'Folder exists already',
    'File exists already',
    'File not found',
    'Folder not found',
    "Parent Folder doesn't exists",
    'Forbidden name',
    'MediaItem not found',
    "Can't rename Folder",
    "Can't rename File",
    //next is 11
    "The use of '/' is forbidden in the directory or file name.",

];
abstract class ERROR_TYPES {
    const NO_READ_PERMISSION = 0;
    const NO_WRITE_PERMISSION = 1;
    const FOLDER_ALREADY_EXISTS = 2;
    const FILE_ALREADY_EXISTS = 3;
    const FILE_NOT_FOUND = 4;
    const FOLDER_NOT_FOUND = 5;
    const PARENT_NOT_EXISTS = 6;
    const FORBIDDEN_NAME = 7;
    const MEDIA_ITEM_NOT_FOUND = 8;
    const ERROR_RENAMING_FOLDER = 9;
    const ERROR_RENAMING_FILE = 10;
    const FORBIDDEN_CHAR_SLASH = 11;
}

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
class MediaException {
    public $code;
    public $msg;
    public $path;
    function __construct($code, $path) {
        $this->msg = ERROR_STRINGS[$code];
        $this->code = $code;
        $this->path = $path;
    }
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
        $rootPath = $this->realPath($path);
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
        $filePath = $this->realPath($path);
        if (is_file($filePath)) {
            return true;
        }
        return false;
    }

    function isDir($path) {
        $filePath = $this->realPath($path);
        if (is_dir($filePath)) {
            return true;
        }
        return false;
    }

    function createFolder($path) {
        //check if the parent folder exists
        if (!$this->isDir(dirname($path))) {
            return new MediaException(ERROR_TYPES::PARENT_NOT_EXISTS, $path);
        }
        //check if exists already
        if ($this->isDir($path)) {
            return new MediaException(ERROR_TYPES::FOLDER_ALREADY_EXISTS, $path);
        }
        //check if permission is granted
        $perm = $this->getPermission(dirname($path));
        if ($perm['writable'] == 0) {
            return new MediaException(ERROR_TYPES::NO_WRITE_PERMISSION, $path);
        }
        //create new folder
        $realPath = $this->cleanPath($this->dataPath.'/'.$path);
        mkdir($realPath);
        //return newly created folder item
        return $this->getItem($path);

    }

    function renameItem($path, $newName) {
        $item = $this->getItem($path);
        if ($item instanceof MediaException) {
            return $item;
        }

        if ($item->writable == 0) {
            return new MediaException(ERROR_TYPES::NO_WRITE_PERMISSION, $path);
        }

        // forbid to change path during rename
        if(strrpos($newName, '/') !== false) {
            return new MediaException(ERROR_TYPES::FORBIDDEN_CHAR_SLASH, $newName);
        }

        $parentPath = dirname($item->fullPath).'/';
        $targetPath = $this->cleanPath($parentPath.$newName);

        if(file_exists($targetPath)) {
            if (is_dir($targetPath)) {
                return new MediaException(ERROR_TYPES::FOLDER_ALREADY_EXISTS, $path);
            } else {
                return new MediaException(ERROR_TYPES::FILE_ALREADY_EXISTS, $path);
            }
        }
        if(!rename($item->fullPath, $targetPath)) {
            if (is_dir($item->fullPath)) {
                return new MediaException(ERROR_TYPES::ERROR_RENAMING_FOLDER, $path);
            } else {
                return new MediaException(ERROR_TYPES::ERROR_RENAMING_FILE, $path);
            }
        }
        //@todo remove item from item cache, when cache is implemented :)
        return $this->loadItem($targetPath);
    }

    function getFileContent($path) {
        $filePath = $this->realPath($path);
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
        $fullPath = $this->realPath($path);
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
        $fullPath = $this->realPath($path);
        if (!$fullPath) {
            return new MediaException(ERROR_TYPES::MEDIA_ITEM_NOT_FOUND, $path);
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

    /**
     * @param $path
     * @return array with 2 items readable and writable values are 0 and 1
     */
    public function getPermission($path) {
        $fullPath = $this->realPath($path);
        if (!$fullPath) return ['readable' => 0, 'writable' => 0];
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
    private function realPath($path) {
        $realPath = realpath($this->dataPath.'/'.$path);
        if (!$realPath)
            $realPath = realpath($path);

        return $realPath;
    }
    private function parseIniFile($objectivePath) {
        $iniDir = $this->realPath($objectivePath);
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

        $fullPath = $this->realpath($path);
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
            return new MediaException(ERROR_TYPES::MEDIA_ITEM_NOT_FOUND, $path);
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
