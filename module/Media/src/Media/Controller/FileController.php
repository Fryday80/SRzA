<?php
namespace Media\Controller;


use Media\Service\MediaService;
use Zend\Mvc\Controller\AbstractActionController;

class FileController extends AbstractActionController  {
    
    

    public function fileAction()
    {
        $mediaService = $this->getServiceLocator()->get('MediaService');
        $path = $this->params('path');
        return $mediaService->createFileResponse($path, $this->getResponse());
    }
    public function imageAction()
    {
        $mediaService = $this->getServiceLocator()->get('MediaService');
        $path = $this->params('path');
        return $mediaService->createFileResponse($path, $this->getResponse());
    }
}