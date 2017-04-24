<?php
namespace Cast\Controller;

use Auth\Model\UserTable;
use Cast\Form\CharacterForm;
use Cast\Model\FamiliesTable;
use Cast\Service\CastService;
use Cast\Utility\CharacterDataTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Exception;

class CastController extends AbstractActionController
{
    public function indexAction() {
        /** @var CastService $castService */
        $castService = $this->getServiceLocator()->get("CastService");
        $castService->getStanding();
//        $characterTable = $this->getServiceLocator()->get("Cast\Model\CharacterTable");
//        bdump($characterTable->getAllCastData());
        return new ViewModel(array(
            'root' => $castService->getStanding(),
        ));
    }
}
