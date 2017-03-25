<?php
namespace Album\Model;


use Media\Service\MediaItem;
use Media\Service\MediaService;

class AlbumModel implements \Iterator
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
            'description' => '',
            'preview' => '',
            'member-only' => false
        ],
        "Restrictions" => [
            "all" => false
        ]
    ];

    public function __construct($path, $mediaService, $options = null) {
        $this->position = 0;
        $this->path = $path;
        $this->mediaService = $mediaService;
        if ($options == null) {
            //@todo check if nessesery options are present
            $fileName = '/album.conf';
            $options = $this->mediaService->parseIniFile($path.$fileName, TRUE);
        }
        $this->options = array_replace_recursive($this->options, $options);
    }
    public function loadImages() {
        $items = $this->mediaService->getItems($this->path);
        $result = [];
        foreach ($items as $key => $value) {
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
    public function getRandomItem() {
        if (count($this->images) == 0) {
            return null;
        }
        $randomIndex = rand(0, count($this->images) -1);
        return $this->images[$randomIndex];
    }
    public function getItemByName($name) {
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
            $this->previewItem = $this->getItemByName($this->options['Album']['preview']);
        }
        return $this->previewItem;
    }
    public function getPreviewUrl() {
        return (!$this->getPreviewItem())? '/img/favicon.png': $this->previewItem->livePath;
    }
    public function getPath() {
        return $this->path;
    }
    public function isMemberOnly() {
        return $this->options['Album']['member-only'];
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

}
