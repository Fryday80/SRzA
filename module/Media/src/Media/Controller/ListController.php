<?php
namespace Media\Controller;


use Zend\Mvc\Controller\AbstractActionController;

class ListController extends AbstractActionController  {

    public function indexAction()
    {
        //show a file browser
        $fileTable = $this->getServiceLocator()->get('Media\Model\FileTable');
        $files = $fileTable->getAll();
        return array(
            'files' => $files
        );
    }
}