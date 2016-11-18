<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 15.11.2016
 * Time: 09:12
 */
namespace Usermanager\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\HeadLink;
use Zend\View\Helper\HeadScript;


Class DatatableHelper extends AbstractHelper {

    protected $view;
    public $controller;
    public $allowance = 'not set';


    function __construct($sm)
    {
        $this->view = $sm->get('viewhelpermanager')->get('basePath')->getView();
        $this->view->headLink()->appendStylesheet($this->view->basePath('/libs/datatables/datatables.min.css'));
        $this->view->headScript()->prependFile($this->view->basePath('/libs/datatables/datatables.min.js'));
    }

    function render($data, $hidden_array)
    {
        $datarow = '';
        $datahead = '';
        $i = 0;

        foreach ($data as $row) {
                $datarow .= '<tr>';
            foreach ($row as $key => $value){
                if (in_array($key, $hidden_array)){continue;}
                if ($key == 'Aktionen') {
                    $value = $this->operationsToLink($value, $row['id']);
                }
                if ( $i == 0) {
                    $datahead .= "<td>$key</td>"; 
                }

                $datarow .= "<td>$value</td>";
            }
            $datarow .= '</tr>';
            $i++;
        }
        $table = "<table class=\"display\" cellspacing=\"0\" width=\"100%\">
                    <thead> <tr> $datahead </tr> </thead>
                    <tfoot> <tr> $datahead </tr> </tfoot> <tbody>";
        $table .= $datarow;
        $table .= '</tbody> </table>';
        $table .= $this->getTableScript();
        return $table;
    }

    private function getTableScript ($options = array ())
    {
        $startScript = '<script>
                        $(".display").DataTable( {';
        $endScript   = '} );
                        </script>';

        if ($this->allowance == 'not set') {
            return $startScript.$endScript;
        }
        if ($this->allowance == 'editor' || $this->allowance == 'self') {
            $tableScript  = '   dom: "lfiBrtp",
                                buttons: [
                                        "print", "copy", "csv", "excel", "pdf"
                                ],
                                select: {
                                    style: "multi"
                                }';
            return $startScript.$tableScript.$endScript;
        }
    }

    public function addButton ($action, $label, $link_array = array())
    {
        if (!isset ($this->controller)) {dumpd ('You need to setController($controller) first!!', 'ERROR, 1');}

        $addUserButton = '';
        if ($this->allowance == 'editor' || $this->allowance == 'self' || $this->allowance == 'not set'){
            $addUserButton = '<div>
                                <br>
                                <button><a href="';
            $addUserButton .= $this->view->url("$this->controller/$action", $link_array);
            $addUserButton .= '">' . $label . '</a></button>
                                <br><br>
                            </div>';
        }
        return $addUserButton;
    }
    public function operationsToLink ($data_set, $id)
    {
        $return = '';
        foreach ($data_set as $action => $label){
            $return .= '<a href="';
            $return .= $this->view->url("$this->controller/$action", array ('id' => $id));
            $return .= '">' . $label . '</a> ';
        }
        return $return;
    }

    public function setController($controller)
    {
        $this->controller = $controller;
    }

    public function setAllowance($allowance)
    {
        $this->allowance = $allowance;
    }

    public function getController()
    {
        return $this->controller;
    }

    public function getAllowance()
    {
        return $this->allowance;
    }
}