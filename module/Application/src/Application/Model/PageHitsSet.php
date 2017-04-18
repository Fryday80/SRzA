<?php
namespace Application\Model;


class PageHitsSet
    extends BasicStatDataSet
{
    private $pageHitsSet = array();
    private $allPageHits = 0;
    private $hashUrlPage = array();

    public function updatePageHit($url, $data = null)
    {
        $rUrl = $this->getRelativeURL($url);
        $key = array_search($rUrl, $this->hashUrlPage);
        if ($key) $this->update($key, $data);
        else $this->create($rUrl, $data);
        $this->allPageHits++;
    }

    /**
     * @param int $since UNIX timestamp
     * @return array array of Page objects
     */
    public function getAllPagesData($since = 0)
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
        $key = array_search($rUrl, $this->hashUrlPage);
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

    /**** PRIVATE METHODS ****/
    private function update($key, $data = null)
    {
        $this->pageHitsSet[$key]->update(time(), $this->getUserId(), $data);
    }

    private function create($url, $data = null)
    {
        $nextKey = count($this->hashUrlPage);
        $this->hashUrlPage[$nextKey] = $url;
        $this->pageHitsSet[$nextKey] = new Page($url, time(), $this->getUserId(), $data);
    }

    private function getRelativeURL($url)
    {
        $relativeUrl = str_replace(array("http://", $_SERVER['HTTP_HOST']),"",$url);
        return $relativeUrl;
    }
}