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

    private $sm;

    function __construct($sm)
    {
        $vHM =  $this->sm->get('viewhelpermanager');
        $headLink = $vHM->get('headLink');
        $headScript = $vHM->get('headLink');
        $basePath = $vHM->get('basePath');
        $headLink->appendStylesheet($basePath->setBasePath('/libs/datatables/datatables.min.css')); //salt das $this->basePath ('blabla') bekomme ich hier noch nicht hin
        $headScript->prependFile($basePath->setBasePath('/libs/datatables/datatables.min.js'));
    }
    
    function render($data)
    {
        dumpd ($this->sm->getRegisteredServices());
        $datarow = '';
        $datahead = '';
        $i = 0;

        foreach ($data as $row) {
                $datarow .= '<tr>';
            foreach ($row as $key => $value){
                if( $i == 0) {
                    $datahead .= "<td>$key</td>"; 
                }
                $datarow .= "<td>$value</td>";
            }
            $datarow .= '</tr>';
            $i++;
        }
        $table = "<table class=\"display\" cellspacing=\"0\" width=\"100%\"><thead><tr>$datahead</tr></thead>";
        $table .= "<tfoot><tr>$datahead</tr></tfoot><tbody>";
        $table .= $datarow;
        $table .= '</tbody></table>';
        $table .= '<script>';
        $table .= '$(".display").DataTable( {';
        $table .= '    dom: "Bfrtip",';
        $table .= '    buttons: [';
        $table .= '    "copy", "excel", "pdf"';
        $table .= ']';
        $table .= '} );';
        $table .= '</script>';
        return $table;
    }
}