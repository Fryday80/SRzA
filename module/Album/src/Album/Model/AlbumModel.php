<?php
namespace Album\Model;


use Media\Service\MediaItem;
use Media\Service\MediaService;

class AlbumModel implements \Iterator, \Countable
{
    /* @var MediaService $mediaService */
    private $mediaService;
    private $path = "";
    /** @var array */
    private $images = [];
    private $position = 0;
    /* @var MediaItem $previewItem */
    private $previewItem;
    private $options = [
        'Album' => [
            'name' => 'New Album',
            'description'   => '',
            'preview'       => '',
            'date'         => '',
            'dateTo'         => '',
        ]
    ];

    public function __construct($path, $mediaService, $options = null) {
        $this->position = 0;
        $this->path = $path;
        $this->mediaService = $mediaService;
        if ($options == null) {
            // @todo check if necessary options are present
            return null;
            $options = $this->mediaService->getFolderMeta($path);
        }
        $this->options = array_replace_recursive($this->options, $options);//ja is einfach ohne check und passt auch
    }
    public function loadImages() {
        $items = $this->mediaService->getItems($this->path);
        $result = [];
        foreach ($items as $key => $value) {
            if ($value->readable == 0) continue;
            if ($value->type != 'folder' && $value->type != 'conf') {
                array_push($result, $value);
            }
        }
        $this->images = $result;
    }
    public function getName() {
        return $this->options['Album']['name'];
    }
    public function getDescription() {
        return $this->options['Album']['description'];
    }
    public function getDate() {
        $return = array();
        if ($this->options['Album']['date'] !== ''){
            $return['date']   = $this->options['Album']['date'];
            $return['oneDay'] = true;
        }
        if($this->options['Album']['dateTo'] !==''){
            $return['oneDay'] = false;
            $return['dateTo'] = $this->options['Album']['dateTo'];
        }
        return $return;
    }
    public function getAllImages() {
        return $this->images;
    }
    public function getRandomImage() {
        if (count($this->images) == 0) {
            return null;
        }
        $randomIndex = rand(0, count($this->images) -1);
        return $this->images[$randomIndex];
    }
    public function getImageByName($name) {
        foreach ($this->images as $key => $value) {
            if ($value->name.'.'.$value->type == $name) {
                return $value;
            }
        }
        return null;
    }

    /**
     * @return MediaItem|null
     */
    public function getPreviewItem() {
        if ($this->previewItem == null) {
            $this->previewItem = $this->getImageByName($this->options['Album']['preview']);
        }
        return $this->previewItem;
    }
    public function getPreviewUrl() {
        return (!$this->getPreviewItem())? '/img/favicon.png': $this->previewItem->livePath;
    }
    public function getPath() {
        return $this->path;
    }

    ///////////////// iterator interface /////////////////////////////
    function rewind() {
        $this->position = 0;
    }

    function current() {
        return $this->images[$this->position];
    }

    function key() {
        return $this->position;
    }

    function next() {
        ++$this->position;
    }

    function valid() {
        return isset($this->images[$this->position]);
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->images);
    }
}
