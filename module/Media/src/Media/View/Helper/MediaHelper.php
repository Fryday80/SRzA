<?php
	/**
	 * Created by IntelliJ IDEA.
	 * User: Fry
	 * Date: 03.09.2017
	 * Time: 22:17
	 */

	namespace Media\View\Helper;


	use const Media\Service\LIVE_PATH;
	use Media\Service\MediaException;
	use Media\Model\MediaItem;
	use Media\Service\MediaService;
	use Zend\Form\View\Helper\AbstractHelper;

	class MediaHelper extends AbstractHelper
	{
		public $mediaService;

		public function __construct(MediaService $mediaService)
		{
			$this->mediaService = $mediaService;
		}

		public function getImageUrl($dataPath)
		{
			$item = $this->getItem($dataPath);

			if ($item && $item->type == 'image') return $item->livePath;
			else return null;
		}

		public function getThumbsUrl($dataPath, $size = 'big')
		{
			$item = $this->getItem($dataPath);

			if ($item && $item->type == 'image') {
				$thumbName = $item->name;
				$thumbName .= ($size == 'big') ? '_thumb_big' : '_thumb_small';
				$thumbName .= '.' . $item->extension;

				$livePath = LIVE_PATH . '_thumbs/' . $item->path . $thumbName;

				return $livePath;
			}
			else return null;
		}

		/**
		 * @param $path
		 *
		 * @return MediaItem|null
		 */
		private function getItem($path)
		{
			$item = $this->mediaService->getItem($path);

			if ($item instanceof MediaException)
				return null;
			return $item;
		}
	}