<?php
namespace Cast\Controller;

use Auth\Model\UserTable;
use Cast\Form\CharacterForm;
use Cast\Model\FamiliesTable;
use Cast\Utility\CharacterDataTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Exception;

class CastController extends AbstractActionController
{
    public function indexAction() {
        throw new Exception("sers");
        $characterTable = $this->getServiceLocator()->get("Cast\Model\CharacterTable");
        throw new \Exception('bums');
        bdump($characterTable->getAllCastData());
        return new ViewModel(array(
            'chars' => $characterTable->getAllCastData(),
        ));
    }
}
