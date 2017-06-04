<?php

namespace Application\Service;


use Auth\Model\User;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class TemplateTypes {
    const SUCCESSFUL_REGISTERED = 'SUCCESSFUL_REGISTERED';
    const RESET_PASSWORD = 'RESET_PASSWORD';
}

class MessageService implements ServiceManagerAwareInterface
{
    /** @var  ServiceManager */
    private $serviceManager;

    public function SendMail($address, $Subject, $message) {
        try {
            mail($address, $Subject, $message, "From: Absender <absender@euredomain.de>");
        } catch(Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * @param $templateID
     * @param $user User
     * @return bool
     */
    public function SendMailFromTemplate($target, $templateID, $templateVars) {
        try {
            //load template from db
//            $mailTemplatesTable = $this->serviceManager->get('MailTemplatesTable');
//            $template = $mailTemplatesTable->getByID($templateID);
            $template = 'sers {{name}} und {{email}}';
            if ($templateID == null) {
                //@todo error: "no template with this id"
            }
            //parse template vars -> check if all exists in data
            $template = $this->buildTemplateString($template, [
                'name' => 'Salt',
                'email' => 'mail@me.de'
            ]);
            bdump($template);
            //replace and send

            $this->SendMail($user->email, 'betreff -> test', 'nachricht ');
            //mail($address, $Subject, $message, "From: Absender <absender@euredomain.de>");
            bdump($user);
        } catch(Exception $e) {
            return false;
        }
        return false;
    }


//    public function pmTo($userId, $msg) {}
//    public function getUnreadPms() {}
//    public function getPms($since) {}
//
//    /**
//     * @param $channel int | string   userID or channelName
//     * @param $msg
//     * @param $media
//     */
//    public function chatSay($channel, $msg, $media) {}
//    public function chatGetChannel($channel, $since) {}
    /**
     * Set service manager
     *
     * @param ServiceManager $serviceManager
     */
    public function setServiceManager(ServiceManager $serviceManager) {
        $this->serviceManager = $serviceManager;
    }
    private function buildTemplateString($string, $data) {
        if (preg_match_all("/{{(.*?)}}/", $string, $result)) {
            bdump($result);
            foreach ($result[1] as $i => $varName) {
                if (isset($data[$varName]))
                    $string = str_replace($result[0][$i], $data[$varName], $string);
            }
        }
        return $string;
    }
}