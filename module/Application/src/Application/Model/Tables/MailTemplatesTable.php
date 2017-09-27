<?php
namespace Application\Model\Tables;

use Exception;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class MailTemplatesTable extends AbstractTableGateway
{

    public $table = 'mail_templates';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet(ResultSet::TYPE_ARRAY);
        $this->initialize();
    }

    public function getAllTemplates()
    {
        return $this->select()->toArray();
    }
    public function getBy(array $select)
    {
        $result = $this->select($select)->toArray();
        if (count($result) < 1) {
            return null;
        }
        return $result[0];
    }

    public function save(Array $data)
    {
        $data = $this->exchangeArray($data);
        $name = $data['name'];
        $select = array( 'name' => $data['name']);
        if ($this->getBy($select)) {
            $this->update($data, $select);
        } else {
            throw new Exception('Template does not exist');
        }
        return $this->lastInsertValue;
    }
    public function deleteBy($key)
    {
        $select = (is_array($key))                      ? $key                  : null;
        $select = ($select === null || is_int($key))    ? array('id' => $key)   : null;
        $select = ($select === null || is_string($key)) ? array('name' => $key) : null;
        if( $this->isBuildIn($select) ) return false;
        return $this->delete($select);
    }

    private function exchangeArray($data)
    {
        $return['name']           = ( isset($data['name']) )          ? $data['name']           : null;
        $return['sender']         = ( isset($data['sender']) )        ? $data['sender']         : null;
        $return['sender_address'] = ( isset($data['sender_address']) )? $data['sender_address'] : null;
        $return['msg']            = ( isset($data['msg']) )           ? $data['msg']            : null;
        $return['subject']        = ( isset($data['subject']) )       ? $data['subject']        : null;
        $return['variables']      = ( isset($data['variables']) )     ? $data['variables']      : null;
        return $return;
    }
}
