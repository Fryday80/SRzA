<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 13.04.2017
 * Time: 02:24
 */

namespace Application\Model\DashboardTables;

use Application\Model\DashboardTables\DashboardTablesBasic;
use Application\Model\DataObjects\PageHitsSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;


class PageHitsTable extends DashboardTablesBasic
{
//  fry        table: pageHits (id, url, lastActionTime, count )     id = primary , url = unique

    public $table;

    public function __construct(Adapter $adapter)
    {
        if($this->configLoad) parent::__construct($adapter);
        else {
            $this->table = 'page_hits';
            $this->adapter = $adapter;
            $this->initialize();
        }
    }

    public function countHit($url, $now)
    {
        $query = "INSERT INTO $this->table (url, time) VALUES ('$url', $now) 
                      ON DUPLICATE KEY UPDATE counter = counter + 1;";
        $this->adapter->query($query, array());
    }


    public function getPageHits()
    {
        return new PageHitsSet($this->getWhere());
    }
}