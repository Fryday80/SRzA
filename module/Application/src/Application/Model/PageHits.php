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
//  fry        table: pageHits (id, url, lastActionTime, count, day )     id = primary , (url,day) = unique

    public $table = 'page_hits';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }
    public function countHit($url, $now) {
        $day = date('d.m.Y', $now);
        $queryItems = "url, lastActionTime, count, day";
        $queryValues = "'$url', $now, count+1, '$day'";
        $query = "REPLACE INTO $this->table ($queryItems) VALUES ($queryValues);";
        bdump($query);
        $this->adapter->query($query, array());
    }

    /** Prepare data for query
     *
     * @param array $data
     * @return array|null [0] = sql columns line up, [1] = the fitting sql VALUES
     */
    private function prepareData($data)
    {
        $queryItems ='';
        $queryValues = '';

        //create SQL items and values line up
        foreach ($data as $key => $value){
            $queryItems .= $key . ", ";

            if ($key == 'action_data'){
                $value = serialize($value);
            }
            if (is_int($value)) {
                $queryValues .= $value. ", ";
            } else {
                $queryValues .= "'$value', ";
            }
        }

        $queryItems = substr($queryItems, 0, -2);
        $queryValues = substr($queryValues, 0, -2);

        return array($queryItems, $queryValues);
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