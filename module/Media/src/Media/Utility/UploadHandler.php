<?php
namespace Media\Utility;


use Exception;

class UploadHandler
{
    public $mimeFileInfo        = true;     // MIME detection with Fileinfo PECL extension
    public $mimeFile            = true;     // MIME detection with UNIX file() command
    public $mimeMagic           = true;     // MIME detection with mime_magic (mime_content_type())
    public $mimeGetImageSize    = true;     // MIME detection with getimagesize()
    public $autoRename          = false;
    public $autoOverwrite       = false;
    public $autoCreateFolder    = true;
    public $dirChmod            = 0777;


    private $srcPath;
    private $srcFileName;
    private $srcError = null;
    private $srcExt;
    private $srcName;
    private $srcSize;
    private $srcMimeType;

    private $dstPath;
    private $dstFileName;
    private $dstName;
    private $dstExt;

    private $isImage;
    private $imageType;
    private $supportedImages;
    private $maxFileSizeRaw;
    private $maxFileSize;
    private $allowedMimeTypes;
    private $mimeTypes;
    private $newName;
    private $dstFolderPath;
    private $nameCount = 1;
	private $imageProcessor;

	/**
     * UploadHandler constructor.
     * @param $file {string|array} path string | $_FILES['form_field'] array
     * @param $destPath {string} save path for upload
     */
    public function __construct($file = null, $destPath = null) {
        $this->maxFileSizeRaw = trim(ini_get('upload_max_filesize'));
        $this->maxFileSize = $this->getSize($this->maxFileSizeRaw);

        $this->allowedMimeTypes = array(
            'application/arj',
            'application/excel',
            'application/gnutar',
            'application/mspowerpoint',
            'application/msword',
            'application/octet-stream',
            'application/onenote',
            'application/pdf',
            'application/plain',
            'application/postscript',
            'application/powerpoint',
            'application/rar',
            'application/rtf',
            'application/vnd.ms-excel',
            'application/vnd.ms-excel.addin.macroEnabled.12',
            'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
            'application/vnd.ms-excel.sheet.macroEnabled.12',
            'application/vnd.ms-excel.template.macroEnabled.12',
            'application/vnd.ms-office',
            'application/vnd.ms-officetheme',
            'application/vnd.ms-powerpoint',
            'application/vnd.ms-powerpoint.addin.macroEnabled.12',
            'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
            'application/vnd.ms-powerpoint.slide.macroEnabled.12',
            'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
            'application/vnd.ms-powerpoint.template.macroEnabled.12',
            'application/vnd.ms-word',
            'application/vnd.ms-word.document.macroEnabled.12',
            'application/vnd.ms-word.template.macroEnabled.12',
            'application/vnd.oasis.opendocument.chart',
            'application/vnd.oasis.opendocument.database',
            'application/vnd.oasis.opendocument.formula',
            'application/vnd.oasis.opendocument.graphics',
            'application/vnd.oasis.opendocument.graphics-template',
            'application/vnd.oasis.opendocument.image',
            'application/vnd.oasis.opendocument.presentation',
            'application/vnd.oasis.opendocument.presentation-template',
            'application/vnd.oasis.opendocument.spreadsheet',
            'application/vnd.oasis.opendocument.spreadsheet-template',
            'application/vnd.oasis.opendocument.text',
            'application/vnd.oasis.opendocument.text-master',
            'application/vnd.oasis.opendocument.text-template',
            'application/vnd.oasis.opendocument.text-web',
            'application/vnd.openofficeorg.extension',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.openxmlformats-officedocument.presentationml.slide',
            'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
            'application/vnd.openxmlformats-officedocument.presentationml.template',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
            'application/vocaltec-media-file',
            'application/wordperfect',
            'application/x-bittorrent',
            'application/x-bzip',
            'application/x-bzip2',
            'application/x-compressed',
            'application/x-excel',
            'application/x-gzip',
            'application/x-latex',
            'application/x-midi',
            'application/xml',
            'application/x-msexcel',
            'application/x-rar',
            'application/x-rar-compressed',
            'application/x-rtf',
            'application/x-shockwave-flash',
            'application/x-sit',
            'application/x-stuffit',
            'application/x-troff-msvideo',
            'application/x-zip',
            'application/x-zip-compressed',
            'application/zip',
            'audio/*',
            'image/*',
            'multipart/x-gzip',
            'multipart/x-zip',
            'text/plain',
            'text/rtf',
            'text/richtext',
            'text/xml',
            'video/*',
            'text/csv'
        );
        $this->mimeTypes = array(
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpe' => 'image/jpeg',
            'gif' => 'image/gif',
            'png' => 'image/png',
            'bmp' => 'image/bmp',
            'flif' => 'image/flif',
            'flv' => 'video/x-flv',
            'js' => 'application/x-javascript',
            'json' => 'application/json',
            'tiff' => 'image/tiff',
            'css' => 'text/css',
            'xml' => 'application/xml',
            'doc' => 'application/msword',
            'xls' => 'application/vnd.ms-excel',
            'xlt' => 'application/vnd.ms-excel',
            'xlm' => 'application/vnd.ms-excel',
            'xld' => 'application/vnd.ms-excel',
            'xla' => 'application/vnd.ms-excel',
            'xlc' => 'application/vnd.ms-excel',
            'xlw' => 'application/vnd.ms-excel',
            'xll' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pps' => 'application/vnd.ms-powerpoint',
            'rtf' => 'application/rtf',
            'pdf' => 'application/pdf',
            'html' => 'text/html',
            'htm' => 'text/html',
            'php' => 'text/html',
            'txt' => 'text/plain',
            'mpeg' => 'video/mpeg',
            'mpg' => 'video/mpeg',
            'mpe' => 'video/mpeg',
            'mp3' => 'audio/mpeg3',
            'wav' => 'audio/wav',
            'aiff' => 'audio/aiff',
            'aif' => 'audio/aiff',
            'avi' => 'video/msvideo',
            'wmv' => 'video/x-ms-wmv',
            'mov' => 'video/quicktime',
            'zip' => 'application/zip',
            'tar' => 'application/x-tar',
            'swf' => 'application/x-shockwave-flash',
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ott' => 'application/vnd.oasis.opendocument.text-template',
            'oth' => 'application/vnd.oasis.opendocument.text-web',
            'odm' => 'application/vnd.oasis.opendocument.text-master',
            'odg' => 'application/vnd.oasis.opendocument.graphics',
            'otg' => 'application/vnd.oasis.opendocument.graphics-template',
            'odp' => 'application/vnd.oasis.opendocument.presentation',
            'otp' => 'application/vnd.oasis.opendocument.presentation-template',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
            'ots' => 'application/vnd.oasis.opendocument.spreadsheet-template',
            'odc' => 'application/vnd.oasis.opendocument.chart',
            'odf' => 'application/vnd.oasis.opendocument.formula',
            'odb' => 'application/vnd.oasis.opendocument.database',
            'odi' => 'application/vnd.oasis.opendocument.image',
            'oxt' => 'application/vnd.openofficeorg.extension',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'docm' => 'application/vnd.ms-word.document.macroEnabled.12',
            'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
            'dotm' => 'application/vnd.ms-word.template.macroEnabled.12',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xlsm' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
            'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
            'xltm' => 'application/vnd.ms-excel.template.macroEnabled.12',
            'xlsb' => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
            'xlam' => 'application/vnd.ms-excel.addin.macroEnabled.12',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'pptm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
            'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
            'ppsm' => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
            'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
            'potm' => 'application/vnd.ms-powerpoint.template.macroEnabled.12',
            'ppam' => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
            'sldx' => 'application/vnd.openxmlformats-officedocument.presentationml.slide',
            'sldm' => 'application/vnd.ms-powerpoint.slide.macroEnabled.12',
            'thmx' => 'application/vnd.ms-officetheme',
            'onetoc' => 'application/onenote',
            'onetoc2' => 'application/onenote',
            'onetmp' => 'application/onenote',
            'onepkg' => 'application/onenote',
            'csv' => 'text/csv',
        );

        if (is_array($file)) {
            $this->setSource($file);
        }
        if (is_string($destPath)) {
            $this->setDestinationPath($destPath);
        }
    }

	public function setImageProcessor($imageProcessor)
	{
		$this->imageProcessor = $imageProcessor;
    }

    /**
     * @param $array $_FILES['form_field'] array
     * @return bool
     */
    public function setSource($array, $overrideName = null) {
        $this->srcPath      = $array['tmp_name'];
        $this->srcFileName  = ($overrideName == null) ? $array['name'] : $overrideName;
        $this->srcSize      = $array['size'];
        $this->srcMimeType  = $array['type'];
        $this->srcError     = trim($array['error']);
        return true;
    }

	public function setName($filename)
	{

    }

    public function validateSource() {
        if (!$this->srcPath || $this->srcPath == '') {
            throw new Exception('missing source!');
        }
        switch ($this->srcError) {
            case null:
            case UPLOAD_ERR_OK:
                // all is OK
                break;
            case UPLOAD_ERR_INI_SIZE:
                throw new Exception('The uploaded file exceeds the upload_max_filesize directive in php.ini', UPLOAD_ERR_INI_SIZE);
                break;
            case UPLOAD_ERR_FORM_SIZE:
                throw new Exception('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form', UPLOAD_ERR_FORM_SIZE);
                break;
            case UPLOAD_ERR_PARTIAL:
                throw new Exception('The uploaded file was only partially uploaded', UPLOAD_ERR_PARTIAL);
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new Exception('No file was uploaded.', UPLOAD_ERR_NO_FILE);
                break;
            case @UPLOAD_ERR_NO_TMP_DIR:
                throw new Exception('Missing a temporary folder.', UPLOAD_ERR_NO_TMP_DIR);
                break;
            case @UPLOAD_ERR_CANT_WRITE:
                throw new Exception('Failed to write file to disk', UPLOAD_ERR_CANT_WRITE);
                break;
            case @UPLOAD_ERR_EXTENSION:
                throw new Exception('A PHP extension stopped the file upload.', UPLOAD_ERR_EXTENSION);
                break;
            default:
                throw new Exception('unknown error while uploading');
        }
        if ($this->srcFileName == '') {
            throw new Exception('missing or incorrect file name!');
        }
    }

    public function upload() {
        $this->validateSource();

        // determines the supported MIME types, and matching image format
        $this->supportedImages = array();
        if ($this->gdVersion()) {
            if (imagetypes() & IMG_GIF) {
                $this->supportedImages['image/gif'] = 'gif';
            }
            if (imagetypes() & IMG_JPG) {
                $this->supportedImages['image/jpg'] = 'jpg';
                $this->supportedImages['image/jpeg'] = 'jpg';
                $this->supportedImages['image/pjpeg'] = 'jpg';
            }
            if (imagetypes() & IMG_PNG) {
                $this->supportedImages['image/png'] = 'png';
                $this->supportedImages['image/x-png'] = 'png';
            }
            if (imagetypes() & IMG_WBMP) {
                $this->supportedImages['image/bmp'] = 'bmp';
                $this->supportedImages['image/x-ms-bmp'] = 'bmp';
                $this->supportedImages['image/x-windows-bmp'] = 'bmp';
            }
        }

        preg_match('/\.([^\.]*$)/', $this->srcFileName, $extension);
        if (is_array($extension) && sizeof($extension) > 0) {
            $this->srcExt      = strtolower($extension[1]);
            $this->srcName     = substr($this->srcFileName, 0, ((strlen($this->srcFileName) - strlen($this->srcExt)))-1);
        } else {
            $this->srcExt      = '';
            $this->srcName     = $this->srcFileName;
        }

        $this->srcMimeType = $this->detectMime();
        // determine whether the file is an image
        if ($this->srcMimeType && is_string($this->srcMimeType) && !empty($this->srcMimeType) && array_key_exists($this->srcMimeType, $this->supportedImages)) {
            $this->isImage = true;
            $this->imageType = $this->supportedImages[$this->srcMimeType];
        }

        // if the file has no extension, we try to guess it from the MIME type
        if (empty($this->srcExt)) {
            if ($key = array_search($this->srcMimeType, $this->mimeTypes)) {
                if ($key === false) {
                    throw new Exception("can't determinate file extension");
                }
                $this->srcExt = $key;
            }
        }
        // if the file is text based, or has a dangerous extension, we rename it as .txt
        if ((((substr($this->srcMimeType, 0, 5) == 'text/' && $this->srcMimeType != 'text/rtf') ||
                strpos($this->srcMimeType, 'javascript') !== false)  && (substr($this->srcFileName, -4) != '.txt')) ||
                preg_match('/\.(php|php5|php4|php3|phtml|pl|py|cgi|asp|js)$/i', $this->srcFileName))
        {
            $this->srcMimeType = 'text/plain';
            if ($this->srcExt) $this->srcName = $this->srcName . '.' . $this->srcExt;
            if ($this->newName && $this->srcExt) $this->newName = $this->newName . '.' . $this->srcExt;
            $this->srcExt = 'txt';
        }

        if (empty($this->srcMimeType)) {
            throw new Exception('no mime type found');
        } else {
            list($m1, $m2) = explode('/', $this->srcMimeType);
            $allowed = false;
            // check wether the mime type is allowed
            if (!is_array($this->allowedMimeTypes)) $this->allowedMimeTypes = array($this->allowedMimeTypes);
            foreach($this->allowedMimeTypes as $k => $v) {
                list($v1, $v2) = explode('/', $v);
                if (($v1 == '*' && $v2 == '*') || ($v1 == $m1 && ($v2 == $m2 || $v2 == '*'))) {
                    $allowed = true;
                    break;
                }
            }
            if (!$allowed) {
                throw new Exception('file type not allowed');
            }
        }

        if ($this->autoOverwrite) $this->autoRename = false;
        $this->dstFileName      = $this->srcFileName;
        $this->dstName          = $this->srcName;
        $this->dstExt           = $this->srcExt;

        //create folder structure if needed
        if (!$this->rmkdir($this->dstFolderPath, $this->dirChmod)) {
            throw new Exception('Error while creating folder structure!');
        }

        //prepare name and destination path
        $this->prepareFileName();
        $this->dstPath = $this->dstFolderPath . '/' . $this->dstFileName;

        //check overwrites
        if ($this->autoOverwrite) {
            //skip
        } else if($this->autoRename) {
            //rename if file exists
            if (@file_exists($this->dstPath)) {
                $this->findFreeName();
            }
        } else {
            //error when file exists
            if (@file_exists($this->dstPath)) {
                throw new Exception('File exists already!');
            }
        }

        // check file was uploaded
        if (!is_uploaded_file($this->srcPath)) {
            throw new Exception("source file missing");
        }

        // checks if the destination directory exists, and attempt to create it
        if (!is_dir($this->dstFolderPath)) {
            if ($this->autoCreateFolder) {
                if (!$this->rmkdir($this->dstFolderPath, $this->dirChmod)) {
                    throw new Exception("Can't create destination folders");
                }
            } else {
                throw new Exception("destination folder doesn't exist");
            }
        }

        //move uploaded file to destination
        if (@file_exists($this->srcPath)) {
            if (!move_uploaded_file($this->srcPath, $this->dstPath)) {
                throw new Exception('failed to move uploaded file to destination');
            }
        } else {
            throw new Exception('source file missing');
        }

        return $this->dstPath;
    }

    public function setDestinationPath($destPath) {
        if (!$destPath || empty($destPath)) {
            throw new Exception('Destination path must be a valid path!');
        }
        $this->dstFolderPath = rtrim($destPath);
    }
    private function prepareFileName() {
        // rename file if newName is set
        if (!is_null($this->newName)) {
            $this->dstName = $this->newName;
        }

        $this->dstName = utf8_encode(strtr(utf8_decode($this->dstName), utf8_decode('ŠŽšžŸÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÑÒÓÔÕÖØÙÚÛÜÝàáâãäåçèéêëìíîïñòóôõöøùúûüýÿ'), 'SZszYAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy'));
        $this->dstName = strtr($this->dstName, array('Þ' => 'TH', 'þ' => 'th', 'Ð' => 'DH', 'ð' => 'dh', 'ß' => 'ss', 'Œ' => 'OE', 'œ' => 'oe', 'Æ' => 'AE', 'æ' => 'ae', 'µ' => 'u'));
        $this->dstName = preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), $this->dstName);
        if (empty($this->dstName) || is_null($this->dstName)) {
            throw new Exception('destination name is not set');
        }

        // set the destination file name
        $this->dstFileName = $this->dstName . (!empty($this->dstExt) ? '.' . $this->dstExt : '');
    }
    private function detectMime() {
        $detectedMime = null;
        // checks MIME type with Fileinfo PECL extension
//        if (!$detectedMime || !is_string($detectedMime) || empty($detectedMime) || strpos($detectedMime, '/') === FALSE) {
//            if ($this->mimeFileInfo) {
//                //$this->log .= '- Checking MIME type with Fileinfo PECL extension<br />';
//                if ($this->function_enabled('finfo_open')) {
//                    $path = null;
//                    if ($this->mimeFileInfo !== '') {
//                        if ($this->mimeFileInfo === true) {
//                            if (getenv('MAGIC') === FALSE) {
//                                if (substr(PHP_OS, 0, 3) == 'WIN') {
//                                    $path = realpath(ini_get('extension_dir') . '/../') . '/extras/magic';
//                                    //$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;MAGIC path defaults to ' . $path . '<br />';
//                                }
//                            } else {
//                                $path = getenv('MAGIC');
//                                //$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;MAGIC path is set to ' . $path . ' from MAGIC variable<br />';
//                            }
//                        } else {
//                            $path = $this->mimeFileInfo;
//                            //$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;MAGIC path is set to ' . $path . '<br />';
//                        }
//                    }
//                    if ($path) {
//                        $f = @finfo_open(FILEINFO_MIME, $path);
//                    } else {
//                        //$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;MAGIC path will not be used<br />';
//                        $f = @finfo_open(FILEINFO_MIME);
//                    }
//                    if (is_resource($f)) {
//                        $mime = finfo_file($f, realpath($this->srcPath));
//                        finfo_close($f);
//                        $detectedMime = $mime;
//                        //$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;MIME type detected as ' . $detectedMime . ' by Fileinfo PECL extension<br />';
//                        if (preg_match("/^([\.\w-]+)\/([\.\w-]+)(.*)$/i", $detectedMime)) {
//                            $detectedMime = preg_replace("/^([\.\w-]+)\/([\.\w-]+)(.*)$/i", '$1/$2', $detectedMime);
//                            //$this->log .= '-&nbsp;MIME validated as ' . $detectedMime . '<br />';
//                        } else {
//                            $detectedMime = null;
//                        }
//                    } else {
//                        //$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;Fileinfo PECL extension failed (finfo_open)<br />';
//                    }
//                } elseif (@class_exists('finfo')) {
//                    $f = new finfo( FILEINFO_MIME );
//                    if ($f) {
//                        $detectedMime = $f->file(realpath($this->srcPath));
////                        ////$this->log .= '- MIME type detected as ' . $detectedMime . ' by Fileinfo PECL extension<br />';
//                        if (preg_match("/^([\.\w-]+)\/([\.\w-]+)(.*)$/i", $detectedMime)) {
//                            $detectedMime = preg_replace("/^([\.\w-]+)\/([\.\w-]+)(.*)$/i", '$1/$2', $detectedMime);
////                            ////$this->log .= '-&nbsp;MIME validated as ' . $detectedMime . '<br />';
//                        } else {
//                            $detectedMime = null;
//                        }
//                    } else {
////                        ////$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;Fileinfo PECL extension failed (finfo)<br />';
//                    }
//                } else {
////                    ////$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;Fileinfo PECL extension not available<br />';
//                }
//            } else {
////                ////$this->log .= '- Fileinfo PECL extension deactivated<br />';
//            }
//        }
        // checks MIME type with shell if unix access is authorized
        if (!$detectedMime || !is_string($detectedMime) || empty($detectedMime) || strpos($detectedMime, '/') === FALSE) {
            if ($this->mimeFile) {
                ////$this->log .= '- Checking MIME type with UNIX file() command<br />';
                if (substr(PHP_OS, 0, 3) != 'WIN') {
                    if (strlen($mime = @exec("file -bi ".escapeshellarg($this->srcPath))) != 0) {
                        $detectedMime = trim($mime);
                        //$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;MIME type detected as ' . $detectedMime . ' by UNIX file() command<br />';
                        if (preg_match("/^([\.\w-]+)\/([\.\w-]+)(.*)$/i", $detectedMime)) {
                            $detectedMime = preg_replace("/^([\.\w-]+)\/([\.\w-]+)(.*)$/i", '$1/$2', $detectedMime);
                            //$this->log .= '-&nbsp;MIME validated as ' . $detectedMime . '<br />';
                        } else {
                            $detectedMime = null;
                        }
                    } else {
                        //$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;UNIX file() command failed<br />';
                    }
                } else {
                    //$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;UNIX file() command not availabled<br />';
                }
            } else {
                //$this->log .= '- UNIX file() command is deactivated<br />';
            }
        }
        // checks MIME type with getimagesize()
        if (!$detectedMime || !is_string($detectedMime) || empty($detectedMime) || strpos($detectedMime, '/') === FALSE) {
            if ($this->mimeGetImageSize) {
                //$this->log .= '- Checking MIME type with getimagesize()<br />';
                $info = getimagesize($this->srcPath);
                if (is_array($info) && array_key_exists('mime', $info)) {
                    $detectedMime = trim($info['mime']);
                    if (empty($detectedMime)) {
                        //$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;MIME empty, guessing from type<br />';
                        $mime = (is_array($info) && array_key_exists(2, $info) ? $info[2] : null); // 1 = GIF, 2 = JPG, 3 = PNG
                        $detectedMime = ($mime==IMAGETYPE_GIF ? 'image/gif' : ($mime==IMAGETYPE_JPEG ? 'image/jpeg' : ($mime==IMAGETYPE_PNG ? 'image/png' : ($mime==IMAGETYPE_BMP ? 'image/bmp' : null))));
                    }
                    //$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;MIME type detected as ' . $detectedMime . ' by PHP getimagesize() function<br />';
                    if (preg_match("/^([\.\w-]+)\/([\.\w-]+)(.*)$/i", $detectedMime)) {
                        $detectedMime = preg_replace("/^([\.\w-]+)\/([\.\w-]+)(.*)$/i", '$1/$2', $detectedMime);
                        //$this->log .= '-&nbsp;MIME validated as ' . $detectedMime . '<br />';
                    } else {
                        $detectedMime = null;
                    }
                } else {
                    //$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;getimagesize() failed<br />';
                }
            } else {
                //$this->log .= '- getimagesize() is deactivated<br />';
            }
        }
        // default to MIME from browser (or Flash)
        if (!empty($this->srcMimeType) && !$detectedMime || !is_string($detectedMime) || empty($detectedMime)) {
            $detectedMime = $this->srcMimeType;
            //$this->log .= '- MIME type detected as ' . $detectedMime . ' by browser<br />';
            if (preg_match("/^([\.\w-]+)\/([\.\w-]+)(.*)$/i", $detectedMime)) {
                $detectedMime = preg_replace("/^([\.\w-]+)\/([\.\w-]+)(.*)$/i", '$1/$2', $detectedMime);
                //$this->log .= '-&nbsp;MIME validated as ' . $detectedMime . '<br />';
            } else {
                $detectedMime = null;
            }
        }

        if (!$detectedMime || !is_string($detectedMime) || empty($detectedMime) || strpos($detectedMime, '/') === FALSE) {
            return null;
        } else {
            return $detectedMime;
        }
    }
    private function findFreeName() {
        $this->nameCount++;
        $name = $this->dstFolderPath . '/' . $this->dstName . "($this->nameCount)." . $this->dstExt;
        if (@file_exists($name)) {
            $this->findFreeName();
        } else {
            $this->dstPath = $name;
            $this->nameCount = 1;
        }
    }
    /**
     * Creates directories recursively
     *
     * @access private
     * @param  string  $path Path to create
     * @param  integer $mode Optional permissions
     * @return boolean Success
     */
    private function rmkdir($path, $mode = 0777) {
        return is_dir($path) || ( $this->rmkdir(dirname($path), $mode) && $this->mkdir($path, $mode) );
    }
    /**
     * Creates directory
     *
     * @access private
     * @param  string  $path Path to create
     * @param  integer $mode Optional permissions
     * @return boolean Success
     */
    private function mkdir($path, $mode = 0777) {
        $old = umask(0);
        $res = @mkdir($path, $mode);
        umask($old);
        return $res;
    }
    /**
     * Returns the version of GD
     *
     * @access public
     * @param  boolean  $full Optional flag to get precise version
     * @return float GD version
     */
    private function gdVersion($full = false) {
        static $gd_version = null;
        static $gd_full_version = null;
        if ($gd_version === null) {
            $gd = gd_info();
            $gd = $gd["GD Version"];
            $regex = "/([\d\.]+)/i";
            if (preg_match($regex, $gd, $m)) {
                $gd_full_version = (string) $m[1];
                $gd_version = (float) $m[1];
            } else {
                $gd_full_version = 'none';
                $gd_version = 0;
            }
        }
        if ($full) {
            return $gd_full_version;
        } else {
            return $gd_version;
        }
    }
    /**
     * Decodes sizes
     *
     * @access private
     * @param  string  $size  Size in bytes, or shorthand byte options
     * @return integer Size in bytes
     */
    private function getSize($size) {
        if ($size === null) return null;
        $split = preg_split('#(?<=\d)(?=[a-z])#i', $size);
        $last = (isset($split[1])) ? strtolower($split[1]) : '';
        $size = (int) $split[0];
        switch($last) {
            case 'g':
                return $size * pow(1024, 3);
            case 'm':
                return $size * pow(1024, 2);
            case 'k':
                return $size * 1024;
        }
        return $size;
    }

}