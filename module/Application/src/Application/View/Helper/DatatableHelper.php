<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 15.11.2016
 * Time: 09:12
 */
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;


Class DataTableHelper extends AbstractHelper {

    protected $view;
    public $additionalButtons = array();
    public $jsOptions = '';


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
        $table .= $this->getTableScript($this->jsOptions);
        return $table;
    }

    private function getTableScript ($options = '')
    {
        $startScript = '<script>  $(".display").DataTable( {';
        $endScript   = '} ); </script>';
        return $startScript . $options . $endScript;
/*                      //cleanfix
        if ($this->allowance == 'not set') {
            return $startScript . '"lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],' . $endScript;
        }
        if ($this->allowance == 'editor' || $this->allowance == 'self') {
            $tableScript  = '   "lengthMenu": [ [25, 10, 50, -1], [25, 10, 50, "All"] ],
                                buttons: [
                                        "print", "copy", "csv", "excel", "pdf"
                                ],
                                select: {
                                    style: "multi"
                                },
                                dom: "lfBrtip",';
            return $startScript.$tableScript.$endScript;
*/
            //https://datatables.net/reference/index for preferences/documentation
    }

    public function setJSOptions($jsOptions)
    {
        $this->jsOptions = $jsOptions;
    }
}