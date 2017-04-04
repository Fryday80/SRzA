<?php
namespace Media\Controller;

use Media\Service\ERROR_TYPES;
use Media\Service\MediaException;
use Media\Service\MediaItem;
use Media\Service\MediaService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Media\Utility\FmHelper;
use Media\Utility\LocalUploadHandler;

class FileBrowserController extends AbstractActionController  {

    /**
     * @var $mediaService MediaService
     */
    private $mediaService;

    function __construct()
    {
    }


    public function indexAction() {
        $viewModel = new ViewModel();
        $viewModel->setVariables(array('key' => 'value'))
            ->setTerminal(true);
        return $viewModel;
    }
    public function embeddedAction() {
        return array();
    }
    public function actionAction() {
        $this->mediaService = $this->getServiceLocator()->get('MediaService');
        $this->initFileBrowser();
//        $fm = getFileBrowserFor($dataDir);
        $this->handleRequest();

        $viewModel = new ViewModel();
        $viewModel->setVariables(array('key' => 'value'))
            ->setTerminal(true);
        return $viewModel;
    }






    //base file manager
    const TYPE_FILE = 'file';
    const TYPE_FOLDER = 'folder';

    public $config = [];
    protected $refParams = [];
    protected $language = [];
    protected $get = [];
    protected $post = [];
    protected $fm_path = '';
    protected $allowed_actions = [];
    /**
     * File item model template
     * @var array
     */
    protected $file_model = [
        "id"    => '',
        "type"  => self::TYPE_FILE,
        "attributes" => [
            'name'      => '',
            'extension' => '',
            'path'      => '',
            'readable'  => 1,
            'writable'  => 1,
            'created'   => '',
            'modified'  => '',
            'timestamp' => '',
            'height'    => 0,
            'width'     => 0,
            'size'      => 0,
        ]
    ];
    /**
     * Folder item model template
     * @var array
     */
    protected $folder_model = [
        "id"    => '',
        "type"  => self::TYPE_FOLDER,
        "attributes" => [
            'name'      => '',
            'path'      => '',
            'readable'  => 1,
            'writable'  => 1,
            'created'   => '',
            'modified'  => '',
            'timestamp' => '',
        ]
    ];
    /**
     * List of all possible actions
     * @var array
     */
    protected $actions_list = ["select", "upload", "download", "rename", "copy", "move", "replace", "delete", "edit"];
    //local file manager
    protected $doc_root;
    protected $path_to_files;
    protected $dynamic_fileroot;

    private function initFileBrowser() {
        $dataPath = getcwd() . '/data';
        $this->config = require_once(getcwd().'\module\Media\config\fileBrowserConfig.php');
        $this->config['options']['fileRoot'] = $dataPath;

        // fix display non-latin chars correctly
        // https://github.com/servocoder/RichFilemanager/issues/7
        setlocale(LC_CTYPE, 'en_US.UTF-8');

        // fix for undefined timezone in php.ini
        // https://github.com/servocoder/RichFilemanager/issues/43
        if(!ini_get('date.timezone')) {
            date_default_timezone_set('GMT');
        }

        $this->fm_path = $this->config['fmPath'] ? $this->config['fmPath'] : dirname(dirname(dirname($_SERVER['SCRIPT_FILENAME'])));

        $this->allowed_actions = $this->actions_list;
        if($this->config['options']['capabilities']) {
            $this->setAllowedActions($this->config['options']['capabilities']);
        }

        $this->setParams();
        $this->loadLanguageFile();
        $this->setFileRoot($dataPath, true, false);
    }


    /**
     * Invokes filemanager action based on request params and returns response
     */
    public function handleRequest()
    {
        $response = '';

        if(!isset($_GET)) {
            $this->error($this->lang('INVALID_ACTION'));
        } else {

            if(isset($_GET['mode']) && $_GET['mode']!='') {

                switch($_GET['mode']) {

                    default:
                        $this->error($this->lang('MODE_ERROR'));
                        break;

                    case 'initiate':
                        $response = $this->actionInitiate();
                        break;

                    case 'getfile':
                        if($this->getvar('path')) {
                            $response = $this->actionGetFile();
                        }
                        break;

                    case 'getfolder':
                        if($this->getvar('path')) {
                            $response = $this->actionGetFolder();
                        }
                        break;

                    case 'rename':
                        if($this->getvar('old') && $this->getvar('new')) {
                            $response = $this->actionRename();
                        }
                        break;

                    case 'copy':
                        if($this->getvar('source') && $this->getvar('target')) {
                            $response = $this->actionCopy();
                        }
                        break;

                    case 'move':
                        if($this->getvar('old') && $this->getvar('new')) {
                            $response = $this->actionMove();
                        }
                        break;

                    case 'editfile':
                        if($this->getvar('path')) {
                            $response = $this->actionEditFile();
                        }
                        break;

                    case 'delete':
                        if($this->getvar('path')) {
                            $response = $this->actionDelete();
                        }
                        break;

                    case 'addfolder':
                        if($this->getvar('path') && $this->getvar('name')) {
                            $response = $this->actionAddFolder();
                        }
                        break;

                    case 'download':
                        if($this->getvar('path')) {
                            $response = $this->actionDownload();
                        }
                        break;

                    case 'getimage':
                        if($this->getvar('path')) {
                            $thumbnail = isset($_GET['thumbnail']);
                            $this->actionGetImage($thumbnail);
                        }
                        break;

                    case 'readfile':
                        if($this->getvar('path')) {
                            $this->actionReadFile();
                        }
                        break;

                    case 'summarize':
                        $response = $this->actionSummarize();
                        break;
                }

            } else if(isset($_POST['mode']) && $_POST['mode']!='') {

                switch($_POST['mode']) {

                    default:
                        $this->error($this->lang('MODE_ERROR'));
                        break;

                    case 'upload':
                        if($this->postvar('path')) {
                            $response = $this->actionUpload();
                        }
                        break;

                    case 'replace':
                        if($this->postvar('path')) {
                            $response = $this->actionReplace();
                        }
                        break;

                    case 'savefile':
                        if($this->postvar('path') && $this->postvar('content', false)) {
                            $response = $this->actionSaveFile();
                        }
                        break;
                }
            }
        }

        echo json_encode([
            'data' => $response,
        ]);
        //exit;
    }

    public function actionInitiate()
    {
        $shared_config = [];
//        if($this->config['overrideClientConfig']) {
//            // config options to override at the client-side
//            $shared_config = [
//                'options' => [
//                    'culture' => $this->config['options']['culture'],
//                    'charsLatinOnly' => $this->config['options']['charsLatinOnly'],
//                    'capabilities' => $this->config['options']['capabilities'],
//                ],
//                'security' => [
//                    'allowFolderDownload' => $this->config['security']['allowFolderDownload'],
//                    'allowChangeExtensions' => $this->config['security']['allowChangeExtensions'],
//                    'allowNoExtension' => $this->config['security']['allowNoExtension'],
//                    'normalizeFilename' => $this->config['security']['normalizeFilename'],
//                    'editRestrictions' => $this->config['security']['editRestrictions'],
//                ],
//                'upload' => [
//                    'paramName' => $this->config['upload']['paramName'],
//                    'chunkSize' => $this->config['upload']['chunkSize'],
//                    'fileSizeLimit' => $this->config['upload']['fileSizeLimit'],
//                    'policy' => $this->config['upload']['policy'],
//                    'restrictions' => $this->config['upload']['restrictions'],
//                ],
//            ];
//        }

        return [
            'id' => '/',
            'type' => 'initiate',
            'attributes' => [
                'config' => $shared_config,
            ],
        ];
    }

    public function convertMediaItems($mediaItems) {
        if (!is_array($mediaItems)) {
            $mediaItems = [$mediaItems];
        }
        /** @var $item MediaItem */
        $files = [];
        foreach ($mediaItems as $item) {
            if (!$item) continue;
            if ($item->type == 'folder') {
                $model = $this->folder_model;
                $model['id'] = $this->cleanPath($item->path.'/');
                $model['attributes']['name'] = $item->name;
                $model['attributes']['path'] = $item->path;
                $model['attributes']['readable'] = $item->readable;
                $model['attributes']['writable'] = $item->writable;
                $model['attributes']['created'] = $item->created;
                $model['attributes']['modified'] = $item->modified;
                $model['attributes']['timestamp'] = $item->timestamp;
                //$model['attributes']['capabilities'] = ['select'];
                array_push($files, $model);
            } else {
                $model = $this->file_model;
                $model['id'] = $item->path;
                $model['attributes']['name'] = $item->name.'.'.$item->type;
                $model['attributes']['extension'] = $item->type;
                $model['attributes']['path'] = $item->path;
                $model['attributes']['readable'] = $item->readable;
                $model['attributes']['writable'] = $item->writable;
                $model['attributes']['created'] = $item->created;
                $model['attributes']['modified'] = $item->modified;
                $model['attributes']['timestamp'] = $item->timestamp;
                $model['attributes']['height'] = 0;
                $model['attributes']['width'] = 0;
                $model['attributes']['size'] = $item->size;
                array_push($files, $model);
            }
        }
        return $files;
    }

    /**
     * @return array
     */
    public function actionGetFolder()
    {
        $target_path = $this->get['path'];
        //@todo implement error handling
        return $this->convertMediaItems($this->mediaService->getItems($target_path));








//        $files_list = [];
//        $response_data = [];
//        $target_path = $this->get['path'];
//        $target_fullpath = $this->getFullPath($target_path, true);
//        //Log::info('opening folder "' . $target_fullpath . '"');
//
//        if(!is_dir($target_fullpath)) {
//            $this->error(sprintf($this->lang('DIRECTORY_NOT_EXIST'), $target_path));
//        }
//
//        // check if folder is readable
//        if(!$this->has_system_permission($target_fullpath, ['r'])) {
//            $this->error(sprintf($this->lang('NOT_ALLOWED_SYSTEM')));
//        }
//
//        if(!$handle = @opendir($target_fullpath)) {
//            $this->error(sprintf($this->lang('UNABLE_TO_OPEN_DIRECTORY'), $target_path));
//        } else {
//            while (false !== ($file = readdir($handle))) {
//                if($file != "." && $file != "..") {
//                    array_push($files_list, $file);
//                }
//            }
//            closedir($handle);
//
//            foreach($files_list as $file) {
//                $file_path = $target_path . $file;
//                if(is_dir($target_fullpath . $file)) {
//                    $file_path .= '/';
//                }
//
//                $item = $this->get_file_info($file_path);
//                if($this->filter_output($item)) {
//                    $response_data[] = $item;
//                }
//            }
//        }
//
//        return $response_data;
    }

    /**
     * @return array
     */
    public function actionGetFile()
    {
        $target_path = $this->get['path'];
        $files = $this->convertMediaItems($this->mediaService->getItems($target_path));
        if (count($files) > 0) {
            return $files[0];
        }






//        $target_path = $this->get['path'];
//        $target_fullpath = $this->getFullPath($target_path, true);
//        //Log::info('opening file "' . $target_fullpath . '"');
//
//        if(is_dir($target_fullpath)) {
//            $this->error(sprintf($this->lang('FORBIDDEN_ACTION_DIR')));
//        }
//
//        // check if the name is not in "excluded" list
//        if(!$this->is_allowed_name($target_fullpath, false)) {
//            $this->error(sprintf($this->lang('INVALID_DIRECTORY_OR_FILE')));
//        }
//
//        // check if file is readable
//        if(!$this->has_system_permission($target_fullpath, ['r'])) {
//            $this->error(sprintf($this->lang('NOT_ALLOWED_SYSTEM')));
//        }
//
//        return $this->get_file_info($target_path);
    }

    /**
     * @return mixed
     */
    public function actionAddFolder()
    {
        $targetPath = $this->get['path'];
        $targetName = $this->get['name'];
        $fullPath = $targetPath.$targetName;

        if(is_dir($this->mediaService->isDir($fullPath))) {
            $this->error(sprintf($this->lang('DIRECTORY_ALREADY_EXISTS'), $targetName));
        }
        $item = $this->mediaService->createFolder($fullPath);
        if ($item instanceof MediaException) {
            switch($item->code) {
                case ERROR_TYPES::NO_WRITE_PERMISSION:
                    $this->error(sprintf($this->lang('NOT_ALLOWED'), $targetName));
                    break;
                case ERROR_TYPES::FOLDER_EXISTS_ALREADY:
                    $this->error(sprintf($this->lang('DIRECTORY_ALREADY_EXISTS'), $targetName));
                    break;
                case ERROR_TYPES::PARENT_NOT_EXISTS:
                    $this->error(sprintf($this->lang('UNABLE_TO_CREATE_DIRECTORY'), $targetName));
                    break;
                case ERROR_TYPES::FORBIDDEN_NAME:
                    $this->error(sprintf($this->lang('FORBIDDEN_NAME'), $targetName));
                    break;
//                case ERROR_TYPES::NO_SYSTEM_WRITE_PERMISSION:
//                    $this->error(sprintf($this->lang('NOT_ALLOWED_SYSTEM'), $targetName));
//                    break;
            }
        }
        return $this->convertMediaItems($item)[0];
    }

    /**
     * @return array
     */
    public function actionUpload()
    {
        $target_path = $this->post['path'];
        $target_fullpath = $this->getFullPath($target_path, true);
        //Log::info('uploading to "' . $target_fullpath . '"');

        // check if file is writable
        if(!$this->has_system_permission($target_fullpath, ['w'])) {
            $this->error(sprintf($this->lang('NOT_ALLOWED_SYSTEM')));
        }

        //check permission
        if ($this->mediaService->getPermission($target_path)['writable'] == 0) {
            $this->error(sprintf($this->lang('NOT_ALLOWED')));
        }
//        if(!$this->hasPermission('upload')) {
//            $this->error(sprintf($this->lang('NOT_ALLOWED')));
//        }

        $content = $this->initUploader([
            'upload_dir' => $target_fullpath,
        ])->post(false);

        $response_data = [];
        $files = isset($content[$this->config['upload']['paramName']]) ?
            $content[$this->config['upload']['paramName']] : null;
        // there is only one file in the array as long as "singleFileUploads" is set to "true"
        if ($files && is_array($files) && is_object($files[0])) {
            $file = $files[0];
            if(isset($file->error)) {
                $this->error($file->error);
            } else {
                $relative_path = $this->cleanPath('/' . $target_path . '/' . $file->name);
                $item = $this->get_file_info($relative_path);
                $response_data[] = $item;
            }
        } else {
            $this->error(sprintf($this->lang('ERROR_UPLOADING_FILE')));
        }

        return $response_data;
    }

    /**
     * @return mixed
     */
    public function actionRename()
    {
        $path = $this->get['old'];
        $newName = $this->get['new'];
        $item = $this->mediaService->renameItem($path, $newName);
        if ($item instanceof MediaException) {
            switch($item->code) {
                case ERROR_TYPES::FOLDER_ALREADY_EXISTS:
                    $this->error(sprintf($this->lang('DIRECTORY_ALREADY_EXISTS'), $newName));
                    break;
                case ERROR_TYPES::FILE_ALREADY_EXISTS:
                    $this->error(sprintf($this->lang('FILE_ALREADY_EXISTS'), $newName));
                    break;
                case ERROR_TYPES::ERROR_RENAMING_FOLDER:
                    $this->error(sprintf($this->lang('ERROR_RENAMING_DIRECTORY'), $newName));
                    break;
                case ERROR_TYPES::ERROR_RENAMING_FILE:
                    $this->error(sprintf($this->lang('ERROR_RENAMING_FILE'), $newName));
                    break;
                case ERROR_TYPES::NO_WRITE_PERMISSION:
                    $this->error(sprintf($this->lang('NOT_ALLOWED'), $newName));
                    break;
                case ERROR_TYPES::FORBIDDEN_CHAR_SLASH:
                    $this->error(sprintf($this->lang('FORBIDDEN_CHAR_SLASH'), $newName));
                    break;
                default:
                    $this->error(sprintf($this->lang('NOT_ALLOWED_SYSTEM'), $newName));
                    break;
            }
        }
        return $this->convertMediaItems($item)[0];
    }

    /**
     * @inheritdoc
     */
    public function actionDelete()
    {
        $targetPath = $this->get['path'];

        $item = $this->mediaService->deleteItem($targetPath);
        if ($item instanceof MediaException) {
            /** @var $item MediaException */
            $this->error($item->msg, $item->path);
        } else {
            return $this->convertMediaItems($item)[0];
        }

        die;
//        $thumbnail_path = $this->get_thumbnail_path($target_fullpath);
//
//        if(is_dir($target_fullpath)) {
//            $this->unlinkRecursive($target_fullpath);
//            //Log::info('deleted "' . $target_fullpath . '"');
//
//            // delete thumbnails if exists
//            if(file_exists($thumbnail_path)) {
//                $this->unlinkRecursive($thumbnail_path);
//            }
//        } else {
//            unlink($target_fullpath);
//            //Log::info('deleted "' . $target_fullpath . '"');
//
//            // delete thumbnails if exists
//            if(file_exists($thumbnail_path)) {
//                unlink($thumbnail_path);
//            }
//        }
    }


    /**
     * @return mixed
     */
    public function actionCopy()
    {
        $sourcePath = $this->get['source'];
        $targetPath = $this->get['target'];

        $item = $this->mediaService->copyItem($sourcePath, $targetPath);
        if ($item instanceof MediaException) {
            /** @var $item MediaException */
            $this->error($item->msg, $item->path);
        } else {
            //@todo add thumbNail logic
//            $old_thumbnail = $this->get_thumbnail_path($source_fullpath);
//
//            // move thumbnail file or thumbnails folder if exists
//            if(file_exists($old_thumbnail)) {
//                $new_thumbnail = $this->get_thumbnail_path($new_fullpath);
//                // delete old thumbnail(s) if destination folder does not exist
//                if(file_exists(dirname($new_thumbnail))) {
//                    FmHelper::copyRecursive($old_thumbnail, $new_thumbnail);
//                }
//            }
            return $this->convertMediaItems($item)[0];
        }

//        $source_path = $this->get['source'];
//        $suffix = (substr($source_path, -1, 1) == '/') ? '/' : '';
//        $tmp = explode('/', trim($source_path, '/'));
//        $filename = array_pop($tmp); // file name or new dir name
//
//        $target_input = $this->get['target'];
//        $target_path = $target_input . '/';
//        $target_path = $this->expandPath($target_path, true);
//
//        $source_fullpath = $this->getFullPath($source_path, true);
//        $target_fullpath = $this->getFullPath($target_path, true);
//        $new_fullpath = $target_fullpath . $filename . $suffix;
//        //Log::info('copying "' . $source_fullpath . '" to "' . $new_fullpath . '"');
//
//        if(!$this->hasPermission('copy')) {
//            $this->error(sprintf($this->lang('NOT_ALLOWED')));
//        }
//
//        if(!is_dir($target_fullpath)) {
//            $this->error(sprintf($this->lang('DIRECTORY_NOT_EXIST'), $target_path));
//        }
//
//        // check system permissions
//        if(!$this->has_system_permission($source_fullpath, ['r']) || !$this->has_system_permission($target_fullpath, ['w'])) {
//            $this->error(sprintf($this->lang('NOT_ALLOWED_SYSTEM')));
//        }
//
//        // check if not requesting main FM userfiles folder
//        if($this->is_root_folder($source_fullpath)) {
//            $this->error(sprintf($this->lang('NOT_ALLOWED')));
//        }
//
//        // check if the name is not in "excluded" list
//        if (!$this->is_allowed_name($target_fullpath, true) ||
//            !$this->is_allowed_name($source_fullpath, is_dir($source_fullpath))
//        ) {
//            $this->error(sprintf($this->lang('INVALID_DIRECTORY_OR_FILE')));
//        }
//
//        // check if file already exists
//        if (file_exists($new_fullpath)) {
//            if(is_dir($new_fullpath)) {
//                $this->error(sprintf($this->lang('DIRECTORY_ALREADY_EXISTS'), rtrim($target_input, '/') . '/' . $filename));
//            } else {
//                $this->error(sprintf($this->lang('FILE_ALREADY_EXISTS'), rtrim($target_input, '/') . '/' . $filename));
//            }
//        }
//
//        // move file or folder
//        if(!FmHelper::copyRecursive($source_fullpath, $new_fullpath)) {
//            if(is_dir($source_fullpath)) {
//                $this->error(sprintf($this->lang('ERROR_COPYING_DIRECTORY'), $filename, $target_input));
//            } else {
//                $this->error(sprintf($this->lang('ERROR_COPYING_FILE'), $filename, $target_input));
//            }
//        } else {
//            //Log::info('moved "' . $source_fullpath . '" to "' . $new_fullpath . '"');
//            $old_thumbnail = $this->get_thumbnail_path($source_fullpath);
//
//            // move thumbnail file or thumbnails folder if exists
//            if(file_exists($old_thumbnail)) {
//                $new_thumbnail = $this->get_thumbnail_path($new_fullpath);
//                // delete old thumbnail(s) if destination folder does not exist
//                if(file_exists(dirname($new_thumbnail))) {
//                    FmHelper::copyRecursive($old_thumbnail, $new_thumbnail);
//                }
//            }
//        }
//
//        $relative_path = $this->cleanPath('/' . $target_path . '/' . $filename . $suffix);
//        return $this->get_file_info($relative_path);
    }

    /**
     * @return mixed
     */
    public function actionMove()
    {
        $sourcePath = $this->get['old'];
        $targetPath = $this->get['new'];
        $oldThumbnail = $this->get_thumbnail_path($sourcePath);

        $item = $this->mediaService->moveItem($sourcePath, $targetPath);
        if ($item instanceof MediaException) {
            /** @var $item MediaException */
            $this->error($item->msg, $item->path);
        } else {
            // move thumbnail file or thumbnails folder if exists
            if(file_exists($oldThumbnail)) {
                $new_thumbnail = $this->get_thumbnail_path($item->fullPath);
                // delete old thumbnail(s) if destination folder does not exist
                if(file_exists(dirname($new_thumbnail))) {
                    rename($oldThumbnail, $new_thumbnail);
                } else {
                    is_dir($oldThumbnail) ? $this->unlinkRecursive($oldThumbnail) : unlink($oldThumbnail);
                }
            }
        }
        return $this->convertMediaItems($item)[0];
    }

    /**
     * @return mixed
     */
    public function actionEditFile()
    {
        $targetPath = $this->get['path'];
        $item = $this->mediaService->getItem($targetPath);
        if ($item instanceof MediaException) {
            /** @var $item MediaException */
            $this->error($item->msg, $item->path);
            return $this->convertMediaItems($item)[0];
        }
        if($item->type == 'folder') {
            $this->error(sprintf($this->lang('FORBIDDEN_ACTION_DIR')));
        }
        $content = $this->mediaService->getFileContent($targetPath);
        if ($content instanceof MediaException) {
            /** @var $item MediaException */
            $this->error($content->msg, $content->path);
            return $this->convertMediaItems($content)[0];
        }
        $item = $this->convertMediaItems($item)[0];
        $item['attributes']['content'] = $content;
        return $item;
    }

    /**
     * @return mixed
     */
    public function actionSaveFile()
    {
        $targetPath = $this->post['path'];

        $mediaItem = $this->mediaService->getItem($targetPath);
        if ($mediaItem instanceof MediaException) {
            /** @var $item MediaException */
            $this->error($mediaItem->msg, $mediaItem->path);
            return $this->convertMediaItems($mediaItem)[0];
        }
        $item = $this->convertMediaItems($mediaItem)[0];

        $target_fullpath = $this->getFullPath($targetPath, true);

        // check if the name is not in "excluded" list
        if(!$this->is_allowed_name($target_fullpath, false)) {
            $this->error(sprintf($this->lang('INVALID_DIRECTORY_OR_FILE')));
        }

        $result = $this->mediaService->setFileContent($targetPath, $this->post['content']);

        if(!is_numeric($result)) {
            $this->error(sprintf($this->lang('ERROR_SAVING_FILE')));
        }
        clearstatcache();

        $mediaItem = $this->mediaService->getItem($targetPath);
        if ($mediaItem instanceof MediaException) {
            /** @var $item MediaException */
            $this->error($mediaItem->msg, $mediaItem->path);
            return $this->convertMediaItems($mediaItem)[0];
        }
        return $this->convertMediaItems($mediaItem)[0];
    }

    /**
     * Seekable stream: http://stackoverflow.com/a/23046071/1789808
     * @return mixed
     */
    public function actionReadFile()
    {
        $target_path = $this->get['path'];
        $item = $this->mediaService->getItem($target_path);
        if ($item instanceof MediaException) {
            /** @var $item MediaException */
            $this->error($item->msg, $item->path);
            return $this->convertMediaItems($item)[0];
        }
        $target_fullpath = $this->getFullPath($target_path, true);
        //Log::info('reading file "' . $target_fullpath . '"');

        if(is_dir($target_fullpath)) {
            $this->error(sprintf($this->lang('FORBIDDEN_ACTION_DIR')));
        }

        // check if the name is not in "excluded" list
        if(!$this->is_allowed_name($target_fullpath, false)) {
            $this->error(sprintf($this->lang('INVALID_DIRECTORY_OR_FILE')));
        }

        // check if file is readable
        if ($item->readable == 0) {
            $this->error(sprintf($this->lang('NOT_ALLOWED_SYSTEM')));
        }

        $filesize = filesize($target_fullpath);
        $length = $filesize;
        $offset = 0;

        if(isset($_SERVER['HTTP_RANGE'])) {
            if(!preg_match('/bytes=(\d+)-(\d+)?/', $_SERVER['HTTP_RANGE'], $matches)) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header('Content-Range: bytes */' . $filesize);
                exit;
            }

            $offset = intval($matches[1]);

            if(isset($matches[2])) {
                $end = intval($matches[2]);
                if($offset > $end) {
                    header('HTTP/1.1 416 Requested Range Not Satisfiable');
                    header('Content-Range: bytes */' . $filesize);
                    exit;
                }
                $length = $end - $offset;
            } else {
                $length = $filesize - $offset;
            }

            $bytes_start = $offset;
            $bytes_end = $offset + $length - 1;

            header('HTTP/1.1 206 Partial Content');
            // A full-length file will indeed be "bytes 0-x/x+1", think of 0-indexed array counts
            header('Content-Range: bytes ' . $bytes_start . '-' . $bytes_end . '/' . $filesize);
            // While playing media by direct link (not via FM) FireFox and IE doesn't allow seeking (rewind) it in player
            // This header can fix this behavior if to put it out of this condition, but it breaks PDF preview
            header('Accept-Ranges: bytes');
        }

        header('Content-Type: ' . mime_content_type($target_fullpath));
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . $length);
        header('Content-Disposition: inline; filename="' . basename($target_fullpath) . '"');

        $fp = fopen($target_fullpath, 'r');
        fseek($fp, $offset);
        $position = 0;

        while($position < $length) {
            $chunk = min($length - $position, 1024 * 8);

            echo fread($fp, $chunk);
            flush();
            ob_flush();

            $position += $chunk;
        }
        exit;
    }

    /**
     * @return array|mixed
     */
    public function actionDownload()
    {
        $targetPath = $this->get['path'];

        $item = $this->mediaService->getItem($targetPath);
        if ($item instanceof MediaException) {
            /** @var $item MediaException */
            $this->error($item->msg, $item->path);
            return $this->convertMediaItems($item)[0];
        }

        if ($item->readable == 0) {
            $this->error(sprintf($this->lang('NOT_ALLOWED')));
        }

        if($item->type == 'folder') {
            // check if permission is granted
            if($this->config['security']['allowFolderDownload'] == false ) {
                $this->error(sprintf($this->lang('NOT_ALLOWED')));
            }
            //@todo ?? check if not requesting data-root folder
        }

        if($this->isAjaxRequest()) {
            return $this->convertMediaItems($item);
        } else {
            $destinationPath = $item->fullPath;
            if($item->type == 'folder') {
                // if Zip archive is created
                $zipItem = $this->mediaService->zipFile($item->fullPath);
                if ($zipItem instanceof MediaException) {
                    /** @var $item MediaException */
                    $this->error($item->msg, $item->path);
                    return $this->convertMediaItems($item)[0];
                }
                $destinationPath = $zipItem->fullPath;
            }
            $fileSize = $this->get_real_filesize($destinationPath);
            header('Content-Description: File Transfer');
            header('Content-Type: ' . FmHelper::mime_content_type($destinationPath));
            header('Content-Disposition: attachment; filename="' . basename($destinationPath) . '"');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . $fileSize);
            // handle caching
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

            // read file by chunks to handle large files
            // if you face an issue while downloading large files yet, try the following solution:
            // https://github.com/servocoder/RichFilemanager/issues/78

            $chunk_size = 5 * 1024 * 1024;
            if ($chunk_size && $fileSize > $chunk_size) {
                $handle = fopen($destinationPath, 'rb');
                while (!feof($handle)) {
                    echo fread($handle, $chunk_size);
                    @ob_flush();
                    @flush();
                }
                fclose($handle);
            } else {
                readfile($destinationPath);
            }
            exit;
        }
    }













    /**
     * @inheritdoc
     */
    public function actionReplace()
    {
        $source_path = $this->post['path'];
        $source_fullpath = $this->getFullPath($source_path);
        //Log::info('replacing file "' . $source_fullpath . '"');

        $target_path = dirname($source_path) . '/';
        $target_fullpath = $this->getFullPath($target_path, true);
        //Log::info('replacing target path "' . $target_fullpath . '"');

        if(!$this->hasPermission('replace') || !$this->hasPermission('upload')) {
            $this->error(sprintf($this->lang('NOT_ALLOWED')));
        }

        if(is_dir($source_fullpath)) {
            $this->error(sprintf($this->lang('NOT_ALLOWED')));
        }

        // check if the name is not in "excluded" list
        if(!$this->is_allowed_name($source_fullpath, false)) {
            $this->error(sprintf($this->lang('INVALID_DIRECTORY_OR_FILE')));
        }

        // check if the given file has the same extension as the old one
        if(strtolower(pathinfo($_FILES[$this->config['upload']['paramName']]['name'], PATHINFO_EXTENSION)) != strtolower(pathinfo($source_path, PATHINFO_EXTENSION))) {
            $this->error(sprintf($this->lang('ERROR_REPLACING_FILE') . ' ' . pathinfo($source_path, PATHINFO_EXTENSION)));
        }

        // check if file is writable
        if(!$this->has_system_permission($source_fullpath, ['w'])) {
            $this->error(sprintf($this->lang('NOT_ALLOWED_SYSTEM')));
        }

        // check if target path is writable
        if(!$this->has_system_permission($target_fullpath, ['w'])) {
            $this->error(sprintf($this->lang('NOT_ALLOWED_SYSTEM')));
        }

        $content = $this->initUploader([
            'upload_dir' => $target_fullpath,
        ])->post(false);

        $response_data = [];
        $files = isset($content[$this->config['upload']['paramName']]) ?
            $content[$this->config['upload']['paramName']] : null;
        // there is only one file in the array as long as "singleFileUploads" is set to "true"
        if ($files && is_array($files) && is_object($files[0])) {
            $file = $files[0];
            if(isset($file->error)) {
                $this->error($file->error);
            } else {
                $replacement_fullpath = $target_fullpath . $file->name;
                //Log::info('replacing "' . $source_fullpath . '" with "' . $replacement_fullpath . '"');

                rename($replacement_fullpath, $source_fullpath);

                $new_thumbnail = $this->get_thumbnail_path($replacement_fullpath);
                $old_thumbnail = $this->get_thumbnail_path($source_fullpath);
                if(file_exists($new_thumbnail)) {
                    rename($new_thumbnail, $old_thumbnail);
                }

                $relative_path = $this->cleanPath('/' . $source_path);
                $item = $this->get_file_info($relative_path);
                $response_data[] = $item;
            }
        } else {
            $this->error(sprintf($this->lang('ERROR_UPLOADING_FILE')));
        }

        return $response_data;
    }

    /**
     * @inheritdoc
     */
    public function actionGetImage($thumbnail)
    {
        $target_path = $this->get['path'];
        $target_fullpath = $this->getFullPath($target_path, true);
        //Log::info('loading image "' . $target_fullpath . '"');

        if(is_dir($target_fullpath)) {
            $this->error(sprintf($this->lang('FORBIDDEN_ACTION_DIR')));
        }

        // check if the name is not in "excluded" list
        if(!$this->is_allowed_name($target_fullpath, false)) {
            $this->error(sprintf($this->lang('INVALID_DIRECTORY_OR_FILE')));
        }

        // check if file is readable
        if(!$this->has_system_permission($target_fullpath, ['r'])) {
            $this->error(sprintf($this->lang('NOT_ALLOWED_SYSTEM')));
        }

        // if $thumbnail is set to true we return the thumbnail
        if($thumbnail === true && $this->config['images']['thumbnail']['enabled'] === true) {
            // get thumbnail (and create it if needed)
            $returned_path = $this->get_thumbnail($target_fullpath);
        } else {
            $returned_path = $target_fullpath;
        }

        header("Content-type: image/octet-stream");
        header("Content-Transfer-Encoding: binary");
        header("Content-length: " . $this->get_real_filesize($returned_path));
        header('Content-Disposition: inline; filename="' . basename($returned_path) . '"');

        readfile($returned_path);
        exit();
    }


    /**
     * @inheritdoc
     */
    public function actionSummarize()
    {
        $attributes = [
            'size' => 0,
            'files' => 0,
            'folders' => 0,
            'sizeLimit' => $this->config['options']['fileRootSizeLimit'],
        ];

        $path = rtrim($this->path_to_files, '/') . '/';
        try {
            $this->getDirSummary($path, $attributes);
        } catch (Exception $e) {
            $this->error(sprintf($this->lang('ERROR_SERVER')));
        }

        return [
            'id' => '/',
            'type' => 'summary',
            'attributes' => $attributes,
        ];
    }










































    protected function setParams()
    {
        $tmp = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
        $tmp = explode('?',$tmp);
        $params = [];
        if(isset($tmp[1]) && $tmp[1]!='') {
            $params_tmp = explode('&',$tmp[1]);
            if(is_array($params_tmp)) {
                foreach($params_tmp as $value) {
                    $tmp = explode('=',$value);
                    if(isset($tmp[0]) && $tmp[0]!='' && isset($tmp[1]) && $tmp[1]!='') {
                        $params[$tmp[0]] = $tmp[1];
                    }
                }
            }
        }
        $this->refParams = $params;
    }

    /**
     * Load language file and retrieve all messages.
     * Defines language code based on "langCode" variable if exists otherwise uses configuration option.
     */
    protected function loadLanguageFile()
    {
        $lang = $this->config['options']['culture'];
        if(isset($this->refParams['langCode'])) {
            $lang = $this->refParams['langCode'];
        }

        $lang_path = dirname(dirname(dirname(__FILE__))) . "/languages/{$lang}.json";

        if (file_exists($lang_path)) {
            $stream = file_get_contents($lang_path);
            $this->language = json_decode($stream, true);
        }
    }

    /**
     * Checking if permission is set or not for a given action
     * @param string $action
     * @return boolean
     */
    protected function hasPermission($action)
    {
        return in_array($action, $this->allowed_actions);
    }

    /**
     * Echo error message and terminate the application
     * @param string $title
     */
    public function error($title)
    {
        //Log::info('error message: "' . $title . '"');

        if($this->isAjaxRequest()) {
            $error_object = [
                'id' => 'server',
                'code' => '500',
                'title' => $title
            ];

            echo json_encode([
                'errors' => [$error_object],
            ]);
        } else {
            echo "<h2>Server error: {$title}</h2>";
        }

        exit;
    }

    /**
     * Setup language by code
     * @param $string
     * @return string
     */
    public function lang($string)
    {
        if(!empty($this->language[$string])) {
            return $this->language[$string];
        } else {
            return 'Language string error on ' . $string;
        }
    }

    /**
     * Retrieve data from $_GET global var
     * @param string $var
     * @param bool $sanitize
     * @return bool
     */
    public function getvar($var, $sanitize = true)
    {
        if(!isset($_GET[$var]) || $_GET[$var]=='') {
            $this->error(sprintf($this->lang('INVALID_VAR').' -- '.$var,$var));
        } else {
            if($sanitize) {
                $this->get[$var] = $this->sanitize($_GET[$var]);
            } else {
                $this->get[$var] = $_GET[$var];
            }
            return true;
        }
    }

    /**
     * Retrieve data from $_POST global var
     * @param string $var
     * @param bool $sanitize
     * @return bool
     */
    public function postvar($var, $sanitize = true)
    {
        if(!isset($_POST[$var]) || ($var != 'content' && $_POST[$var]=='')) {
            $this->error(sprintf($this->lang('INVALID_VAR'),$var));
        } else {
            if($sanitize) {
                $this->post[$var] = $this->sanitize($_POST[$var]);
            } else {
                $this->post[$var] = $_POST[$var];
            }
            return true;
        }
    }

    /**
     * Retrieve data from $_SERVER global var
     * @param string $var
     * @param string|null $default
     * @return bool
     */
    public function get_server_var($var, $default = null)
    {
        return !isset($_SERVER[$var]) ? $default : $_SERVER[$var];
    }

    /**
     * Returns whether this is an AJAX (XMLHttpRequest) request.
     * Note that jQuery doesn't set the header in case of cross domain
     * requests: https://stackoverflow.com/questions/8163703/cross-domain-ajax-doesnt-send-x-requested-with-header
     * @return boolean whether this is an AJAX (XMLHttpRequest) request.
     */
    public function isAjaxRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * Sanitize global vars: $_GET, $_POST
     * @param string $var
     * @return mixed|string
     */
    protected function sanitize($var)
    {
        $sanitized = strip_tags($var);
        $sanitized = str_replace('http://', '', $sanitized);
        $sanitized = str_replace('https://', '', $sanitized);
        $sanitized = str_replace('../', '', $sanitized);

        return $sanitized;
    }

    /**
     * Clean string to retrieve correct file/folder name.
     * @param string $string
     * @param array $allowed
     * @return array|mixed
     */
    public function normalizeString($string, $allowed = [])
    {
        $allow = '';
        if(!empty($allowed)) {
            foreach ($allowed as $value) {
                $allow .= "\\$value";
            }
        }

        if($this->config['security']['normalizeFilename'] === true) {
            // Remove path information and dots around the filename, to prevent uploading
            // into different directories or replacing hidden system files.
            // Also remove control characters and spaces (\x00..\x20) around the filename:
            $string = trim(basename(stripslashes($string)), ".\x00..\x20");

            // Replace chars which are not related to any language
            $replacements = [' '=>'_', '\''=>'_', '/'=>'', '\\'=>''];
            $string = strtr($string, $replacements);
        }

        if($this->config['options']['charsLatinOnly'] === true) {
            // transliterate if extension is loaded
            if(extension_loaded('intl') === true && function_exists('transliterator_transliterate')) {
                $options = 'Any-Latin; Latin-ASCII; NFD; [:Nonspacing Mark:] Remove; NFC;';
                $string = transliterator_transliterate($options, $string);
            }
            // clean up all non-latin chars
            $string = preg_replace("/[^{$allow}_a-zA-Z0-9]/u", '', $string);
        }

        // remove double underscore
        $string = preg_replace('/[_]+/', '_', $string);

        return $string;
    }

    /**
     * Defines real size of file
     * Based on https://github.com/jkuchar/BigFileTools project by Jan Kuchar
     * @param string $path
     * @return int|string
     * @throws Exception
     */
    public static function get_real_filesize($path)
    {
        // This should work for large files on 64bit platforms and for small files everywhere
        $fp = fopen($path, "rb");
        if (!$fp) {
            throw new Exception("Cannot open specified file for reading.");
        }
        $flockResult = flock($fp, LOCK_SH);
        $seekResult = fseek($fp, 0, SEEK_END);
        $position = ftell($fp);
        flock($fp, LOCK_UN);
        fclose($fp);

        if(!($flockResult === false || $seekResult !== 0 || $position === false)) {
            return sprintf("%u", $position);
        }

        // Try to define file size via CURL if installed
        if (function_exists("curl_init")) {
            $ch = curl_init("file://" . rawurlencode($path));
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            $data = curl_exec($ch);
            curl_close($ch);
            if ($data !== false && preg_match('/Content-Length: (\d+)/', $data, $matches)) {
                return $matches[1];
            }
        }

        return filesize($path);
    }

    /**
     * Check if file is allowed to upload regarding the configuration settings
     * @param string $file
     * @return bool
     */
    public function is_allowed_file_type($file)
    {
        $path_parts = pathinfo($file);

        // if there is no extension
        if (!isset($path_parts['extension'])) {
            // we check if no extension file are allowed
            return (bool)$this->config['security']['allowNoExtension'];
        }

        $extensions = array_map('strtolower', $this->config['upload']['restrictions']);

        if($this->config['upload']['policy'] == 'DISALLOW_ALL') {
            if(!in_array(strtolower($path_parts['extension']), $extensions)) {
                return false;
            }
        }
        if($this->config['upload']['policy'] == 'ALLOW_ALL') {
            if(in_array(strtolower($path_parts['extension']), $extensions)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if file or folder name is not in "excluded" list
     * @param string $name
     * @param bool $is_dir
     * @return bool
     */
    public function is_allowed_name($name, $is_dir = false)
    {
        $name = basename($name);

        // check if folder name is allowed regarding the security Policy settings
        if ($is_dir && (
                in_array($name, $this->config['security']['excluded_dirs']) ||
                preg_match($this->config['security']['excluded_dirs_REGEXP'], $name))
        ) {
            return false;
        }

        // check if file name is allowed regarding the security Policy settings
        if (!$is_dir && (
                in_array($name, $this->config['security']['excluded_files']) ||
                preg_match($this->config['security']['excluded_files_REGEXP'], $name))
        ) {
            return false;
        }

        return true;
    }

    /**
     * Remove excluded and filtered items from output
     * @param array $item
     * @return bool
     */
    public function filter_output($item)
    {
        // filter out item if the name is not in "excluded" list
        if(!$this->is_allowed_name($item["attributes"]["name"], $item["type"] === self::TYPE_FOLDER)) {
            return false;
        }

        // filter out item if any filter is specified and item is matched
        $filter_name = isset($this->refParams['type']) ? $this->refParams['type'] : null;
        $allowed_types = isset($this->config['outputFilter'][$filter_name]) ? $this->config['outputFilter'][$filter_name] : null;
        if($filter_name && is_array($allowed_types) && $item["type"] === self::TYPE_FILE) {
            return (in_array(strtolower($item["attributes"]["extension"]), $allowed_types));
        }

        return true;
    }

    /**
     * Check whether file is image by its mime type
     * For S3 plugin it may cost extra request for each file
     * @param $file
     * @return bool
     */
    public function is_image_file($file)
    {
        $mime = FmHelper::mime_content_type($file);

        $imagesMime = [
            "image/jpeg",
            "image/png",
            "image/gif",
            "image/bmp",
            "image/svg+xml",
        ];
        return in_array($mime, $imagesMime);
    }


    public function setFileRoot($path, $mkdir = false) {
        if($this->config['options']['serverRoot'] === true) {
            $this->dynamic_fileroot = $path;
            $this->path_to_files = $this->cleanPath($this->doc_root . '/' . $path);
        } else {
            $this->path_to_files = $this->cleanPath($path);
        }

        //Log::info('Overwritten with setFileRoot() method:');
        //Log::info('$this->path_to_files: "' . $this->path_to_files . '"');
        //Log::info('$this->dynamic_fileroot: "' . $this->dynamic_fileroot . '"');

        if($mkdir && !file_exists($this->path_to_files)) {
            mkdir($this->path_to_files, 0755, true);
            //Log::info('creating "' . $this->path_to_files . '" folder through mkdir()');
        }
    }

    /**
     * @param array $settings
     * @return LocalUploadHandler
     */
    public function initUploader($settings = [])
    {
        $data = [
                'images_only' => $this->config['upload']['imagesOnly'] || (isset($this->refParams['type']) && strtolower($this->refParams['type'])=='images'),
            ] + $settings;

        if(isset($data['upload_dir'])) {
            $data['thumbnails_dir'] = rtrim($this->get_thumbnail_path($data['upload_dir']), '/');
        }

        return new LocalUploadHandler([
            'fm' => [
                'instance' => $this,
                'data' => $data,
            ],
        ]);
    }





    /**
     * Check if system permission is granted
     * @param string $filepath
     * @param array $permissions
     * @return bool
     */
    protected function has_system_permission($filepath, $permissions)
    {
        if(in_array('r', $permissions)) {
            if(!is_readable($filepath)) {
                //Log::info('Not readable path "' . $filepath . '"');
                return false;
            };
        }
        if(in_array('w', $permissions)) {
            if(!is_writable($filepath)) {
                //Log::info('Not writable path "' . $filepath . '"');
                return false;
            }
        }
        return true;
    }

    /**
     * Create array with file properties
     * @param string $relative_path
     * @return array
     */
    protected function get_file_info($relative_path)
    {
        $fullpath = $this->getFullPath($relative_path);
        $pathInfo = pathinfo($fullpath);
        $filemtime = filemtime($fullpath);

        // check if file is readable
        $is_readable = $this->has_system_permission($fullpath, ['r']);
        // check if file is writable
        $is_writable = $this->has_system_permission($fullpath, ['w']);

        if(is_dir($fullpath)) {
            $model = $this->folder_model;
        } else {
            $model = $this->file_model;
            $model['attributes']['extension'] = isset($pathInfo['extension']) ? $pathInfo['extension'] : '';

            if ($is_readable) {
                $model['attributes']['size'] = $this->get_real_filesize($fullpath);

                if($this->is_image_file($fullpath)) {
                    if($model['attributes']['size']) {
                        list($width, $height, $type, $attr) = getimagesize($fullpath);
                    } else {
                        list($width, $height) = [0, 0];
                    }

                    $model['attributes']['width'] = $width;
                    $model['attributes']['height'] = $height;
                }
            }
        }

        $model['id'] = $relative_path;
        $model['attributes']['name'] = $pathInfo['basename'];
        $model['attributes']['path'] = $this->getDynamicPath($fullpath);
        $model['attributes']['readable'] = (int) $is_readable;
        $model['attributes']['writable'] = (int) $is_writable;
        $model['attributes']['timestamp'] = $filemtime;
        $model['attributes']['modified'] = $this->formatDate($filemtime);
        //$model['attributes']['created'] = $model['attributes']['modified']; // PHP cannot get create timestamp
        return $model;
    }

    /**
     * Return full path to file
     * @param string $path
     * @param bool $verify If file or folder exists and valid
     * @return mixed|string
     */
    protected function getFullPath($path, $verify = false)
    {
        $full_path = $this->cleanPath($this->path_to_files . '/' . $path);

        if($verify === true) {
            if(!file_exists($full_path) || !$this->is_valid_path($full_path)) {
                $langKey = is_dir($full_path) ? 'DIRECTORY_NOT_EXIST' : 'FILE_DOES_NOT_EXIST';
                $this->error(sprintf($this->lang($langKey), $path));
            }
        }
        return $full_path;
    }

    /**
     * Returns path without document root
     * @param string $fullPath
     * @return mixed
     */
    protected function getDynamicPath($fullPath)
    {
        // empty string makes FM to use connector path for preview instead of absolute path
        // COMMENTED: due to it prevents to build absolute URL when "serverRoot" is "false" and "fileRoot" is provided
        // as well as "previewUrl" value in the JSON configuration file is set to the correct URL
//        if(empty($this->dynamic_fileroot)) {
//            return '';
//        }
        $path = $this->dynamic_fileroot . '/' . $this->getRelativePath($fullPath);
        return $this->cleanPath($path);
    }

    /**
     * Returns path without "path_to_files"
     * @param string $fullPath
     * @return mixed
     */
    protected function getRelativePath($fullPath)
    {
        return $this->subtractPath($fullPath, $this->path_to_files);
    }

    /**
     * Subtracts subpath from the fullpath
     * @param string $fullPath
     * @param string $subPath
     * @return string
     */
    protected function subtractPath($fullPath, $subPath)
    {
        $position = strrpos($fullPath, $subPath);
        if($position === 0) {
            $path = substr($fullPath, strlen($subPath));
            return $path ? $this->cleanPath('/' . $path) : '';
        }
        return '';
    }

    /**
     * Check whether path is valid by comparing paths
     * @param string $path
     * @return bool
     */
    protected function is_valid_path($path)
    {
        $rp_substr = substr(realpath($path) . DIRECTORY_SEPARATOR, 0, strlen(realpath($this->path_to_files))) . DIRECTORY_SEPARATOR;
        $rp_files = realpath($this->path_to_files) . DIRECTORY_SEPARATOR;

        // handle better symlinks & network path - issue #448
        $pattern = ['/\\\\+/', '/\/+/'];
        $replacement = ['\\\\', '/'];
        $rp_substr = preg_replace($pattern, $replacement, $rp_substr);
        $rp_files = preg_replace($pattern, $replacement, $rp_files);
        $match = ($rp_substr === $rp_files);

        if(!$match) {
            //Log::info('Invalid path "' . $path . '"');
            //Log::info('real path: "' . $rp_substr . '"');
            //Log::info('path to files: "' . $rp_files . '"');
        }
        return $match;
    }

    /**
     * Delete folder recursive
     * @param string $dir
     * @param bool $deleteRootToo
     */
    protected function unlinkRecursive($dir, $deleteRootToo = true)
    {
        if(!$dh = @opendir($dir)) {
            return;
        }
        while (false !== ($obj = readdir($dh))) {
            if($obj == '.' || $obj == '..') {
                continue;
            }

            if (!@unlink($dir . '/' . $obj)) {
                $this->unlinkRecursive($dir.'/'.$obj, true);
            }
        }
        closedir($dh);

        if ($deleteRootToo) {
            @rmdir($dir);
        }

        return;
    }

    /**
     * Clean path string to remove multiple slashes, etc.
     * @param string $string
     * @return $string
     */
    public function cleanPath($string)
    {
        // replace backslashes (windows separators)
        $string = str_replace("\\", "/", $string);
        // remove multiple slashes
        $string = preg_replace('#/+#', '/', $string);
        return $string;
    }

    /**
     * Check whether the folder is root
     * @param string $path
     * @return bool
     */
    protected function is_root_folder($path)
    {
        return rtrim($this->path_to_files, '/') == rtrim($path, '/');
    }

    /**
     * Check whether the file model could be edited regarding configuration setup
     * @param string $file_model
     * @return bool
     */
    protected function is_editable($file_model)
    {
        $allowed_types = array_map('strtolower', $this->config['security']['editRestrictions']);
        return in_array(strtolower($file_model['attributes']['extension']), $allowed_types);
    }

    /**
     * Remove "../" from path
     * @param string $path Path to be converted
     * @param bool $clean If dir names should be cleaned
     * @return string or false in case of error (as exception are not used here)
     */
    public function expandPath($path, $clean = false)
    {
        $todo = explode('/', $path);
        $fullPath = [];

        foreach ($todo as $dir) {
            if ($dir == '..') {
                $element = array_pop($fullPath);
                if (is_null($element)) {
                    return false;
                }
            } else {
                if ($clean) {
                    $dir = $this->normalizeString($dir);
                }
                array_push($fullPath, $dir);
            }
        }
        return implode('/', $fullPath);
    }

    /**
     * Format timestamp string
     * @param string $timestamp
     * @return string
     */
    protected function formatDate($timestamp)
    {
        return date($this->config['options']['dateFormat'], $timestamp);
    }

    /**
     * Returns summary info for specified folder
     * @param string $dir
     * @param array $result
     * @return array
     */
    public function getDirSummary($dir, &$result = ['size' => 0, 'files' => 0, 'folders' => 0])
    {
        // suppress permission denied and other errors
        $files = @scandir($dir);
        if($files === false) {
            return $result;
        }

        foreach($files as $file) {
            if($file == "." || $file == "..") {
                continue;
            }
            $path = $dir . $file;
            $is_dir = is_dir($path);

            if ($is_dir && $this->is_allowed_name($file, true)) {
                $result['folders']++;
                $this->getDirSummary($path . '/', $result);
            }
            if (!$is_dir && $this->is_allowed_name($file, false)) {
                $result['files']++;
                $result['size'] += filesize($path);
            }
        }

        return $result;
    }

    /**
     * Calculates total size of all files
     * @return mixed
     */
    public function getRootTotalSize()
    {
        $path = rtrim($this->path_to_files, '/') . '/';
        $result = $this->getDirSummary($path);
        return $result['size'];
    }

    /**
     * Return Thumbnail path from given path, works for both file and dir path
     * @param string $path
     * @return string
     */
    protected function get_thumbnail_path($path)
    {
        $relative_path = $this->getRelativePath($path);
        $thumbnail_path = $this->path_to_files . '/' . $this->config['images']['thumbnail']['dir'] . '/';

        if(is_dir($path)) {
            $thumbnail_fullpath = $thumbnail_path . $relative_path . '/';
        } else {
            $thumbnail_fullpath = $thumbnail_path . dirname($relative_path) . '/' . basename($path);
        }

        return $this->cleanPath($thumbnail_fullpath);
    }

    /**
     * Returns path to image file thumbnail, creates thumbnail if doesn't exist
     * @param string $path
     * @return string
     */
    protected function get_thumbnail($path)
    {
        $thumbnail_fullpath = $this->get_thumbnail_path($path);

        // generate thumbnail if it doesn't exist or caching is disabled
        if(!file_exists($thumbnail_fullpath) || $this->config['images']['thumbnail']['cache'] === false) {
            $this->createThumbnail($path, $thumbnail_fullpath);
        }

        return $thumbnail_fullpath;
    }

    /**
     * Creates thumbnail from the original image
     * @param $imagePath
     * @param $thumbnailPath
     */
    protected function createThumbnail($imagePath, $thumbnailPath)
    {
        if($this->config['images']['thumbnail']['enabled'] === true) {
            //Log::info('generating thumbnail "' . $thumbnailPath . '"');

            // create folder if it does not exist
            if(!file_exists(dirname($thumbnailPath))) {
                mkdir(dirname($thumbnailPath), 0755, true);
            }

            $this->initUploader([
                'upload_dir' => dirname($imagePath) . '/',
            ])->create_thumbnail_image($imagePath);
        }
    }

}