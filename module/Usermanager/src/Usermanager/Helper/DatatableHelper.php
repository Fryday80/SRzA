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


    function __construct($sm)
    {
        //dumpd ($this->sm->getRegisteredServices());
        //dumpd (get_class_methods($basePath));
        //dumpd ($basePath->getView()->basePath('/bla/bla/blub'));
        $vHM =  $sm->get('viewhelpermanager');
        $headLink = $vHM->get('headLink');
        $headScript = $vHM->get('headScript');
        $basePath = $vHM->get('basePath');
        $headLink->appendStylesheet($basePath->getView()->basePath('/libs/datatables/datatables.min.css'));
        $headScript->prependFile($basePath->getView()->basePath('/libs/datatables/datatables.min.js'));
    }
    
    function render($data, $allowance='')
    {
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
        $table = "<table class=\"display\" cellspacing=\"0\" width=\"100%\">
                    <thead> <tr> $datahead </tr> </thead>
                    <tfoot> <tr> $datahead </tr> </tfoot> <tbody>";
        $table .= $datarow;
        $table .= '</tbody> </table>';
        $table .= $this->getTableScript(array ('allowance' => $allowance));
        return $table;
    }

    private function getTableScript ($options){
        $startScript = '<script>
                        $(".display").DataTable( {';
        $endScript   = '} );
                        </script>';

        if ($options['allowance'] == '') {
            return $startScript.$endScript;
        }
        if ($options['allowance'] == 'editor' || $options['allowance'] == 'self') {
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
}