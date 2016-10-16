<?php
namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Album\Model\Album;
use Album\Form\AlbumForm;

class AlbumController extends AbstractActionController
{

    protected $albumTable;

    public function indexAction()
    {
        return new ViewModel(array(
            'albums' => $this->getAlbumTable()->fetchAll()
        ));
    }

    public function addAction()
    {
        $form = new AlbumForm();
        $form->get('submit')->setValue('Add');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $album = new Album();
            $form->setInputFilter($album->getInputFilter());
            $form->setData($request->getPost());
            
            if ($form->isValid()) {
                $album->exchangeArray($form->getData());
                $this->getAlbumTable()->saveAlbum($album);
                
                // Redirect to list of albums
                return $this->redirect()->toRoute('album');
            }
        }
        return array(
            'form' => $form
        );
    }

    public function editAction()
    {
        //des problem is bisal kompliziert ... liegt daran das wir hier eine extra Model classe haben (class Album).
        //man kann auch einfach nur die AlbumTable benutzen ... so machs ich auch fast überall. egal
        $request = $this->getRequest();
        $id = (int) $this->params()->fromRoute('id', 0);
        if (! $id && !$request->isPost()) {
            return $this->redirect()->toRoute('album', array(
                'action' => 'add'
            ));
        }
        $album = null;
        try {
            $album = $this->getAlbumTable()->getAlbum($id);
        } catch (\Exception $ex) {
            $album = new Album(); //das hier is das problem. er braucht immer ein album hatte aber beim post keins weil keine id da war
        }
        $form = new AlbumForm();
        $form->bind($album);//zum verständnis   durch bind ändern sich die daten in album auch wenn man die form daten ändert
        $form->get('submit')->setAttribute('value', 'Edit');

        if ($request->isPost()) {
            $data = $request->getPost();
            $data['timestamp'] = $data['timestamp'] / 1000;
            $form->setInputFilter($album->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getAlbumTable()->saveAlbum($album);
                
                // Redirect to list of albums
                return $this->redirect()->toRoute('album');
            }
        }
        
        return array(
            'id' => $id,
            'form' => $form
        );
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (! $id) {
            return $this->redirect()->toRoute('album');
        }
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getAlbumTable()->deleteAlbum($id);
            }
            
            // Redirect to list of albums
            return $this->redirect()->toRoute('album');
        }
        
        return array(
            'id' => $id,
            'album' => $this->getAlbumTable()->getAlbum($id)
        );
    }

    public function getAlbumTable()
    {
        if (! $this->albumTable) {
            $sm = $this->getServiceLocator();
            $this->albumTable = $sm->get('Album\Model\AlbumTable');
        }
        return $this->albumTable;
    }
    public function showAction () {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (! $id) {
            return $this->redirect()->toRoute('album', array(
                'action' => 'index'
            ));
        }
        // load Galerieansicht mit den bildern aus: wenn möglich "Overlay/Pop-Up-Gallery" -> keine neue Seite.. nur js??
        // $album_path = "../ftp/gallery/"
        // $pic_path = $albumpath.$form->path
    }
}
