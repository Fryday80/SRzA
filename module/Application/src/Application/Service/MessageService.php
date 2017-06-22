<?php
namespace Application\Service;

use Application\Model\MailTemplatesTable;
use Exception;

class MessageService
{
    /** @var MailTemplatesTable  */
    protected $mailTemplatesTable;
    
    function __construct(MailTemplatesTable $mailTemplatesTable)
    {
        $this->mailTemplatesTable = $mailTemplatesTable;
    }

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
     * @param $templateName
     * @param $templateVars
     * @return bool
     * @throws Exception
     */
    public function SendMailFromTemplate($target, $templateName, $templateVars) {
        try {
            //load template from db
            $template = $this->mailTemplatesTable->getBy(array( 'name' => $templateName));

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

    public function getAllTemplates()
    {
        return $this->mailTemplatesTable->getAllTemplates();
    }
    public function getTemplateByName($name)
    {
        return $this->mailTemplatesTable->getBy(array( 'name' => $name));
    }

    public function saveTemplate(Array $data)
    {
        return $this->mailTemplatesTable->save($data);
    }
    
    private function buildTemplateString($string, $data) {
        if (preg_match_all("/{{(.*?)}}/", $string, $result)) {
            foreach ($result[1] as $i => $varName) {
                if (isset($data[$varName]))
                    $string = str_replace($result[0][$i], $data[$varName], $string);
            }
        }
        return $string;
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
}
class TemplateTypes {
    const SUCCESSFUL_REGISTERED = 'successfulRegistered';
    const RESET_PASSWORD = 'passwordForgotten';
    const ACTIVATION = 'activation';
    const DEACTIVATION = 'deactivation';
}