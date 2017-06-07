<?php
namespace Media\Controller;


use Media\Service\MediaService;
use Zend\Mvc\Controller\AbstractActionController;

class ListController extends AbstractActionController  {

    private $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }
    public function indexAction()
    {
        //show a file browser
//        $files = $this->mediaService->getImportPreview();
//        //dumpd($files);
//        return array(
//            'files' => $files,
//            //'jsonFiles' => json_encode($files)
//        );

    }
}