<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 18.06.2017
 * Time: 18:09
 */

namespace Media\Service;


class ImageUploadService
{
    public $sizeLimit = 600;
    public $outputWidth = false;
    public $outputHeight = false;

    private $srcWidth;
    private $srcHeight;
    private $newHeight;
    private $newWidth;
    /** @var  bool returns whether given image is animated or not */
    private $isAnimated;

    /**
     * @param array[ <br/>
     * ['name' => 'sizeLimit',    'value' => 'value'],  // longer side under 'value' => no action is taken  <br/>
     * ['name' => 'outputWidth',  'value' => 'value'],  // value in px <br/>
     * ['name' => 'outputHeight', 'value' => 'value'],  // value in px <br/>
     * ['name' => 'name', 'value' => 'value'] <br/>
     * ] $options
     */
    public function setOptions(Array $options)
    {
        foreach ($options as $option){
            $this->$option['name'] = $option['value'];
        }
    }

    public function createThumb($imagePath)
    {
        $this->isAnimated = $this->isAnimated($imagePath);
        if ($this->isAnimated($imagePath)){
            $this->createAnimatedImage($imagePath);
        }
        else {
            $this->createImage($imagePath);
        }
    }

    private function createImage($imagePath)
    {
        $img = file_get_contents($imagePath);
        $im = imagecreatefromstring($img);

        ImageAlphaBlending($im, true);

        $this->srcWidth = imagesx($im);
        $this->srcHeight = imagesy($im);

        if ($this->srcWidth < $this->sizeLimit && $this->srcHeight < $this->sizeLimit) {
            // no change
        }
        else {
            $this->setNewSize();

            $srcInfo = pathinfo($imagePath);
            $newImage = imagecreatetruecolor($this->newWidth, $this->newHeight);
            $transparent = imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 255, 255, 255, 127));
            imagefill($newImage, 0, 0, $transparent);

            imagecopyresized($newImage, $im, 0, 0, 0, 0, $this->newWidth, $this->newHeight, $this->srcWidth, $this->srcHeight);
            switch($srcInfo['extension']) {
                case 'jpg':
                case 'jpeg':
                    imagejpeg($newImage, $imagePath);
                    break;
                case 'png':
                    imagepng($newImage, $imagePath);
                    break;
                case 'gif':
                    imagegif($newImage, $imagePath);

            }
            imagedestroy($newImage);
            imagedestroy($im);
        }
    }

    private function setNewSize()
    {
        if (!$this->outputHeight && !$this->outputWidth){
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

    private function isLandscape()
    {
        if ($this->srcWidth > $this->srcHeight) return true;
        return false;

    }

    /**
     * check if Image is animated or not
     * @param $filename
     * @return bool
     */
    private function isAnimated($filename) {
        if(!($fh = @fopen($filename, 'rb')))
            return false;
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
        return true;
    }

    private function createAnimatedImage($imagePath)
    {
        // @todo
    }
}