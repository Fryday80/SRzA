<?php
namespace Application\Hydrator;

use Application\Model\Interfaces\IToObjectArray;
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
}