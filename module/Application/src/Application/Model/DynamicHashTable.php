<?php
namespace Application\Model;

use Exception;
use Zend\Db\Adapter\AdapterAwareInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class DynamicHashTable extends AbstractTableGateway implements AdapterAwareInterface
{

    public $table = 'dynamic_hash';

    public function getAll() {
        try {
            $sql = new Sql($this->getAdapter());
            $select = $sql->select();
            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $this->resultSetPrototype->initialize($statement->execute())->toArray();
            return $result;
        } catch (Exception $e) {
            throw new Exception($e->getPrevious()->getMessage());
        }
    }

    public function getByHash($hash) {
        $this->clean();
        $result = $this->select([
            'hash' => $hash
        ])->toArray();
        if (count($result) < 1) {
            return null;
        }
        return $result[0];
    }
    public function create($lifetime = 3600) {
        try {
            $hash = $this->randomToken();
            $this->insert(array(
                'hash' => $hash,
                'time' => time() + $lifetime
            ));
            return $hash;
        } catch (Exception $e) {
            throw new Exception($e->getPrevious()->getMessage());
        }
    }

    public function deleteByHash($hash) {
        return $this->delete([
            'hash' => $hash
        ]);
    }
    public function clean() {
        $where = new Where();
        $where->lessThan('time', time());
        $this->delete($where);
    }
    private function randomToken($length = 32){
        $length = $length / 2;
        if (function_exists('random_bytes')) {
            return bin2hex(random_bytes($length));
        }
        if (function_exists('mcrypt_create_iv')) {
            return bin2hex(mcrypt_create_iv($length, MCRYPT_DEV_URANDOM));
        }
        if (function_exists('openssl_random_pseudo_bytes')) {
            return bin2hex(openssl_random_pseudo_bytes($length));
        }
    }

    /**
     * Set db adapter
     *
     * @param Adapter $adapter
     * @return AdapterAwareInterface
     */
    public function setDbAdapter(Adapter $adapter) {
        $this->adapter = $adapter;
//        $this->resultSetPrototype = new ResultSet(ResultSet::TYPE_ARRAY);
        $this->initialize();
    }
}
