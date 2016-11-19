<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 15.11.2016
 * Time: 09:12
 */
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\HeadLink;
use Zend\View\Helper\HeadScript;


Class DataTableHelper extends AbstractHelper {

    protected $view;
    public $additionalButtons = array();
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
                //if ($key == 'Aktionen') {
                //    $value = $this->operationsToLink($value, $row['id']);
                //}
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

    function renderForm($form)
    {
        $datarow = '';

        foreach ($form as $element) {
            $label = '';
            if ($element->getLabel() !== NULL) {
                $label = $element->getLabel();
            }
            $input = $this->view->formElement($element);
            $datarow .= '<tr>';
            $datarow .= "<td>$label</td>";
            $datarow .= "<td>$input</td>";
            $datarow .= '</tr>';
        }
        $table = "<table class=\"display\" cellspacing=\"0\" width=\"100%\">
                    <thead> <tr> <th>Label</th> <th>Input</th> </tr> </thead>
                    <tfoot> <tr> <th>Label</th> <th>Input</th> </tr> </tfoot> <tbody>";
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
            return $startScript . '"lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],' . $endScript;
        }
        if ($this->allowance == 'editor' || $this->allowance == 'self') {
            $tableScript  = '   "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
                                buttons: [
                                        "print", "copy", "csv", "excel", "pdf"
                                ],
                                select: {
                                    style: "multi"
                                },
                                dom: "lfiBrtp",';
            return $startScript.$tableScript.$endScript;
            //https://datatables.net/reference/index for preferences
        }
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