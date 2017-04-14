<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 13.04.2017
 * Time: 02:24
 */

namespace Application\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;


class PageHitsTable extends AbstractTableGateway
{
//  fry        table: pageHits (id, url, lastActionTime, count )     id = primary , url = unique

    public $table = 'page_hits';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }
    public function countHit($url, $now)
    {
        $query = "INSERT INTO $this->table (url, time) VALUES ('$url', $now) 
                      ON DUPLICATE KEY UPDATE counter = counter + 1;";
        $this->adapter->query($query, array());
    }

    /**
     * @param string $url
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     * @throws \Exception
     */
    public function getByUrl( $url )
    {
        $url = $this->getRelativeURL($url);
        return $this->getWhere(array('url' => $url));
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
        return $this->adapter->query($query, array());
    }

    private function getWhere($where = array(), $columns = array())
    {
        try {
            $sql = $this->getSql();
            $select = $sql->select();

            if (count($where) > 0) {
                $select->where($where);
            }
            if (count($columns) > 0) {
                $select->columns($columns);
            }
//            $select->join(array(
//                'parent' => $this->table
//            ),
//                'parent.rid = role.role_parent', array('role_parent_name' => 'role_name'), 'left'
//            );

            $results = $this->selectWith($select);
            return $results;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    private function getRelativeURL($url)
    {
        $relativeUrl = str_replace(array("http://", $_SERVER['HTTP_HOST']),"",$url);
        return $relativeUrl;
    }
}