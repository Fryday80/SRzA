<?php
namespace Usermanager\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Usermanager\Form\ProfileForm;

class FamilyController extends AbstractActionController
{
    public function __construct() {
    }

    public function indexAction() {
        //@todo list all Families
        return new ViewModel(array(
            'jsOptions' => 42,
            'families' => 42,
        ));
    }
    public function addAction() {
        //@todo add Family
        return new ViewModel(array(
            'job' => 42,
        ));
    }
    public function editAction() {
        //@todo edit Family
        return new ViewModel(array(
            'job' => 42,
        ));
    }
    public function deleteAction() {
        //@todo remove Family
        return new ViewModel(array(
            'job' => 42,
        ));
    }
}
