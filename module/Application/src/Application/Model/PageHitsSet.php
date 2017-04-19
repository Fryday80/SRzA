<?php
namespace Application\Model;


class PageHitsSet
    extends BasicStatDataSet
{
    private $pageHitsSet = array();
    private $allPageHits = 0;
    private $hashKeyUrl = array();

    public function updatePageHit($url, $data = null)
    {
        $rUrl = $this->getRelativeURL($url);
        $key = array_search($rUrl, $this->hashKeyUrl);
        if ($key !== false) $this->update($key, $data);
        else $this->create($rUrl, $data);
        $this->allPageHits++;
    }

    /**
     * @param int $since UNIX timestamp
     * @return array array of Page objects
     */
    public function toArray($since = 0)
    {
        if((int)$since == 0) return $this->pageHitsSet;
        $result = array();
        foreach ($this->pageHitsSet as $key => $item)
        {
            if($item->time > $since) array_push($result, $item);
        }
        return $result;
    }

    public function getByUrl($url)
    {
        $rUrl = $this->getRelativeURL($url);
        $key = array_search($rUrl, $this->hashKeyUrl);
        return $this->pageHitsSet[$key];
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
        foreach ($this->pageHitsSet as $item){
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
    private function update($key, $data = null)
    {
        $this->pageHitsSet[$key]->update(time(), $this->userId(), $data);
    }

    private function create($url, $data = null)
    {
        $nextKey = count($this->hashKeyUrl);
        $this->hashKeyUrl[$nextKey] = $url;
        $this->pageHitsSet[$nextKey] = new Page($url, time(), $this->userId(), $data);
    }

    private function getRelativeURL($url)
    {
        $relativeUrl = str_replace(array("http://", $_SERVER['HTTP_HOST']),"",$url);
        return $relativeUrl;
    }
}