<?php
namespace Media\Controller;


use Zend\Mvc\Controller\AbstractActionController;

class ListController extends AbstractActionController  {

    public function indexAction()
    {
        //show a file browser

        $mediaSerivce = $this->getServiceLocator()->get('MediaService');
        $files = $mediaSerivce->getImportPreview();
        //dumpd($files);
        return array(
            'files' => $files,
            //'jsonFiles' => json_encode($files)
        );

    }
}