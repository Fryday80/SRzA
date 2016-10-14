<?php
namespace Media\Controller;


use Zend\Mvc\Controller\AbstractActionController;

class FileController extends AbstractActionController  {

    public function indexAction()
    {
        $path = $this->params('path');
        print($path);die;
        //@todo return file with the right head pls
    }
}