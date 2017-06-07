<?php
namespace Media\Controller;


use Media\Service\MediaService;
use Zend\Mvc\Controller\AbstractActionController;

class FileController extends AbstractActionController  {

    /**
     * @var $mediaService MediaService
     */
    private $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    public function fileAction()
    {
        $path = $this->params('path');
        return $this->mediaService->createFileResponse($path, $this->getResponse());
    }
    public function imageAction()
    {
        $path = $this->params('path');
        return $this->mediaService->createFileResponse($path, $this->getResponse());
    }
}