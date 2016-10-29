<?php
namespace Media\Controller;


use Zend\Mvc\Controller\AbstractActionController;

class FileController extends AbstractActionController  {

    public function imageAction()
    {
        $path = $this->params('path');
        $fileContent =  file_get_contents('Data' . $path);
        $response = $this->getResponse();
        $response->setContent($fileContent);
        $response
            ->getHeaders()
            ->addHeaderLine('Content-Transfer-Encoding', 'binary')
            ->addHeaderLine('Content-Type', 'image/jpg')
            ->addHeaderLine('Content-Length', mb_strlen($fileContent));

        return $response;
    }
    public function downloadAction() {
        $path = $this->params('path');
        $filePath = 'Data'.$path;
        $response = new \Zend\Http\Response\Stream();
        $response->setStream(fopen($filePath, 'r'));
        $response->setStatusCode(200);
        $response->setStreamName(basename($filePath));
        $headers = new \Zend\Http\Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="' . basename($filePath) .'"',
            'Content-Type' => 'application/octet-stream',
            'Content-Length' => filesize($filePath),
            'Expires' => '@0', // @0, because zf2 parses date as string to \DateTime() object
            'Cache-Control' => 'must-revalidate',
            'Pragma' => 'public'
        ));
        $response->setHeaders($headers);
        return $response;
    }
    public function uploadAction() {
        print("needs to implement file upload action");
    }
}