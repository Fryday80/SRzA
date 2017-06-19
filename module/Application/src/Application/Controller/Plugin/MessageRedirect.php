<?php
namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class MessageRedirect extends AbstractPlugin{
    public function __invoke($title, $msg) {
        //set flash msg and redirect
        $flashMsger = $this->getController()->flashmessenger();
        $flashMsger->addMessage($title . ':::' . $msg, 'messagePage');
        return $this->getController()->redirect()->toRoute('message');
    }
}