<?php
namespace Media\Controller;

require_once('G:\Projects\christoph\SRzA\public\libs\rich-filemanager\connectors\php\filemanager.php');

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class FileBrowserController extends AbstractActionController  {

    public function indexAction() {
        $viewModel = new ViewModel();
        $viewModel->setVariables(array('key' => 'value'))
            ->setTerminal(true);
        return $viewModel;
    }
    public function actionAction() {
        $dataDir = getcwd() . '/data';
        $fm = getFileBrowserFor($dataDir);
        $fm->handleRequest();

        $viewModel = new ViewModel();
        $viewModel->setVariables(array('key' => 'value'))
            ->setTerminal(true);
        return $viewModel;
    }
}