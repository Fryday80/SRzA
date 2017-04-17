<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 17.04.2017
 * Time: 13:53
 */

namespace Application\Model\DataObjects;

use Application\Model\DataObjects\DBDashboardDataSets;

class PageHitsSet extends DBDashboardDataSets{

    /**
     * @param string $url
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     * @throws \Exception
     */
    public function getByUrl( $url )
    {
        $url = $this->getRelativeURL($url);
        return $this->objectReturn($this->getWhere(array('url' => $url)));
    }

    /**
     * @param int $since timestamp
     * @param string $url
     * @return \Zend\Db\Adapter\Driver\StatementInterface|\Zend\Db\ResultSet\ResultSet
     */
    public function getSinceByURL( $since, $url)
    {
        $url = $this->getRelativeURL($url);
        $query = "SELECT * FROM $this->table WHERE time < $since AND url = '$url';";
        return $this->objectReturn($this->adapter->query($query, array()));
    }

    private function getRelativeURL($url)
    {
        $relativeUrl = str_replace(array("http://", $_SERVER['HTTP_HOST']),"",$url);
        return $relativeUrl;
    }
}