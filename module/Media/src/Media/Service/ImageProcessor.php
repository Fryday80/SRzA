<?php

namespace Media\Service;


class ImageProcessor
{
	private $config;

	private $srcImage = null;
	private $srcPath;
	private $srcInfo;
	private $srcWidth;
	private $srcHeight;

	private $newImage;

	public function __construct($config)
	{
		$this->config = $config;
	}



	public function createThumb($item, $targetPath = null)
	{
		if($item instanceof MediaItem)
			$this->loadMediaItem($item);
		else
			$this->loadPath($item);
		$width = $this->config['Media_ImageProcessor']['thumbs']['x1'];
		$height = $this->config['Media_ImageProcessor']['thumbs']['y1'];
		$this->intern_resize_crop($width, $height);
		$this->intern_save($targetPath);
		$width = $this->config['Media_ImageProcessor']['thumbs']['x2'];
		$height = $this->config['Media_ImageProcessor']['thumbs']['y2'];
		$this->intern_resize_crop($width, $height);
		$this->intern_save($targetPath);
		$this->intern_end();
	}

	public function createBlazon($item, $targetPath = null)
	{
		// enhancement when/if BlazonModel was created
//		if($item instanceof BlazonModel)
//			$this->loadMediaItem($item);
//		else
			$this->loadPath($item);
		$width  = $this->config['Cast_ImageProcessor']['blazon']['x'];
		$height = $this->config['Cast_ImageProcessor']['blazon']['y'];
		$this->intern_resize_crop($width, $height);
		$this->intern_save($targetPath);
		$this->intern_end();
	}




	public function loadPath($imagePath)
	{
		$this->srcPath = $imagePath;
		$this->intern_load();
	}

	public function loadMediaItem(MediaItem $item)
	{
		$this->loadPath($item->fullPath);
	}

	public function resize($newWidth, $newHeight = null, $keepRatio = true) {
		$this->intern_resize($newWidth, $newHeight, $keepRatio);
	}

	public function resize_crop($newWidth, $newHeight) {
		$this->intern_resize_crop($newWidth, $newHeight);
	}

	public function saveImage($targetPath = null) {
		$this->intern_save($targetPath);
		$this->intern_end();
	}


	private function intern_load()
	{
		$srcImg = file_get_contents($this->srcPath);
		$this->srcImage  = imagecreatefromstring($srcImg);
		$this->srcWidth  = imagesx($this->srcImage);
		$this->srcHeight = imagesy($this->srcImage);

		$this->srcInfo = pathinfo($this->srcPath);
	}

	private function intern_save ($targetPath = null)
	{
		if ($targetPath == null) $targetPath = $this->srcPath;

		switch($this->srcInfo['extension']) {
			case 'jpg':
			case 'jpeg':
				imagejpeg ( $this->newImage, $targetPath);
				break;
			case 'png':
				imagepng  ( $this->newImage, $targetPath);
				break;
			case 'gif':
				imagegif  ( $this->newImage, $targetPath);
		}
	}

	private function intern_resize($newWidth, $newHeight = null, $keepRatio = true) {
		if ($newHeight == null)
			$this->newImage = imagescale($this->srcPath, $newWidth);
		else {
			if ($keepRatio !== true)
				$this->newImage = imagescale($this->srcPath, $newWidth, $newHeight);
			else
			{
				ImageAlphaBlending($this->srcImage, true);

				$startWidth = $startHeight = 0;
				if ($this->srcWidth > $this->srcHeight) {
					$startHeight = ($newHeight - $this->srcHeight) / 2;
				} else {
					$startWidth = ($newWidth - $this->srcWidth) / 2;
				}
				$this->newImage = imagecreatetruecolor($newWidth, $newHeight);
				$transparent = imagecolortransparent($this->newImage, imagecolorallocatealpha($this->newImage, 255, 255, 255, 127));
				imagefill($this->newImage, 0, 0, $transparent);

				imagecopyresized($this->newImage, $this->srcImage, $startWidth, $startHeight, 0, 0, $newWidth, $newHeight, $this->srcWidth, $this->srcHeight);
			}
		}
	}

	private function intern_resize_crop($newWidth, $newHeight) {
		$startWidth = $startHeight = 0;
		if ($newWidth < $newHeight) {
			$resized = imagescale($this->srcPath, $newWidth);
			$y = imagesy($resized);
			$startHeight = ($y - $newHeight)/2;
		} else {
			$scale = $this->srcWidth/$this->srcHeight;
			$resized = imagescale($this->srcPath, ($newHeight*$scale));
			$x = imagesx($resized);
			$startWidth = ($x - $newWidth)/2;
		}
		$this->newImage = imagecreatetruecolor($newWidth, $newHeight);

		imagecopyresized($this->newImage, $resized, 0,0, $startWidth, $startHeight, $newWidth, $newHeight, $this->srcWidth, $this->srcHeight);
	}

	private function intern_end()
	{
		imagedestroy($this->newImage);
		imagedestroy($this->srcImage);
		$this->newImage = null;
		$this->srcImage = null;
	}
}