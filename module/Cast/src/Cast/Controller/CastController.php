<?php
namespace Cast\Controller;

use Auth\Model\UserTable;
use Cast\Form\CharacterForm;
use Cast\Model\FamiliesTable;
use Cast\Utility\CharacterDataTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class CastController extends AbstractActionController
{
    public function indexAction() {
        $characterTable = $this->getServiceLocator()->get("Cast\Model\CharacterTable");
        bdump($characterTable->getAllCastData());
        return new ViewModel(array(
            'chars' => $characterTable->getAllCastData(),
        ));
    }
}
