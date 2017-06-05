<?php
namespace Application\Service;

use Exception;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class TemplateTypes {
    const SUCCESSFUL_REGISTERED = 'SUCCESSFUL_REGISTERED';
    const RESET_PASSWORD = 'passwordForgotten';
}

class MessageService implements ServiceManagerAwareInterface
{
    /** @var  ServiceManager */
    private $serviceManager;

    public function SendMail($address, $Subject, $message, $sender, $senderAddress) {
        try {
            $headers =  'MIME-Version: 1.0' . "\r\n";
            $headers .= 'From: '.$sender.' <'.$senderAddress.'>' . "\r\n";
            $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
            mail($address, $Subject, $message, $headers);//"From: Absender <absender@euredomain.de>");
        } catch(Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * @param $target
     * @param $templateID
     * @param $templateVars
     * @return bool
     */
    public function SendMailFromTemplate($target, $templateID, $templateVars) {
        try {
            //load template from db
            $mailTemplatesTable = $this->serviceManager->get('Application\Model\MailTemplatesTable');
            $template = $mailTemplatesTable->getByID($templateID);

            if (!$template) {
                //@todo error: "no template with this id"
                throw new Exception('no template with this templateID');
                return;
            }
            //parse template vars -> check if all exists in data
            $template['subject'] = $this->buildTemplateString($template['subject'], $templateVars);
            $template['msg'] = $this->buildTemplateString($template['msg'], $templateVars);

            //send
            $this->SendMail($target, $template['subject'], $template['msg'], $template['sender'], $template['sender_address']);
            return true;
        } catch(Exception $e) {
            throw new Exception($e->getMessage());
            return false;
        }
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