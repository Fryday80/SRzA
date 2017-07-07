<?php
namespace Media\Service;
use Auth\Service\AccessService;
use Exception;
use Media\Utility\FmHelper;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Zend\Http\Response;
use Zend\ServiceManager\ServiceManager;
use ZipArchive;



const DATA_PATH = '/Data';
const TRASH_BIN_PATH = '/_trash';
const NOT_ALLOWED_IMAGE = 'public/img/imgNotFound.png';
const NOT_FOUND_IMAGE = 'public/img/imgNotFound.png';

const ERROR_STRINGS = [
    'No read permission',
    'No write permission',
    'Folder already exists',
    'File already exists',
    'File not found',
    'Folder not found',
    "Parent folder doesn't exists",
    'Forbidden name',
    'MediaItem not found',
    "Can't rename folder",
    "Can't rename file",//i = 10
    "The use of '/' is forbidden in the directory or file name",
    "Destination folder not found",
    'No write permission in destination folder',
    'Destination already exists',
    "Can't move folder",
    "Can't move file",
    "Can't delete folder",
    "Can't delete file",
    "Can't copying folder",
    "Can't copying file",//i = 20
    "Can't reading file",
    "Can't writing file",
    "Can't read folders",
    "Can't write to folder",
    "No ZIP extension",
    "Error while zipping",
    'No read permission in sub folders',
    'No write permission in sub folders',
    'Folder is not in data path',

];
class MediaService {
    protected $dataPath;
    protected $accessService;
    private $metaCache;

    function __construct(AccessService $accessService) {
        try {
            $this->accessService = $accessService;
            $rootPath = getcwd();
            $this->dataPath = $this->cleanPath($rootPath.DATA_PATH);
            $this->metaCache = [];
        } catch (Exception $e) {
            bdump($e);
        }
    }
    //@todo need to be replaced by getItems -- only used in galleryService.
    //@todo Stage 2 Deprecated
//    /**   DEPRECATED DEPRECATED DEPRECATED DEPRECATED DEPRECATED
//     * @param $path
//     * @return array
//     */
//    function getFolderNames($path) {
//        $rootPath = $this->realPath($path);
//        //check folder restrictions
//        $meta = $this->getFolderMeta($path);
//        if ($meta && isset($meta['Restrictions']) ) {
//            if (isset($meta['Restrictions']['folder'])) {
//                if (in_array($this->accessService->getRole(), $meta['Restrictions']['folder']) ) {
//                    //@not allowed
//                    return [];
//                }
//            }
//        }
//        $dir = scandir($rootPath);
//        $result = array();
//        foreach ($dir as $key => $value) {
//            if ($value == '.' || $value == '..') continue;
//            if( is_dir ($rootPath.'/'.$value) ) {
//                //check folder restrictions in /folder/folder.conf
//                $meta = $this->getFolderMeta($path.'/'.$value);
//                if ($meta && isset($meta['Restrictions']) ) {
//                    if (isset($meta['Restrictions']['folder'])) {
//                        if (in_array($this->accessService->getRole(), $meta['Restrictions']['folder']) ) {
//                            //@not allowed
//                            continue;
//                        }
//                    }
//                }
//                array_push($result, array('name' => $value, 'path' => $path.'/'.$value, 'fullPath' => $rootPath.'/'.$value) );
//            }
//        }
//        return $result;
//    }

    /**
     * @param $path
     * @return bool
     */
    function fileExists($path) {
        $filePath = $this->realPath($path);
        if (is_file($filePath)) {
            return true;
        }
        return false;
    }

    /**
     * @param $path
     * @return bool
     */
    function isDir($path) {
        $filePath = $this->realPath($path);
        if (is_dir($filePath)) {
            return true;
        }
        return false;
    }

    /**
     * @param $path
     * @return bool
     */
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
//        $oldmask = umask(0);
        mkdir($realPath, 0777);
//        umask($oldmask);
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

    /**
     * @param $path
     * @param $targetPath path of the target folder NOT the full path of the target item
     * @return MediaItem|MediaException|null
     */
    function moveItem($path, $targetPath) {
        $item = $this->getItem($path);
        if ($item instanceof MediaException) {
            return $item;
        }

        $targetParentItem = $this->getItem($targetPath);
        if ($targetParentItem instanceof MediaException) {
            return new MediaException(ERROR_TYPES::TARGET_FOLDER_NOT_FOUND, $targetPath);
        }

        if ($item->type == 'folder') {
            if (!$this->checkFolderPermissions($item->path, false, true)) {
                return new MediaException(ERROR_TYPES::NO_WRITE_PERMISSION_IN_CHILDS, $path);
            }
            $fullTargetPath = $this->cleanPath($targetParentItem->fullPath . '/' . $item->name);
        } else {
            if ($item->writable == 0) {
                return new MediaException(ERROR_TYPES::NO_WRITE_PERMISSION, $path);
            }
            $fullTargetPath = $this->cleanPath($targetParentItem->fullPath . '/' . $item->name . '.' . $item->type);
        }

        if ($item->type == 'folder') {
            if(is_dir($fullTargetPath) ) {
                return new MediaException(ERROR_TYPES::TARGET_ALREADY_EXISTS, $targetPath.'/'.$item->name);
            }
        } else {
            if(file_exists($fullTargetPath) ) {
                return new MediaException(ERROR_TYPES::TARGET_ALREADY_EXISTS, $targetPath.'/'.$item->name.'.'.$item->type);
            }
        }

        if(!rename($item->fullPath, $fullTargetPath)) {
            if (is_dir($item->fullPath)) {
                return new MediaException(ERROR_TYPES::ERROR_MOVING_FOLDER, $path);
            } else {
                return new MediaException(ERROR_TYPES::ERROR_MOVING_FILE, $path);
            }
        }
        //@todo move item in item cache, when cache is implemented :)
        return $this->getItem($fullTargetPath);
    }
    function copyItem($path, $targetParentPath) {
        $item = $this->getItem($path);
        if ($item instanceof MediaException)
            return $item;

        if (!$this->checkFolderPermissions($path, true)) {
            return new MediaException(ERROR_TYPES::NO_READ_PERMISSION_IN_CHILDS, $path);
        }
        $targetParentItem = $this->loadItem($targetParentPath);
        if ($targetParentItem instanceof MediaException)
            return $item;

        if ($item->readable == 0) {
            return new MediaException(ERROR_TYPES::NO_READ_PERMISSION, $path);
        }
        if ($targetParentItem->writable == 0) {
            return new MediaException(ERROR_TYPES::TARGET_NO_WRITE_PERMISSION, $targetParentPath.'/'.$item->name);
        }

        $targetPath = null;
        if (dirname($item->fullPath) == $targetParentItem->fullPath ) {
            //if source and target in same folder. postfix copy with "(n)"
            $i = 1;
            if ($item->type == 'folder') {
                do {
                    $targetPath = $targetParentItem->fullPath . '/' . $item->name . ' ('.$i.')';
                    $i++;
                } while(is_dir($targetPath));
            }else {
                do {
                    $targetPath = $targetParentItem->fullPath . '/' . $item->name . '.' . $item->type . ' ('.$i.')';
                    $i++;
                } while(is_file($targetPath));
            }
        } else {
            if ($item->type == 'folder') {
                $targetPath = $targetParentItem->fullPath . '/' . $item->name;
            } else {
                $targetPath = $targetParentItem->fullPath . '/' . $item->name . '.' . $item->type;
            }
        }

        // move file or folder
        if(!$this->copyRecursive($item->fullPath, $targetPath)) {
            if(is_dir($item->fullPath)) {
                return new MediaException(ERROR_TYPES::ERROR_COPYING_FOLDER, $path);
            } else {
                return new MediaException(ERROR_TYPES::ERROR_COPYING_FILE, $path);
            }
        }
        return $this->getItem($targetPath);
    }

    function deleteItem($path) {
        $item = $this->getItem($path);
        if ($item instanceof MediaException)
            return $item;

        if ($item->type == 'folder') {
            if (!$this->checkFolderPermissions($path, false, true)) {
                return new MediaException(ERROR_TYPES::NO_WRITE_PERMISSION_IN_CHILDS, $path);
            }
        } else {
            if ($item->writable == 0) {
                return new MediaException(ERROR_TYPES::NO_WRITE_PERMISSION, $path);
            }
        }
        $trashPath = $this->realPath(TRASH_BIN_PATH);

        //if item is not in trash bin -> move it there
        if (strncmp($item->fullPath, $trashPath, strlen($trashPath)) !== 0) {
            if (!is_dir($this->realPath($trashPath))) {
                mkdir($this->realPath($trashPath));
            }
            $trashPathWithName = '';
            if ($item->type == 'folder') {
                $trashPathWithName = $trashPath.'/'.$item->name;
                $i = 1;
                //if name already taken, add a number
                while(is_dir($trashPathWithName)) {
                    $trashPathWithName = $trashPath . '/' . $item->name . ' ('.$i.')';
                    $i++;
                }
            } else {
                $trashPathWithName = $trashPath . '/' . $item->name . '.' . $item->type;
                $i = 1;
                //if name already taken, add a number
                while(file_exists($trashPathWithName)) {
                    $trashPathWithName = $trashPath . '/' . $item->name . ' ('.$i.').' . $item->type;
                    $i++;
                }
            }

            if(!rename($item->fullPath, $trashPathWithName)) {
                if (is_dir($item->fullPath)) {
                    return new MediaException(ERROR_TYPES::ERROR_DELETE_FOLDER, $path);
                } else {
                    return new MediaException(ERROR_TYPES::ERROR_DELETE_FILE, $path);
                }
            }
        } else {
            $this->deleteRecursive($item->fullPath);
//            if(!unlink($item->fullPath)) {
//                if (is_dir($item->fullPath)) {
//                    return new MediaException(ERROR_TYPES::ERROR_DELETE_FOLDER, $path);
//                } else {
//                    return new MediaException(ERROR_TYPES::ERROR_DELETE_FILE, $path);
//                }
//            }
        }
        return $item;
    }

    function getFileContent($path) {
        $item = $this->loadItem($path);
        if ($item instanceof MediaException) {
            return $item;
        }
        if ($item->type == 'folder') {
            return new MediaException(ERROR_TYPES::CAN_NOT_READ_FOLDER, $path);
        }
        if ($item->readable == 0) {
            return new MediaException(ERROR_TYPES::NO_READ_PERMISSION, $path);
        }
        try {
            return file_get_contents($item->fullPath);
        } catch (\Exception $e) {
            return new MediaException(ERROR_TYPES::ERROR_READING_FILE, $path);
        }
    }

    function setFileContent($path, $content) {
        $item = $this->loadItem($path);
        if ($item instanceof MediaException) {
            return $item;
        }
        if ($item->type == 'folder') {
            return new MediaException(ERROR_TYPES::CAN_NOT_WRITE_FOLDER, $path);
        }
        if ($item->writable == 0) {
            return new MediaException(ERROR_TYPES::NO_WRITE_PERMISSION, $path);
        }
        try {
            return file_put_contents($item->fullPath, $content, LOCK_EX);
        } catch (\Exception $e) {
            return new MediaException(ERROR_TYPES::ERROR_WRITING_FILE, $path);
        }
    }
    function getFolderMeta($path) {
        return $this->parseIniFile($this->realPath($path));
    }

    /**
     * @param $path string
     * @return MediaItem[]
     */
    public function getItems($path) {
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
        $fullPath = $this->realPath($path);
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
            ->addHeaderLine('Expires', date('D, d M Y H:i:s \G\M\T', time() + (60 * 60 * 24 * 31) ))
            ->addHeaderLine('Content-Type', FmHelper::mime_type_by_extension($path))
            ->addHeaderLine('Content-Length', strlen($fileContent));

        return $response;
    }

    /**
     * @param $path
     * @return array with 2 items readable and writable values are 0 and 1
     */
    public function getPermission($path) {
        $fullPath = $this->realPath($path);
        if (!$fullPath) return ['readable' => 0, 'writable' => 0];
//@todo deprecated systemPermission
//        $sysPerms = $this->getSystemPermission($fullPath);
        $isDir = is_dir($fullPath);
        $file = basename($fullPath);
        $role = $this->accessService->getRole();
        $readable = 0;
        $writable = 0;

        if ($isDir) {
            $meta = $this->getFolderMeta($path);
            if ($meta instanceof MediaException) {
                return ['readable' => 0, 'writable' => 0];
            }
            if ($meta && isset($meta['Permissions']) ) {
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
            }
        } else {
            $meta = $this->getFolderMeta($path);
            if ($meta instanceof MediaException) {
                return ['readable' => 0, 'writable' => 0];
            }
            if ($meta && isset($meta['Permissions']) ) {
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
            }
        }
        return [
//@todo deprecated systemPermission
//            'readable' => ($sysPerms['r'] == 0)? 0: $readable,
//            'writable' => ($sysPerms['w'] == 0)? 0: $writable
            'readable' => 1,//$readable,
            'writable' => 1//$writable
        ];
    }

    /**
     * Creates a zip file from source
     * @param $path
     * @return MediaException|MediaItem
     * @internal param string $source Source path for zip
     * @link    http://stackoverflow.com/questions/17584869/zip-main-folder-with-sub-folder-inside
     */

    public function zipFile($path)
    {
        $includeFolder = true;
        $item = $this->getItem($path);
        $source = $item->fullPath;

        if ($item->type == 'folder') {
            if (!$this->checkFolderPermissions($path, true)) {
                return new MediaException(ERROR_TYPES::NO_READ_PERMISSION_IN_CHILDS);
            }
        } else {
            if ($item->readable == 0) {
                return new MediaException(ERROR_TYPES::NO_READ_PERMISSION);
            }
        }

        $destination = sys_get_temp_dir().'/SRzA_'.uniqid().'.zip';

        if (!extension_loaded('zip') || !file_exists($source)) {
            return new MediaException(ERROR_TYPES::NO_ZIP_EXTENSION, $path);
        }

        $zip = new ZipArchive();
        if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
            return new MediaException(ERROR_TYPES::ERROR_IN_ZIP, $path);
        }

        $source = str_replace('\\', '/', realpath($source));
        $folder = $includeFolder ? basename($source) . '/' : '';

        if (is_dir($source) === true) {
            // add file to prevent empty archive error on download
            $zip->addFromString('SRzA.txt', "This archive has been generated by SRzA");

            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($files as $file) {
                $file = str_replace('\\', '/', realpath($file));

                if (is_dir($file) === true) {
                    $path = str_replace($source . '/', '', $file . '/');
                    $zip->addEmptyDir($folder . $path);
                } else if (is_file($file) === true) {
                    $path = str_replace($source . '/', '', $file);
                    $zip->addFile($file, $folder . $path);
                }
            }
        } else if (is_file($source) === true) {
            $zip->addFile($source, $folder . basename($source));
        }
        $zip->close();
        return $this->loadItem($destination);
    }
    

    /**
     * Check recursive folder permissions
     * @param $path
     * @param bool $read
     * @param bool $write
     * @return bool
     */
    private function checkFolderPermissions($path, $read = true, $write = true) {
        $realPath = $this->realPath($path);
        if (!$realPath) {
            //@todo return MediaException
            return false;
        }
        return $this->recursiveCheckFolderPermissions($realPath, $read, $write);
    }

    /**
     * Check recursive folder permissions
     * @param $path
     * @param bool $read
     * @param bool $write
     * @return bool
     */
    private function recursiveCheckFolderPermissions($path, $read, $write) {
        if(!is_dir($path) ) {
            return false;
        }

        $dir = scandir($path);
        $role = $this->accessService->getRole();
        $meta = $this->getFolderMeta($path);
        if ($meta instanceof MediaException) {
            return false;
        }
        if ($meta && isset($meta['Permissions']) ) {
            if (isset($meta['Permissions']['folderRead'])) {
                if ($read && !in_array($role, $meta['Permissions']['folderRead']) ) {
                    return false;
                }
            }
            if (isset($meta['Permissions']['folderWrite'])) {
                if ($write && !in_array($role, $meta['Permissions']['folderWrite']) ) {
                    return false;
                }
            }
        }

        foreach ($dir as $key => $value) {
            if ($value == '.' || $value == '..') continue;
            $valuePath = $path.'/'.$value;
            if( is_dir ($valuePath) ) {
                if (!$this->recursiveCheckFolderPermissions($valuePath, $read, $write)) {
                    return false;
                }
            }
        }
        return true;
    }
    private function realPath($path) {
        $realPath = realpath($this->dataPath.'/'.$path);
        if (!$realPath)
            $realPath = realpath($path);
        if ($realPath)
            $realPath = $this->cleanPath($realPath);
        return $realPath;
    }

    /**
     * @param $path
     * @return bool
     */
    private function isInDataFolder($path) {
        //wenn der pfad nicht im dataPath ist dann false
        if (strpos($path, $this->dataPath) === false) {
            return false;
        }
        return true;
    }
    /**
     * @param $objectivePath muss ein absoluter pfad sein
     * @return array|MediaException
     */
    private function parseIniFile($objectivePath) {
        if (!is_dir($objectivePath)) {
            $objectivePath = dirname($objectivePath);
        }
        if (!$this->isInDataFolder($objectivePath)) {
            return new MediaException(ERROR_TYPES::ERROR_FOLDER_NOT_IN_DATA_PATH, $objectivePath);
        }
        $dir = $objectivePath;
        $objectivePath = $objectivePath.'/folder.conf';
//        var_dump($objectivePath);die;//ich muss weg bis in 20 min :)kk
        $process_sections = true;
        $scanner_mode = INI_SCANNER_TYPED;
        if (array_key_exists($objectivePath, $this->metaCache)) {
            return $this->metaCache[$objectivePath];
        }
        $ini = [];
        if (is_file($objectivePath)) {
            $ini = parse_ini_file($objectivePath, $process_sections, $scanner_mode);
            $this->metaCache[$objectivePath] = $ini;
        } else {
            $ini = $this->parseIniFile(dirname($dir));
            if ($ini instanceof MediaException) {
                return $ini;
            }
            unset($ini['FileRestrictions']);
            $this->metaCache[$objectivePath] = $ini;
        }
        return $ini;
    }

    /**
     * @param $path
     * @return MediaItem|MediaException|null
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

    /**
     * Copies a single file, symlink or a whole directory.
     * In case of directory it will be copied recursively.
     *
     * @param $source
     * @param $target
     * @return bool
     */
    private function copyRecursive($source, $target) {
        // handle symlinks
        if (is_link($source)) {
            return symlink(readlink($source), $target);
        }

        // copy a single file
        if (is_file($source)) {
            return copy($source, $target);
        }

        // make target directory
        if (!is_dir($target)) {
            mkdir($target, 0755);
        }

        $handle = opendir($source);
        // loop through the directory
        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $from = $source . DIRECTORY_SEPARATOR . $file;
            $to = $target . DIRECTORY_SEPARATOR . $file;

            if (is_file($from)) {
                copy($from, $to);
            } else {
                // recursive copy
                $this->copyRecursive($from, $to);
            }
        }
        closedir($handle);

        return true;
    }

    /**
     * deletes hard without permission check
     * @todo add error handling
     * @param $realPath
     */
    private function deleteRecursive($realPath) {
        if (is_dir($realPath)){
            $files = glob($realPath.'/*', GLOB_MARK); //GLOB_MARK adds a slash to directories returned
            foreach ($files as $file)
            {
                $this->deleteRecursive( $file );
            }

            rmdir($realPath);
        } elseif (is_file($realPath)) {
            unlink($realPath);
        }
    }

    /**
     * Set service manager
     *
     * @param ServiceManager $serviceManager
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        // TODO: Implement setServiceManager() method.
    }
}