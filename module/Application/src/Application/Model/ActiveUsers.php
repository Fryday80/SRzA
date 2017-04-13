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
    public function updateActive($data) {
        //@todo hier brauchts eigentlich nur eine public funktion
        //@todo wenn es einen eintrag mit dieser sid giebt dann updaten
        //@todo ansonsten eine neue zeile
        //@todo deleteOlderThan
        //@todo am coolsten wäre es wenn man des in ein sql packen könnte (da giebts paar wege glaub ich aber all sql mässig)

        //@todo und mach noch ne spalte rein mit userID sonst können wir keine anzeige machen wer gerade on ist
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