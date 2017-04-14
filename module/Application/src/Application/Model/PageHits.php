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

    public $table = '';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }
//  @todo     //Adds one to the counter
//  @todo
//  @todo     mysql_query("UPDATE counter SET counter = counter + 1");
//  @todo
//  @todo     //Retrieves the current count
//  @todo
//  @todo     $count = mysql_fetch_row(mysql_query("SELECT counter FROM counter"));
//  @todo
//  @todo     //Displays the count on your site
//  @todo
//  @todo     print "$count[0]";

}

//
//          tabelle 2: pageHits (id, url, lastActionTime, count)