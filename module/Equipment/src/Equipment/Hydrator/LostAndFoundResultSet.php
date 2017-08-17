<?php
namespace Equipment\Hydrator;


use Application\Hydrator\HydratingResultSet;
use Equipment\Model\DataModels\Equip;

class LostAndFoundResultSet extends HydratingResultSet
{
    /**
     * Iterator: get current item
     *
     * @return object
     */
    public function current()
    {
        if ($this->buffer === null) {
            $this->buffer = -2; // implicitly disable buffering from here on
        } elseif (is_array($this->buffer) && isset($this->buffer[$this->position])) {
            return $this->buffer[$this->position];
        }
        $data = $this->dataSource->current();
		$object = $data;
//        // $data = array of one db row
//        /** @var Equip $object */
//		$object = unserialize($data['data']);
//        $object->metaDataUpdate($data);

        if (is_array($this->buffer)) {
            $this->buffer[$this->position] = $object;
        }

        return $object;
    }
}