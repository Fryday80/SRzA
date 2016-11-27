<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 27.11.2016
 * Time: 00:26
 */

namespace Application\Utility;


use ZendDiagnosticsTest\TestAsset\Check\ReturnThis;

class TablehelperConfig
{
    private $accessService;
    private $owner = false;
    private $edit_permission = false;
    //https://datatables.net/reference/index for preferences/documentation
    //defaults
    public $lengthMenu = array(array(25, 10, 50, -1), array(25, 10, 50, "All"));
    public $select = 'select: { style: "multi" },'; //todo ??
    //settings
    public $buttons;
    public $dom = 'l f r t i p';

    function __construct($accessService = false, $owner = false)
    {
        if ($accessService){
            $this->accessService = $accessService;
            $this->edit_permission = $this->accessService->allowed("Usermanger\Controller\UsermanagerController", "edit");
        }
        if ($owner){
            $this->owner = $owner;
            $this->ownersButtons();
        }
        if ($this->edit_permission){
            $this->editorButtons();
        }

    }

    public function isOwner(){
        $this->owner = true;
    }

    public function isNotOwner(){
        $this->owner = false;
    }

    public function setButtons ($setting) {
        if (strtolower($setting) == 'all'){
            $this->buttons = array("print", "copy", "csv", "excel", "pdf");
        } else if (is_array($setting)){
            $this->buttons = $setting;
        }
        $this->dom = 'B ' . $this->dom;
    }

    private function ownersButtons(){
        $this->buttons = array ("print", "pdf");
        $this->dom = 'B ' . $this->dom;
    }

    private function editorButtons(){
        $this->buttons = $this->setButtons('all');
    }

    /**
     * @param $owner boolean if user is owner
     * @param $options mixed can be
     * left empty       -> returns standard setting ||
     * allowance string -> returns options set for a special allowance ||
     * options array   may look like array ('b' => '[settings]', 't')
     * if only $options array is given method will still work!
     * @return string set of options for js plugin "datatables"
     */
    private function setJSOptionForDatatables($owner = false , $options = false)
    {
        //defaults:
        $length = '"lengthMenu": [ [25, 10, 50, -1], [25, 10, 50, "All"] ],';
        $buttons = 'buttons: ["print", "pdf"],';
        $list_select = 'select: { style: "multi" },';
        $dom = '"dom": "Blfrtip",';

        //options change
        if ($options)
        {
            $allowed_options = array ('b','l','f','r','t','i','p');
            $js_dom = '"dom": "';
            $js_option = '';
            foreach ($options as $key => $option)
            {
                if (!is_array($option))
                {
                    $option = strtolower($option);
                    if (in_array($option, $allowed_options))
                    {
                        $js_dom .= ($option == 'b')? 'B' : $option;
                    }
                }
                else
                {
                    $option = strtolower($key);
                    if (in_array($key, $allowed_options))
                    {
                        $js_dom .= ($key == 'b')? 'B' : $key;
                        $js_option .= $option . ',';
                    }
                }
            }
            $js_dom .= (stristr($js_dom, 't')) ? '' : 't';  //adds the t-able in the dom if not set
            $js_dom .= '",';
            $js_option = str_replace(',,', ',', $js_option);
            $js_option_set = $js_option . $js_dom;
        }
        // cases:
        if ($owner)
        {
            return $length.$buttons.$list_select.$dom;
        }
        if ($this->accessService->allowed("Usermanger\Controller\UsermanagerController", "edit"))
        {

            $buttons = 'buttons: ["print", "copy", "csv", "excel", "pdf"],'; // hier kannste die buttons wählen   ja
            //da muss ich leider wieder sagen hässlicher gehts nur schwirig
            //ich mein machs halt so wie zend es dir vorlebt
            $conf = array(
                //ich weiß nich wie die config von dataTable.js jetz genau ausschaut
                //nochmal warum es so sau unschön ist
                //du machst in dem controller JS sachen die wir eigentlich in dataTableHelper ausgelagert haben
                //auserdem js generieren is eigentlich eh hässlich aber im helper sehen wirs ja nich
                // aber dann muss wieder der ganze owner-admin scheiß mit übergeben werden...
                //// ne
                //des läuft doch ganz easy  über die colum config vom helper
                //ich schau mal wie ich es einbauen würde, und ich schau wie schnell es geht so ein link button in den table helper zu bauen ...
                //und dann sag ich dir bescheid . mach ich gleich
                'tableColor' => '#5wt',
                'buttons' => array("print", "copy", "csv", "excel", "pdf") //so halt ca
            );
            return $length.$buttons.$list_select.$dom;
        }
        else return $length;
    }
}