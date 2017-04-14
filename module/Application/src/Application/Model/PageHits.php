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


class PageHits extends AbstractTableGateway
{
//  fry        table: pageHits (id, url, lastActionTime, count )     id = primary , (url,day) = unique

    public $table = 'page_hits';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }
    public function countHit($url, $now)
    {
        $query = "INSERT INTO $this->table (url, last_action_time) VALUES ('$url', $now) 
                      ON DUPLICATE KEY UPDATE counter = counter + 1;";
        var_dump($query);
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
        $query = "SELECT * FROM $this->table WHERE last_action_time < $since AND url = '$url';";
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





//  @salt     //Adds one to the counter
//  @salt
//  @salt     mysql_query("UPDATE counter SET counter = counter + 1");
//  @salt
//  @salt     //Retrieves the current count
//  @salt
//  @salt     $count = mysql_fetch_row(mysql_query("SELECT counter FROM counter"));
//  @salt
//  @salt     //Displays the count on your site
//  @salt
//  @salt     print "$count[0]";
//UPDATE yourtable
//SET url = REPLACE(url, 'http://domain1.com/images/', 'http://domain2.com/otherfolder/') AND counter = counter + 1
//WHERE url LIKE ('http://domain1.com/images/%');

}