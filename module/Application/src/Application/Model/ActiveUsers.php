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

class ActiveUsers extends AbstractTableGateway
{

    public $table = '';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }
    public function fetchBySID($sid){
        $row = $this->select(array('sid' => (int) $sid));
        if (!$row)
            return false;

        return $row->toArray()[0];
    }
    public function add($data){
        if (!$this->insert(array('sid' => $data['sid'])))
            return false;
        return $this->getLastInsertValue();
    }

    public function save($sid, $data) {
        if (!$this->update(array('sid' => $data['sid']), array('sid' => (int)$sid)))
            return false;
        return $sid;
    }
    public function deleteOlderThan($age){}
}
//          tabelle 1: activeUsers (id,ip,sid,lastActionTime,lastActionUrl)

//  für activeUsers brauchst du ne funktion die alle einträge löscht wo lastActionTime schon alter als x ist
//  und updaten anhand der sid
//  session id