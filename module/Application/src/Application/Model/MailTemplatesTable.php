<?php
namespace Application\Model;

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
        $id = $data['id'];
        if (!isset($data['id']) || $id == 0 || $id = '') {
            unset($data['id']);
            $this->insert($data);
        } else {
            $select = array( 'id' => $data['id']);
            if ($this->getBy($select)) {
                if( $this->isBuildIn($select) ) return false;
                $this->update($data, $select);
            } else {
                throw new Exception('Template does not exist');
            }
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
    
    public function isBuildIn(Array $select) {
        $entry = $this->getBy($select);
        if ($entry === null) return false;
        if ($entry['build_in'] == 1) return true;
        return false;
    }

    private function exchangeArray($data)
    {
        $return['id']             = ( isset($data['id']) )            ? $data['id']            : 0;
        $return['name']           = ( isset($data['name']) )          ? $data['name']          : null;
        $return['sender']         = ( isset($data['sender']) )        ? $data['sender']        : null;
        $return['sender_address'] = ( isset($data['sender_address']) )? $data['sender_address']: null;
        $return['msg']            = ( isset($data['msg']) )           ? $data['msg']           : null;
        $return['build_in']       = ( isset($data['build_in']) )      ? $data['build_in']      : 0;
        $return['subject']        = ( isset($data['subject']) )       ? $data['subject']       : null;
        // get variables
        $from = '{{';
        $to = '}}';
        $aMatches = array();
        preg_match_all("/\\".$from."(.*?)\\".$to."/", $data['msg'], $aMatches);
        $return['variables'] = implode (' <br/>', $aMatches[1]);
        return $return;
    }
}
