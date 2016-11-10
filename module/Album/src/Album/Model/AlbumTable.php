<?php
namespace Album\Model;

use Zend\Db\TableGateway\TableGateway;

class AlbumTable
{
    protected $tableGateway;
    protected $galleryTable;

    public function __construct(TableGateway $tableGateway, $galleryTable)
    {
        $this->tableGateway = $tableGateway;
        $this->galleryTable = $galleryTable;
    }

    public function fetchAll()
    {
        //@todo kann ich das hier nicht umleiten auf Gallery Table?
        return $this->galleryTable->getAlbums();

        /* ---alter code
        $resultSet = $this->tableGateway->select();
        return $resultSet;
        */
    }

    public function getAlbum($id)
    {
        $id  = (int) $id;
        //@todo kann ich das hier nicht umleiten auf Gallery Table?
        return $this->galleryTable->getAlbumById($id);

        /* ---alter code
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
        */
    }

    public function saveAlbum(Album $album)
    {
        $data = array(
            'aid',
            'folder',
            'event' => $album->event,
            'timestamp'  => $album->timestamp,
            'preview_pic',
            'avisibility'
        );

        $id = (int) $album->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getAlbum($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Album id does not exist');
            }
        }
    }

    public function deleteAlbum($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}
