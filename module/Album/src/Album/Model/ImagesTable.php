<?php
namespace Album\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class ImagesTable extends AbstractTableGateway
{

    public $table = 'images';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function getById($id) {
        $row = $this->select(array('id' => (int) $id))->current();
        if (!$row)
            return false;

        return $row->toArray();
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
        return ($this->delete(array('id' => (int)$id))) ? true : false;
    }

    public function getImagesByAlbumID($id)
    {
        if(!is_int($id)) {
            throw new \Exception("ID must be an int;");
        }

        $sql = new Sql($this->getAdapter());

        $select = $sql->select()
            ->from(array(
                'albums' => 'albums'
            ))
            ->columns(array(
                'timestamp',
                'preview_pic',
            ))
            ->join(array(
                'albumImages' => 'albumimages'
            ), 'albums.id = albumImages.album_id', array(
            ), 'left')
            ->join(array(
                'images' => 'images'
            ), 'albumImages.image_id = images.id', array(
                'id',//
                'filename',
                'extension',
                'text_1',
                'text_2',
                'visibility'
            ), 'left')
            ->where("albums.id = $id");

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $this->resultSetPrototype->initialize($statement->execute())
            ->toArray();
        return $result;
    }
}
