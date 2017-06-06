<?php
namespace Gallery\Model;


use Media\Service\MediaItem;
use Media\Service\MediaService;
use Tracy\Debugger;

class AlbumModel implements \Iterator, \Countable
{
    /* @var MediaService $mediaService */
    private $mediaService;
    private $path = "";
    /** @var array */
    public $images = [];
    private $position = 0;
    /* @var MediaItem $previewItem */
    private $previewItem;
    private $options = [
        'Album' => [
            'name' => 'New Album',
            'description'   => '',
            'preview'       => '',
            'date'          => '',
            'dateTo'        => '',
            'year'          => ''
        ]
    ];

    public function __construct($path, $options = []) {
        $this->position = 0;
        $this->path = $path;
//        $this->mediaService = $mediaService;
//        if ($options == null) {
//            // @todo check if necessary options are present
//            $options = $this->mediaService->getFolderMeta($path);
//        }
        $this->options = array_replace_recursive($this->options, $options);
        $this->fixDate();
    }
//    public function loadImages() {
//        $items = $this->mediaService->getItems($this->path);
//        $result = [];
//        foreach ($items as $key => $value) {
//            if ($value->readable == 0) continue;
//            if ($value->type != 'folder' && $value->type != 'conf') {
//                array_push($result, $value);
//            }
//        }
//        $this->images = $result;
//    }
    public function getName() {
        return $this->options['Album']['name'];
    }
    public function getDescription() {
        return $this->options['Album']['description'];
    }
    public function getDate() {
        $return = array();
        $return['date'] = $this->options['Album']['date'];
        $return['dateString']   = $this->options['Album']['date'];
        $return['oneDay'] = true;
        if($this->options['Album']['dateTo'] !==''){
            $return['oneDay'] = false;
            $return['dateTo'] = $this->options['Album']['dateTo'];
            $return['dateString'] = $return['date']. ' - ' . $return['dateTo'];
        }
        return $return;
    }
    public function getYear(){
        return $this->options['Album']['year'];
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

    /**
     * validates the dates (date, dateTo) to [dd.mm.yyyy]
     * sets the year [yyyy]
     */
    private function fixDate(){
        $replace = array (',', '-', '/');
        $this->options['Album']['date'] = str_replace($replace, '.', $this->options['Album']['date']);
        $this->options['Album']['dateTo'] = str_replace($replace, '.', $this->options['Album']['dateTo']);
        $this->options['Album']['year'] = $this->refactorYear($this->options['Album']['date']);

        $dateCheck = array ('date','dateTo');
        foreach ($dateCheck as $value) {
            if ($this->options['Album'][$value] !== '' && $this->checkYear($this->options['Album'][$value])) {
                $parts = explode('.', $this->options['Album'][$value]);
                $last = count($parts) - 1;
                $replacement = '';
                for ($i = 0; $i < $last; $i++) {
                    $replacement .= $parts[$i] . '.';
                }
                $replacement .= $this->refactorYear($this->options['Album'][$value]);
                $this->options['Album'][$value] = $replacement;
            }
        }
    }

    /**
     * @param string $date
     * @return string year "yyyy"
     */
    private function refactorYear($date){
        $parts = explode('.', $date );
        $last = count($parts)-1;
        $year = $parts[$last];
        // if no entry
        if ( $year == '' ) {
            $year = date('Y');
        }
        // change short yy -> yyyy
        $year = (strlen($year) == 4) ? $year : (strlen($year) == 2) ? substr(date('Y'), 0, 2).$year : $year;
        return $year;
    }

    /**
     * @param string $date
     * @return bool true if given date has format "yy" else false
     */
    private function checkYear($date){
        $parts = explode('.', $date);
        $i = count($parts) -1;
        $refYear = $this->refactorYear($date);
        return ( $parts[$i] !== $refYear ) ?: false;
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
