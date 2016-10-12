<?php
namespace Media\Controller;


use Zend\Mvc\Controller\AbstractActionController;

class ListController extends AbstractActionController  {

    public function indexAction()
    {
        //show a file browser
        $fileTable = $this->getServiceLocator()->get('Media\Model\FileTable');
        $files = $fileTable->getAllFolders();
        return array(
            'files' => $files,
            'jsonFiles' => json_encode($files)
        );
    }
}