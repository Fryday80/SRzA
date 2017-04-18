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
    public function getItem($id) {
        $result = $this->select("id = $id")->toArray();
        if (count($result) < 1) {
            return false;
        }
        return $result[0];
    }
    public function getNav($id)
    {
        $statement = $this->adapter->query('
                SELECT 
                n.id,
                n.label,
                n.uri,
                n.min_role_id,
                role.role_name AS role_name,
                role.rid AS role_id,

                COUNT(*)-1 AS level,
                ROUND ((n.rgt - n.lft - 1) / 2) AS offspring
                FROM nav AS n, nav AS p
                INNER JOIN role ON min_role_id = role.rid

                WHERE n.lft BETWEEN p.lft AND p.rgt
                GROUP BY n.lft
                ORDER BY n.lft;', array());

        $result = $statement->toArray();
        $result = $this->toArray($result);
        return $result;
    }
    public function append($data) {
        //find highest rgt
        $select = $this->getSql()->select();
        $select->columns(array(
            'rgt' => new \Zend\Db\Sql\Expression('MAX(rgt)')
        ));
        $rowset = $this->selectWith($select);
        $lastItem = $rowset->current();
        if (!$lastItem) {
            throw new \Exception("Could not retrieve max rgt value");
        }
        $maxRgt = $lastItem['rgt'];
        //add new item at lft = $maxRgt + 1
        $this->insert([
            'menu_id'       => 0,
            'label'         => $data['label'],
            'uri'           => $data['uri'],
            'min_role_id'   => $data['rid'],
            'lft'           => $maxRgt + 1,
            'rgt'           => $maxRgt + 2
        ]);
        return $this->lastInsertValue;
    }

    /**
     * Update a row inc right and left value
     * @param $row
     */
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
