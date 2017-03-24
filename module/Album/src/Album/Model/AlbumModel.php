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
            $options = $this->mediaService->parseIniFile($path.$fileName, TRUE, INI_SCANNER_TYPED);
        }
        $this->options = array_replace_recursive($this->options, $options);
    }
    public function loadImages() {
        $this->images = $this->mediaService->getItems($this->path);
    }
    public function getName() {
        return $this->options['Album']['name'];
    }
    public function getDescription() {
        return $this->options['Album']['description'];
    }
    public function getPreview() {
        return $this->options['Album']['preview'];
    }
    public function getPreviewUrl() {
        return $this->options['Album']['preview'];
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
