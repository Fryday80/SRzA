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
    function __construct($sm)
    {
        $this->view = $sm->get('viewhelpermanager')->get('basePath')->getView();
        $this->view->headLink()->appendStylesheet($this->view->basePath('/libs/datatables/datatables.min.css'));
        $this->view->headScript()->prependFile($this->view->basePath('/libs/datatables/datatables.min.js'));
    }
    public function render($table) {
        echo $this->renderHTML($table);
        echo $this->renderJS($table);
    }
    protected function renderHTML($table) {
        $datarow = '';
        $datahead = '';
        $i = 0;
        foreach ($table->data as $row) {
            $datarow .= '<tr>';
            foreach ($table->columns as $name => $value){
                $datarow .= "<td>";
                switch($value['type']) {
                    case 'text':
                        $datarow .= $row[$value['dataIndex']];
                        break;
                    case 'custom':
                        $datarow .= $value['render']($row);
                        break;
                    case '':
                        break;
                }
                $datarow .= "</td>";
            }
            $datarow .= '</tr>';
            $i++;
        }
        $html = '<table class="display" cellspacing="0" width="100%">';
        $html .= "<thead><tr> $datahead </tr></thead>";
        $html .= "<tfoot><tr> $datahead </tr></tfoot><tbody>";
        $html .= $datarow;
        $html .= '</tbody></table>';
        return $html;
    }
    protected function renderJS($table) {
        $js = '<script>';
        $js .= '$(".display").DataTable( {';
        $js .= '});';
        $js .= '</script>';
        return $js;
    }
}