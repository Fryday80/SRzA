<?php
namespace Album\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class AlbumImagesTable extends AbstractTableGateway
{

    public $table = 'albumimages';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    //data like: array('columName' => $data['columName'] )
    public function add($data) {
        if (!$this->insert($data))
            return false;
        return $this->getLastInsertValue();
    }
    
    public function change($id, $data) {
        if (!$this->update($data, array('id' => (int)$id)))
            return false;
        return $id;
    }
    
    public function remove($id) {
        return $this->delete(array('id' => (int)$id));
    }
    
    public function removeByImageID($id) {
        return $this->delete(array('image_id' => (int)$id));
    }
    
    public function removeByAlbumID($id) {
        return $this->delete(array('album_id' => (int)$id));
    }
    
}
