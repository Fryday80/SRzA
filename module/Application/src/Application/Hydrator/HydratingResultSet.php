<?php
namespace Application\Hydrator;

use Application\Model\Interfaces\IToObjectArray;
use Exception;
use Zend\Db\ResultSet\HydratingResultSet as ZendHydratingResultSet;

class HydratingResultSet extends ZendHydratingResultSet implements IToObjectArray
{
    /**
     * Cast result set to array of objects
     *
     * @return array
     */
    public function toObjectArray($keyProperty = null)
    {
        $return = [];
        foreach ($this as $row) {
            if ($keyProperty !== null) {
                if (property_exists($row, $keyProperty) ) {
                    $return[$row[$keyProperty]] = $row;
                }
                continue;
            } else {
                $return[] = $row;
            }
        }
        return $return;
    }
    /**
     * Cast result set to array of objects
     *
     * @return array
     */
    public function toArray()
    {
        $return = [];
        foreach ($this as $row) {
            $return[] = $row;
        }
        return $return;
    }
    /**
     * Cast result set to array of arrays
     *
     * @return array
     * @throws Exception if any row is not castable to an array
     */
    public function toArrayOfArrays()
    {
        $return = [];
        foreach ($this as $row) {
            $return[] = $this->getHydrator()->extract($row);
        }
        return $return;
    }
}