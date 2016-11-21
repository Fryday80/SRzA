<?php
namespace Usermanager\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Usermanager\Form\ProfileForm;

class JobController extends AbstractActionController
{
    public function __construct() {
    }

    public function indexAction() {
        //@todo list all jobs
        return new ViewModel(array(
            'jobs' => 42,
        ));
    }
    public function addAction() {
        //@todo add job
        return new ViewModel(array(
            'job' => 42,
        ));
    }
    public function editAction() {
        //@todo edit job
        return new ViewModel(array(
            'job' => 42,
        ));
    }
    public function deleteAction() {
        //@todo remove job
        return new ViewModel(array(
            'job' => 42,
        ));
    }
}
