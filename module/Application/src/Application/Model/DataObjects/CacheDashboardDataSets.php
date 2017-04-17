<?php

namespace Application\Model\DataObjects;


use Zend\Db\ResultSet\ResultSet;

class CacheDashboardDataSets extends BasicDashboardDataSets
{
    protected $cacheFile;
    protected $decodeColumns = array ('data');

    function __construct($data = null)
    {
        if ($data !== null){
            $this->data = (($data instanceof ResultSet)) ? $data->toArray() : $data;
            $this->sortByKey('time');
            foreach ($this->decodeColumns as $dec_Col) $this->decode($dec_Col);
        }
    }
    public function toArray($since = null)
    {
        if ($since == null) return $this->getAllResults();
        return $this->getResultSince($since);
    }

    protected function decode($dec_Col)
    {
        foreach ($this->data as $row => $column) $this->data[$row][$dec_Col] = json_decode($row[$dec_Col]);
    }

    protected function sortByKey($key, $order = 'DESC')
    {
        $sort = array();
        foreach ( $this->data as $row => $column){
            if (!key_exists($key, $column)) return false;
            $sort[$row] = $row[$key];
        }
        if ($order == 'DESC') array_multisort($sort, SORT_DESC, $this->data);
        else array_multisort($sort, $this->data);
        return true;
    }
}