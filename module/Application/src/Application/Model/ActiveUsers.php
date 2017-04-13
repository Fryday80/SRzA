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
    private $storetime = 'z.b. 2 Monate';
    private $activeUser = array(        //theorie zum bauen
        'user_id' => '',                // so ein array bauen und dann damit handeln, oder was haste vor?
        'active' => false,              // das thema kann man ja auf x weisen angehen..
        'last_sid' => '',
        'dbRows' => array('$rows'),
    );

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }
    public function getBySID($sid){
        $row = $this->select(array('sid' => (int) $sid));
        if (!$row)
            return false;

        return $row->toArray()[0];
    }
    private function updateActive($data) {
        //@todo hier brauchts eigentlich nur eine public funktion
        //@todo wenn es einen eintrag mit dieser sid giebt dann updaten
        //@todo ansonsten eine neue zeile
        //@todo deleteOlderThan
        //@todo am coolsten wäre es wenn man des in ein sql packen könnte (da giebts paar wege glaub ich aber all sql mässig)

        //@todo und mach noch ne spalte rein mit userID sonst können wir keine anzeige machen wer gerade on ist
    }
    private function add($data){
        if (!$this->insert(array('sid' => $data['sid'])))
            return false;
        return $this->getLastInsertValue();
    }


    private function save($sid, $data) {
        if (!$this->update(array('sid' => $data['sid']), array('sid' => (int)$sid)))
            return false;
        return $sid;
    }
    private function remove($id) {
        return ($this->delete(array('id' => (int)$id)))? $id : false;
    }

    private function deleteOlderThan($dataSet){
        foreach ($dataSet as $key => $data){
            $age = /* processed */$data['lastActionTime'];
            if ( $age > $this->storetime ){
                $this->remove($data['id']);
            }
        }
    }
}

//@todo man kann ja ne db machen mit
//@todo id,ip,sid,lastActionTime,lastActionUrl
//@todo und ne hashTable id, sid, au_id oder so (ggf + ph_id + sl_id falls du da noch was willst)
//@todo dann kannste ->get( ActiveUserDBTable, where ( hashTable.au_id (sid = $sid) ) ) // absolut falscher string aber du weißt was ich meine

//          tabelle 1: activeUsers (id,ip,sid,userID,lastActionTime,lastActionUrl)

//  für activeUsers brauchst du ne funktion die alle einträge löscht wo lastActionTime schon alter als x ist
//  und updaten anhand der sid
//  session id