<?php
namespace Media\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;

class FileTable extends AbstractTableGateway
{
    public $table = 'files';

    public function __construct(Adapter $dbAdapter) {
        $this->adapter = $dbAdapter;
        $this->resultSetPrototype = new ResultSet(ResultSet::TYPE_ARRAY);
        $this->initialize();
    }
    public function getAll() {
        return $this->select()->toArray();
    }

    public function getAllFolders() {
        return $this->select('type = "folder"')->toArray();
    }






    public function getItem($id) {
        $result = $this->select("id = $id")->toArray();
        if (count($result) < 1) {
            return false;
        }
        return $result[0];
    }
    public function getNav($id)
    {
        $id  = (int) $id;
        $rowset = $this->select(array('menu_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row with menu_id: $id");
        }

        $statement = $this->adapter->query('
                SELECT 
                n.id,
                n.label,
                n.uri,
                n.permission_id,
                res.resource_name AS resource,
                perm.permission_name AS privilege,

                COUNT(*)-1 AS level,
                ROUND ((n.rgt - n.lft - 1) / 2) AS offspring
                FROM nav AS n, nav AS p

                INNER JOIN permission as perm ON permission_id = perm.id
                INNER JOIN resource as res ON perm.resource_id = res.id

                WHERE n.lft BETWEEN p.lft AND p.rgt
                GROUP BY n.lft
                ORDER BY n.lft;', array());

        $result = $statement->toArray();
        $result = $this->toArray($result);
        return $result;
    }
    public function add($data, $lft) {
        return 'function not used try append instad';
//         UPDATE nav SET rgt=rgt+2 WHERE rgt >= $RGT;
//         UPDATE nav SET lft=lft+2 WHERE lft > $RGT;
//         INSERT INTO nav (label,lft,rgt) VALUES ('Halbaffen', $RGT, $RGT +1);

        $rgt = 2;
        $label = 'testLabel';
        $uri = 'user/add';

        $sql = $this->sql;
        $update = $sql->update();
        $update->set(['rgt' => new \Zend\Db\Sql\Expression("rgt + 2")]);
        $update->where("rgt >= $rgt");
        $this->executeUpdate($update);

        $update->set(['lft' => new \Zend\Db\Sql\Expression("lft + 2")]);
        $update->where("lft > $rgt");
        $this->executeUpdate($update);

        $this->insert([
            'menu_id'   => 0,
            'label'     => $label,
            'rui'       => $uri,
            'lft'       => $rgt,
            'rgt'       => $rgt + 1
        ]);
    }
    public function updateNesting($row) {
        $id = $row['id'];
        unset($row['id']);
        $this->update($row, array('id' => $id));
    }
    public function updateItem($data, $id) {
        $this->update($data, array('id' => $id));
    }
    public function deleteByID($id) {
        $this->delete("id = $id");
    }

}
