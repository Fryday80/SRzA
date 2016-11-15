<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 15.11.2016
 * Time: 09:12
 */
namespace Usermanager\Helper;

use Zend\Mvc\Controller\AbstractActionController;


Class ViewHelper extends \Zend\Mvc\Controller\AbstractActionController {
    
    public $variables;
    private $variables_names = array();
    
    function __construct()
    {
    }

    public function set ($settings = array())
    {
        $this->variables = $settings;
        foreach ($settings as $var_names => $var_value){
           array_push($this->variables_names, $var_names);
        }
    }

    public function getVar ($var_name){
        if (in_array($var_name, $this->variables_names)) {
            return $this->variables[$var_name];
        }
        dumpd ('only available variables: ' . $var_name);
    }
    
    public function printIndex () {

        $bg_head = 'style="width: 20%; background-color: #ffffff"';
        $bg_1 ='style = "background-color: none;"';
        $bg_2 ='style = "background-color: #ffffff;"';
        $button_1 = ' style = "background-color: none; text-align: center; display: block"';
        $button_2 = ' style = "background-color: #ffffff; text-align: center; display: block"';
        $i = 1;
        ?>
        <br>
        <table style="width: 100%">
            <tr>
                <th <?= $bg_head?> >Username</th>
                <th <?= $bg_head?> >Vorname</th>
                <th <?= $bg_head?> >Name</th>
                <th <?= $bg_head?> >Mitgliedsnummer</th>
                <th <?= $bg_head?> >&nbsp;</th>
            </tr>
            <?php
            foreach ($this->variables['profiles'] as $user):
                $bg = 'bg_' . $i;
                $button = 'button_' . $i;
                $i = ($i == 1) ? 2 : 1;
                ?>
                <tr>
                <td <?= $$bg ?> "> <?= $user['name'] ?> </td>             <?php //username ?>
                <td <?= $$bg ?> "> <?= '' ?> </td>                        <?php //Vorname ?>
                <td <?= $$bg ?> "> <?= '' ?> </td>                        <?php //Nachname ?>
                <td <?= $$bg ?> "> <?= '' ?> </td>                        <?php //Mitgliedsnummer ?>
                <td <?= $$button ?> >
                <a href="#">Auswählen</a><br></td>
                </tr>
            <?php endforeach; ?>
        </table><br>
        <?php
    }
}