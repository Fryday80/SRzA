<?php
namespace Nav\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;

class NavTable extends AbstractTableGateway
{
    public $table = 'nav';
    
    public function __construct(Adapter $dbAdapter)
    {
        $this->adapter = $dbAdapter;
        $this->resultSetPrototype = new ResultSet(ResultSet::TYPE_ARRAY);
        $this->initialize();
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function testSave() {}
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
                n.label,
                n.route,
                n.resource,
                n.privilege,
                COUNT(*)-1 AS level,
                ROUND ((n.rgt - n.lft - 1) / 2) AS offspring
                FROM nav AS n,
                nav AS p
                WHERE n.lft BETWEEN p.lft AND p.rgt
                GROUP BY n.lft
                ORDER BY n.lft;', array());
        
        $result = $statement->toArray();
        $result = $this->toArray($result);
        return $result;
    }
    public function add() {
//         UPDATE nav SET rgt=rgt+2 WHERE rgt >= $RGT;
//         UPDATE nav SET lft=lft+2 WHERE lft > $RGT;
//         INSERT INTO nav (label,lft,rgt) VALUES ('Halbaffen', $RGT, $RGT +1);

        $rgt = 2;
        $label = 'testLabel';
        $route = 'user/add';

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
            'route'     => $route,
            'lft'       => $rgt,
            'rgt'       => $rgt + 1
        ]);
    }
    public function toArray(array $nodes)
    {
        $result     = array();
        $stackLevel = 0;
        // Node Stack. Used to help building the hierarchy
        $stack = array();
        foreach ($nodes as $node) {
            $node['pages'] = array();
            // Number of stack items
            $stackLevel = count($stack);
            // Check if we're dealing with different levels
            while ($stackLevel > 0 && $stack[$stackLevel - 1]['level'] >= $node['level']) {
                array_pop($stack);
                $stackLevel--;
            }
            // Stack is empty (we are inspecting the root)
            if ($stackLevel == 0) {
                // Assigning the root node
                $i = count($result);
                $result[$i] = $node;
                $stack[] =& $result[$i];
            } else {
                // Add node to parent
                $i = count($stack[$stackLevel - 1]['pages']);
                $stack[$stackLevel - 1]['pages'][$i] = $node;
                $stack[] =& $stack[$stackLevel - 1]['pages'][$i];
            }
        }
        return $result;
    }
}