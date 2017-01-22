<?php

namespace Application\Service;


class MessageService
{
    public function SendMail($address, $Subject, $message) {
        try {
            mail($address, $Subject, $message, "From: Absender <absender@euredomain.de>");
        } catch(Exception $e) {
            return false;
        }
        return true;
    }
    public function SendMailFromTemplate($templateName, $user) {
        try {
            //@todo get address and template
            //mail($address, $Subject, $message, "From: Absender <absender@euredomain.de>");
        } catch(Exception $e) {
            return false;
        }
        return false;
    }


}
class TemplateTypes {
    const SUCCESSFUL_REGISTERED = 'SUCCESSFUL_REGISTERED';
    const RESET_PASSWORD = 'RESET_PASSWORD';
}
class MessageTemplate {
    private $template = "";

}