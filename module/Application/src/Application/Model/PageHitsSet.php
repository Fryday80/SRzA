<?php
namespace Application\Model;


use Application\Model\BasicModels\StatDataSetBasic;

class PageHitsSet
    extends StatDataSetBasic
{
    private $allPageHits = 0;
    private $hashKeyUrl = array();
    
    public function updateItem($data)
    {
        $this->allPageHits++;
        if ($this->newBuild) {
            $this->create( $data );
            bdump('init catch');
            return true;
        }
        $hash = $this->getHashTableByKey('url');
        bdump($hash);
        $key = (key_exists($data['url'], $hash)) ? $hash[$data['url']] : null;
        if ($key !== null) $this->update($key, $data);
        else $this->create($data);
        bdump($this->data);
        return true;
    }

    public function toArray($since = 0){
        if ($since == 0) parent::toArray();
        return $this->getSince($since);
    }
    public function getByUrl($url)
    {
        $res = array();
        $hash = $this->getHashTableByKey($url);
        return $this->data[$hash[0]];
    }

    public function getHitsByUrl($url)
    {
        return $this->getByUrl($url)->count;
    }

    public function getAllHits()
    {
        return $this->allPageHits;
    }

    /**
     * @param int $top e.g. 10 for Top Ten
     * @return array|bool false if failure, array on success
     */
    public function getMostVisitedPages($top = 1){
        $result = array();
        $list = array();
        $count = 0;
        /** @var  $item Page*/
        foreach ($this->data as $item){
            if (!isset($list[$item->count])) $list[$item->count] = array();
            array_push($list[$item->count], $item->url);
        }
        krsort($list);
        foreach($list as $hits => $item)
            foreach ($item as $url){
                if($count == (int)$top) return $result;
                array_push($result, array('hits' => $hits, 'url' => $url));
                $count++;
            }
        if ((count($result) < $top) && (count($result) >= 1))return $result;
        return false;
    }

    /**** PRIVATE METHODS ****/
    private function update($key, $data)
    {
        $this->data[(int)$key]->update($data['time'], $data['userId'], $data['data']);
    }
    protected function create($data)
    {
        $new = $this->creator($data);
        if ($new !== null){
            $this->data[$this->nextId] = $new;
            $this->setHashOfNewItem($this->nextId);
            $this->nextId++;
            $this->newBuild = false;
        }
    }
    protected function creator($createData)
    {
        if (!$createData) return NULL;
        $url = $time = $userId = false;
        $itemData = null;
        foreach ($createData as $key => $value)
            $$key = $value;
        if (!($url && $time && $userId))return null;
        return new Page($this->nextId, $url, $time, $userId, $itemData);
    }
    
    private function getRelativeURL($url)
    {
        $relativeUrl = str_replace(array("http://", $_SERVER['HTTP_HOST']),"",$url);
        return $relativeUrl;
    }
}