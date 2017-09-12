<?php
namespace Media\Service;
use Auth\Service\AccessService;
use Exception;
use Media\Utility\FmHelper;
use Media\Utility\Pathfinder;
use Media\Utility\UploadHandler;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Zend\Http\Response;
use Zend\ServiceManager\ServiceManager;
use ZipArchive;
use Media\Model\MediaItem;



const DATA_PATH = '/Data';
const LIVE_PATH = '/media/file/';
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
    "Can't rename folder",//i = 10
    "Can't rename file",
    "The use of '/' is forbidden in the directory or file name",
    "Destination folder not found",
    'No write permission in destination folder',
    'Destination already exists',
    "Can't move folder",
    "Can't move file",
    "Can't delete folder",
    "Can't delete file",
    "Can't copying folder",//i = 20
    "Can't copying file",
    "Can't reading file",
    "Can't writing file",
    "Can't read folders",
    "Can't write to folder",
    "No ZIP extension",
    "Error while zipping",
    'No read permission in sub folders',
    'No write permission in sub folders',
    'Folder is not in data path',
    'UPLOAD_ERROR',//i = 30
    'UPLOAD_FILE_NOT_FOUND',
    'FILE_UPLOAD_ERROR',

];
class MediaService {
	/** @var ImageProcessor  */
	public $imageProcessor;

    protected $dataPath;
    /** @var AccessService  */
    protected $accessService;

    private $metaCache;
    private $itemCache = array();
    private $config;

    function __construct($config, AccessService $accessService, ImageProcessor $imageProcessor) {
        try {
            $this->accessService = $accessService;
            $this->imageProcessor = $imageProcessor;
            $this->config = $config;
            $rootPath = getcwd();
            $this->dataPath = $this->cleanPath($rootPath.DATA_PATH);
            $this->metaCache = [];
        } catch (Exception $e) {
            bdump($e);
        }
    }

    /**
     * @param string $path
     * @return bool
     */
    public function fileExists($path) {
        $filePath = $this->realPath($path);
        if (is_file($filePath)) {
            return true;
        }
        return false;
    }

    /**
     * @param string $path
     * @return bool
     */
    public function isDir($path) {
        $filePath = $this->realPath($path);
        if (is_dir($filePath)) {
            return true;
        }
        return false;
    }

    /**
     * @param string $path
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

    public function createFolder($path) {
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

    public function renameItem($path, $newName) {
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
    public function moveItem($path, $targetPath) {
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
    public function copyItem($path, $targetParentPath) {
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

    public function deleteItem($path) {
        $item = $this->getItem($path);

        // remove from cache
        $this->clearCache($item);

        // if item is MediaException
        if ($item instanceof MediaException)
            return $item;

        // change on disc
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

	public function deleteAllItemsByPath($path)
	{
		$item = $this->getItem($path);
		if($item instanceof MediaException) return;
		if($item->type == 'folder')
			$this->deleteRecursive($item->fullPath);
		elseif ($item->type == 'image')
			$this->deleteRecursive(str_replace($item->name . '/' . $item->extension, '', $item->fullPath));
		$this->clearCache();
    }

    public function getFileContent($path) {
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

    public function setFileContent($path, $content) {
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

    public function getFolderMeta($path) {
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
        $role = $this->accessService->getRole();
        if ($role === "Administrator") return ['readable' => 1, 'writable' => 1];

        $file = basename($fullPath);
        $readable = 0;
        $writable = 0;

        $meta = $this->getFolderMeta($path);
        if ($meta instanceof MediaException) {
            return ['readable' => 0, 'writable' => 0];
        }
        if ($meta && isset($meta['Permissions']) ) {
            if (isset($meta['Permissions']['read'])) {
                if (in_array($role, $meta['Permissions']['read']) ) {
                    $readable = 1;
                }
            }
            if (isset($meta['Permissions']['write'])) {
                if (in_array($role, $meta['Permissions']['write']) ) {
                    $writable = 1;
                }
            }
            // permission restrictions
            if ($meta && isset($meta['Restrictions']) ) {
                if (isset($meta['Restrictions'][$role.'Read'])) {
                    if (in_array($file, $meta['Restrictions'][$role.'Read']) ) {
                        $readable = 0;
                    }
                }
                if (isset($meta['Restrictions'][$role.'Write'])) {
                    if (in_array($file, $meta['Restrictions'][$role.'Write']) ) {
                        $writable = 0;
                    }
                }
            }
        }
        return [
            'readable' => $readable,
            'writable' => $writable
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
	 * @param array  $filePostArray
	 * @param string $targetFolder
	 * @param string $fileName
	 * @param bool   $force
	 *
	 * @return MediaException|MediaItem
	 */
    public function upload($filePostArray, $targetFolder, $fileName, $force = false) {
		$target = $this->realPath($targetFolder);

    	if (!$target && $force) {
    		$path = str_replace(getcwd(), '', $targetFolder);
    		$path = str_replace(DATA_PATH, '', $path);
    		$parts = substr($path, 1);
    		$parts = explode('/', $parts);
    		$rootPath = $this->cleanPath( getcwd() . DATA_PATH );
    		$c = count($parts);
    		$i = 0;
    		while ($i < $c-1){
    			$rootPath .= '/' . $parts[$i];
    			@mkdir( $rootPath, 0755);
    			$i++;
			}
			$target = $rootPath . '/' .$parts[$c-1];
		}

        $perm = $this->getPermission($target);
        if (!$force && !$perm['writable']) {
            return new MediaException(ERROR_TYPES::NO_WRITE_PERMISSION, $targetFolder);
        }

        $path = '';

        try {
            $uploadHandler = new UploadHandler($filePostArray, $target);
//            $uploadHandler->autoOverwrite = true;
            $uploadHandler->autoRename = true;
            $newFilePath = $uploadHandler->upload();
            $item = $this->getItem(Pathfinder::getRelativePath($newFilePath));
            if ($this->isImage($item->fullPath)) {
                $this->imageProcessor->load($item);
                $this->createDefaultThumbs($item);
            }
            return $item;
        } catch (Exception $e) {
            return new MediaException(ERROR_TYPES::UPLOAD_ERROR, $e->getMessage());
        }
    }

    /**
     * @param array $uploadFileDefsArray the php FILES array
     * @param string|array $targetFolder string or array of target folders. (length needs to be the same as fileDefsArray)
     * @param string|array $targetName string(without extension) or array of file names. (length needs to be the same as fileDefsArray)
     * @throws Exception
     */
    public function multiUpload($uploadFileDefsArray, $targetFolder, $targetName = null) {
        $fileCount = count($uploadFileDefsArray);
        if (is_array($targetFolder) && count($targetFolder) != $fileCount) {
            throw new Exception("if targetFolder is an array, it must have the same length");
        }
        if (is_array($targetName) && count($targetName) != $fileCount) {
            throw new Exception("if targetName is an array, it must have the same length");
        }
        $index = 0;
        foreach ($uploadFileDefsArray as $key => $value) {
			$name = null;
            $target = (is_string($targetFolder))? $targetFolder: $targetFolder[$index];
            $handler = $this->uploadHandlerFactory($value, $target);
            $handler->autoOverwrite = false;
			if ($targetName !== null){
				$name = (is_string($targetName)) ? $targetName : $targetName[ $index ];
			}

			$handler->setName($name);
            $handler->upload();
            $index++;
        }
    }

    /**
     * @param array $uploadFileDef    one element of the php FILES array
     * @param string $targetFolder
     * @param bool $force ignore write permission
     * @return UploadHandler
     * @throws MediaException
     */
	public function uploadHandlerFactory($uploadFileDef, $targetFolder, $force = false) {
		$target = $permTest = $this->realPath($targetFolder);

		if (!$target) {
            $dirs = explode('/', $targetFolder);
            $lastDir = '/';
            foreach ($dirs as $key => $value) {
                if ($value == '') continue;
                $nextDir = $lastDir . $value.'/';
                if (file_exists($this->dataPath.$nextDir)) {
                    $lastDir = $nextDir;
                } else {
					$permTest = $lastDir;
                    break;
                }
            }
        }

		$perm = $this->getPermission($permTest);
		if (!$force && $perm['writable'] != 1) {
			throw new MediaException(ERROR_TYPES::NO_WRITE_PERMISSION, $targetFolder);
		}
        $uploadHandler = new UploadHandler($uploadFileDef, $this->dataPath.$targetFolder);
        $self = $this;
        $uploadHandler->registerOnFinishHandler(function($targetPath) use ($self) {
            $item = $self->getItem(Pathfinder::getRelativePath($targetPath));
            if ($self->isImage($item->fullPath)) {
                $self->imageProcessor->load($item);
                $self->createDefaultThumbs($item);
            }
        });
        $uploadHandler->autoOverwrite = false;
        $uploadHandler->autoRename = true;
        return $uploadHandler;
	}
    private function addItem2cache(MediaItem $item) {
        $path = rtrim($item->path, "\x5C\x2F");
        $this->itemCache[$path] = $item;
    }
    private function getCachedItem($path) {
        //cut off last /
        $path = rtrim($path, "\x5C\x2F");
        if (array_key_exists($path, $this->itemCache) ){
            return $this->itemCache[$path];
        }
        return false;
    }
    private function clearCache(MediaItem $item = null){
    	if ($item == null) $this->itemCache = array ();
    	else {
			$path = rtrim($item->path, "\x5C\x2F");
    		unset ($this->itemCache[$path]);
		}
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

        $role = $this->accessService->getRole();
        if ($role === "Administrator") {
            return true;
        }
        $dir = scandir($path);
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

	/**
	 * @param $path
	 *
	 * @return bool|string
	 */
    private function realPath($path) {
		if (Pathfinder::isAbsolute($path)) {
			$dataRoot = $this->cleanPath(getcwd() . '/Data');
			$test = substr($path, 0 , strlen($dataRoot));
			if ($test !== $dataRoot) return false;
		}
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
//            unset($ini['Restrictions']);
            $this->metaCache[$objectivePath] = $ini;
        }
        return $ini;
    }

    /**
     * @param $path
     * @return MediaItem|MediaException|null
     */
    private function loadItem($path) {
    	$cachePath = $path;
		$return = $this->getCachedItem($cachePath);
		if ($return) return $return;
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
            $item->extension = $pathInfo['extension'];
            $livePath = LIVE_PATH . $path;
            $item->livePath = $this->cleanPath($livePath);
            $item->modified = filectime($fullPath);
            $item->size = filesize($fullPath);
            if ($this->isImage($fullPath)) {
                $item->type = "image";
            } else {
                $item->type = $pathInfo['extension'];
            }
        } else {
            return new MediaException(ERROR_TYPES::MEDIA_ITEM_NOT_FOUND, $path);
        }
        // security
		if ($item->path[0]== '/') $item->path = substr($item->path, 1, strlen($item->path));

		$this->addItem2cache($item);
        return $item;
    }


    /**
     * Clean path string to remove multiple slashes, etc.
     * @param string $string
     * @return string $string
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

	public function createDefaultThumbs(MediaItem $item)
	{
		$thumbFolder    = $this->config['MediaService']['thumbs']['relPath'];
		$thumbPathBig   = $thumbFolder . str_replace($item->name, $item->name . '_thumb_big', $item->path);
		$thumbPathSmall = $thumbFolder . str_replace($item->name, $item->name . '_thumb_small', $item->path);

		$profileImageThumbBigSizeX = $this->config['MediaService']['thumbs']['bX'];
		$profileImageThumbBigSizeY = $this->config['MediaService']['thumbs']['bY'];

		$profileImageThumbSmallSizeX = $this->config['MediaService']['thumbs']['sX'];
		$profileImageThumbSmallSizeY = $this->config['MediaService']['thumbs']['sY'];

		$this->imageProcessor->load($item);
		$i = 0;
		while ($i < 2)
		{
			switch ($i){
				case 0:
					// process
					$this->imageProcessor->resize_crop($profileImageThumbBigSizeX, $profileImageThumbBigSizeY);
					$this->imageProcessor->saveImage($thumbPathBig);
					$this->imageProcessor->load($item);
					break;
				case 1:
					// process
					$this->imageProcessor->resize_crop($profileImageThumbSmallSizeX, $profileImageThumbSmallSizeY);
					$this->imageProcessor->saveImage($thumbPathSmall);
					break;
			}
			$i++;
		}
	}

	public function cleanUpThumbs()
	{
		$dataPath = Pathfinder::getAbsolutePath(null);
		$thumbsPath = $dataPath .'/_thumbs';
		$imagesInDirs = $this->readDirRecursive($thumbsPath);
		$imagePaths = $this->prepareFileArray($imagesInDirs);

		// deletes all Thumbs that have no image
		foreach ($imagePaths as $imagePath) {
			// @todo permission for folder -> can't delete folder in the moment
			if (!file_exists($dataPath . $imagePath) && !is_dir($dataPath. '/_thumbs' . $imagePath)){
				unlink($dataPath. '/_thumbs' . $imagePath);
			}
		}
	}

	public function createThumbsForAll()
	{
		$thumbsInDir = array();
		$newImages = array();
		$imagesInDir = $this->readDirRecursive(Pathfinder::getAbsolutePath(null));
		if (isset($imagesInDir['_thumbs'])) {
			$thumbsInDir = $imagesInDir['_thumbs'];
			unset($imagesInDir['_thumbs']);
		}

		if (!empty($thumbsInDir))
			$thumbsInDir = $this->prepareFileArray($thumbsInDir);

		$imagesInDir = $this->prepareFileArray($imagesInDir);
		if ($key = array_search('/', $imagesInDir)) unset($imagesInDir[$key]);

		foreach ($thumbsInDir as $thumbPath) {
			if($key = array_search($thumbPath, $imagesInDir))
				unset($imagesInDir[$key]);
		}

		// create thumbs
		foreach ($imagesInDir as $imagePath) {
			$item = $this->getItem(substr($imagePath, 1, strlen($imagePath)));
			if ($item->type !== 'image') continue;
			$this->createDefaultThumbs($item);
		}
			bdump($imagesInDir);

	}
	/**
	 * Reads Dir Recursive
	 *
	 * @param string $path absolute path to folder
	 *
	 * @return null | array array of files that preserves folder tree structure
	 */
	private function readDirRecursive($path)
	{
		$result = null;
		if ( is_dir ( $path))
		{
			if ( $handle = opendir($path) )
			{
				while (($file = readdir($handle)) !== false)
				{
					if ($file == '..' || $file == '.' || $file == 'folder.conf') continue;
					if ($path[strlen($path)-1] !== '/') $path .= '/';
//
					if (is_dir($path.$file)) $result[$file] = $this->readDirRecursive($path.$file);
					else $result[$file] = $file;
				}
				closedir($handle);
			}
			return $result;
		}
	}

	/**
	 * @param array  $readDirArray
	 * @param string $parentKey
	 *
	 * @return array numeric array of absolute paths
	 */
	private function prepareFileArray($readDirArray, $parentKey = '')
	{
		$result = array();
		foreach ($readDirArray as $key => $value) {
			if (!is_array($value)) array_push($result, $parentKey . '/' . $value);
			else {
				$subResult = array();
				array_push($subResult, $this->prepareFileArray($value, $parentKey . '/' .$key));
				foreach ($subResult[0] as $sub){
					array_push($result, $sub);
				}
			}
		}
		return $result;
	}
}