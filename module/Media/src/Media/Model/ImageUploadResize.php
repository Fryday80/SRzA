<?php
namespace Media\Service;

class ImageUploadResize
{
    public $sizeLimit = 600;
    public $outputWidth = false;
    public $outputHeight = false;
    public $destinationPath;

    private $imagePath;
    private $img;
    private $srcWidth;
    private $srcHeight;
    private $newHeight;
    private $newWidth;

    private $options;

    /**
     * @param $options array optional array ( <br/>
     * 'sizeLimit'      => 'value',  // value in px; default 300; longer side under 'value' => no action is taken  <br/>
     *'outputWidth'     => 'value',  // value in px <br/>
     *'outputHeight'    => 'value',  // value in px <br/>
     *'destinationPath' => 'value'   // if not set, overwrites src file <br/>
     * )
     */
    function __construct(Array $options = array())
    {
        if (!empty($options)) $this->setOptions($options);
    }

    /**
     * Set resize options
     *
     * @param $options array array ( <br/>
     * 'sizeLimit'      => 'value',  // value in px; default 300; longer side under 'value' => no action is taken  <br/>
     *'outputWidth'     => 'value',  // value in px <br/>
     *'outputHeight'    => 'value',  // value in px <br/>
     *'destinationPath' => 'value'   // if not set, overwrites src file <br/>
     * )
     */
    public function setOptions(Array $options)
    {
        $this->options = $options;
    }

    public function createImage($imagePath)
    {
        $this->prepare($imagePath);
        $this->getImageAndData();
        if (!$this->exitStrategy()) {
            if ($this->isAnimated())
                $this->createAnimatedImage();
            else
                $this->createResizedImage();
        }
    }

    private function createResizedImage()
    {
        imagealphablending($this->img, true);
        $this->setNewSize();

        $srcInfo = pathinfo($this->imagePath);
        $newImage = imagecreatetruecolor($this->newWidth, $this->newHeight);
        $transparent = imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 255, 255, 255, 127));
        imagefill($newImage, 0, 0, $transparent);

        imagecopyresized($newImage, $this->img, 0, 0, 0, 0, $this->newWidth, $this->newHeight, $this->srcWidth, $this->srcHeight);
        switch($srcInfo['extension']) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($newImage, $this->destinationPath);
                break;
            case 'png':
                imagepng($newImage, $this->destinationPath);
                break;
            case 'gif':
                imagegif($newImage, $this->destinationPath);

        }
        imagedestroy($newImage);
        imagedestroy($this->img);
    }

    private function createAnimatedImage()
    {
        $this->setNewSize();
        $newAnimatedGif = new GIF_eXG($this->imagePath, 0);
        $newAnimatedGif->resize($this->destinationPath, $this->newWidth, $this->newHeight, 1, 0);
    }

    private function prepare($imagePath){
        $this->reset();
        $this->imagePath = $imagePath;
        foreach ($this->options as $option){
            $this->$option['name'] = $option['value'];
        }
        $this->destinationPath = (isset($this->destinationPath)) ? $this->destinationPath : $this->imagePath;
    }

    private function getImageAndData()
    {
        $image = file_get_contents($this->imagePath);
        $this->img = imagecreatefromstring($image);

        $this->srcWidth = imagesx($this->img);
        $this->srcHeight = imagesy($this->img);
    }

    private function exitStrategy()
    {
        if ($this->srcWidth < $this->sizeLimit && $this->srcHeight < $this->sizeLimit) {
            return true;
        }
        return false;
    }

    private function isLandscape()
    {
        if(!isset($this->srcWidth) || !isset($this->srcHeight)) {
            $this->getImageData($this->getImage());
        }

        if ($this->srcWidth > $this->srcHeight) return true;
        return false;

    }

    /**
     * check if given image is animated or not
     * @param $imagePath
     * @return bool
     */
    private function isAnimated() {
        if(!($fh = @fopen($this->imagePath, 'rb'))){
            bdump('test isAnimated');
            bdump('true');
            return false;
        }
        $count = 0;
        //an animated gif contains multiple "frames", with each frame having a
        //header made up of:
        // * a static 4-byte sequence (\x00\x21\xF9\x04)
        // * 4 variable bytes
        // * a static 2-byte sequence (\x00\x2C)

        // We read through the file til we reach the end of the file, or we've found
        // at least 2 frame headers
        while(!feof($fh) && $count < 2) {
            $chunk = fread($fh, 1024 * 100); //read 100kb at a time
            $count += preg_match_all('#\x00\x21\xF9\x04.{4}\x00[\x2C\x21]#s', $chunk, $matches);
        }

        fclose($fh);
//        return $count > 1;
        bdump('test isAnimated');
        bdump('true');
        return true;
    }

    private function setNewSize()
    {
        if(!isset($this->srcWidth) || !isset($this->srcHeight)) {
            $this->getImageData($this->getImage());
        }

        if (!$this->outputHeight && !$this->outputWidth) {
            if ($this->isLandscape()){
                $this->newWidth = $this->sizeLimit;
                $this->newHeight = $this->newWidth * $this->srcHeight / $this->srcWidth;
            }
            else {
                $this->newHeight = $this->sizeLimit;
                $this->newWidth = $this->newHeight * $this->srcWidth / $this->srcHeight;
            }
        }
        elseif ($this->outputHeight && !$this->outputWidth) {
            $this->newHeight = $this->outputHeight;
            $this->newWidth = $this->newHeight * $this->srcWidth / $this->srcHeight;
        }
        elseif (!$this->outputHeight && $this->outputWidth) {
            $this->newWidth = $this->outputWidth;
            $this->newHeight = $this->newWidth * $this->srcHeight / $this->srcWidth;
        }
        else {
            $this->newWidth = $this->outputWidth;
            $this->newHeight = $this->outputHeight;
        }
    }

    private function reset()
    {
        if (isset($this->imagePath)){
            $this->sizeLimit = 600;
            $this->outputWidth = false;
            $this->outputHeight = false;
            unset($this->destinationPath);
            unset($this->imagePath);
            unset($this->srcWidth);
            unset($this->srcHeight);
            unset($this->newHeight);
            unset($this->newWidth);
        }
    }
}