<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $messages = $this->flashMessenger()->getMessagesFromNamespace('MessagePage');
        if (count($messages) == 0) {
            return $this->redirect()->toRoute('home');
        }
        $message = explode('::', $messages[0]);

        return array(
            'title' => $message[0],
            'message' => $message[1],
        );
    }
}
