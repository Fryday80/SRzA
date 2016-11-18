<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 15.11.2016
 * Time: 09:12
 */
namespace Usermanager\Helper;

use Zend\View\Helper\AbstractHelper;


Class ViewHelper extends AbstractHelper {

    public $variables = array();
    private $variables_names = array();
    
    function __construct()
    {
    }

    function render($data)
    {
        //ich hatte gedacht der erste eintrag von $data ist ein array für den head wo nur die title drin stehen
        // nee das sieht so aus:: $bla[0][$key] ;/ / 4ja schin klar aber ich mein
        $datarow = '';
        $datahead = '';//woher kommen die daten bei dir
        $i = 0;

        foreach ($data as $row) {
                $datarow .= '<tr>'; //datarow sind die einzelnen user
            foreach ($row as $key => $value){
                if( $i == 0) {
                    $datahead .= "<td>$key</td>"; 
                }
                $datarow .= "<td>$value</td>";
            }
            $datarow .= '</tr>';
            $i++;
        }
        $table = "<table id='table'  class=\"display\" cellspacing=\"0\" width=\"100%\"><thead><tr>$datahead</tr></thead>";
        $table .= "<tfoot><tr>$datahead</tr></tfoot><tbody>";
        $table .= $datarow;
        $table .= '</tbody></table>';
        $table .= '<script>';
        $table .= 'console.log("########");';
        $table .= '$("#table").DataTable( {';
        $table .= '    dom: "Bfrtip",';
        $table .= '    buttons: [';
        $table .= '    "copy"", "excel", "pdf"';
        $table .= ']';
        $table .= '} );';
        $table .= '</script>';
        return $table;
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
        //$this->url('usermanager/profile', array('id' => $user['id']));

        $this->getCurrent()->getVariable('parentVariable');
        ?>
        <br>nope
        <table style="width: 100%">
            <thead>
                <tr>
                    <th <?= $bg_head?> >Username</th>
                    <th <?= $bg_head?> >Vorname</th>
                    <th <?= $bg_head?> >Name</th>
                    <th <?= $bg_head?> >Mitgliedsnummer</th>
                    <th <?= $bg_head?> >&nbsp;</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th <?= $bg_head?> >Username</th>
                    <th <?= $bg_head?> >Vorname</th>
                    <th <?= $bg_head?> >Name</th>
                    <th <?= $bg_head?> >Mitgliedsnummer</th>
                    <th <?= $bg_head?> >&nbsp;</th>
                </tr>
            </tfoot>
            <tbody>
                <?php
                foreach ($this->variables['profiles'] as $user):
                    $bg = 'bg_' . $i;
                    $button = 'button_' . $i;
                    $i = ($i == 1) ? 2 : 1;
                    ?>
                    <tr>
                    <td <?= $$bg ?> "> <?= $user['name'] ?> </td>             <?php //username ?>
                    <td <?= $$bg ?> "> <?= $user['realfirstname'] ?> </td>                        <?php //Vorname ?>
                    <td <?= $$bg ?> "> <?= $user['realname'] ?> </td>                        <?php //Nachname ?>
                    <td <?= $$bg ?> "> <?= '' ?> </td>                        <?php //Mitgliedsnummer ?>
                    <td <?= $$button ?> >
                    <a href="<?php echo $this->variables['profile_url'];?>">Auswählen</a><br>
                    <?php if ($this->variables['allowance'] == 'editor'): ?>
                    <a href="<?php echo $this->variables['delete_url'];?>">Löschen</a><br>
                    <?php endif; ?>
                    </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table><br>
        <?php
    }
}