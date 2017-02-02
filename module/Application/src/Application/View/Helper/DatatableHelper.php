<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 15.11.2016
 * Time: 09:12
 */
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Application\Utility\DataTable;


Class DataTableHelper extends AbstractHelper {

    protected $view;
    function __construct($sm)
    {
        $this->view = $sm->get('viewhelpermanager')->get('basePath')->getView();
        $this->view->headLink()->appendStylesheet($this->view->basePath('/libs/datatables/datatables.min.css'));
        $this->view->headScript()->prependFile($this->view->basePath('/libs/datatables/datatables.min.js'));
    }
    //so
    /**
     * @param DataTable $table
     */
    public function render($table) {
        //@todo check $table
        if (!($table instanceof DataTable)) {
            trigger_error('argument 1 is not a instance of DataTable', E_USER_ERROR);
        }
        $table->prepare();
        echo $this->renderHTML($table);
        echo $this->renderJS($table->getSetupString());
    }

    /**
     *
     * @param DataTable $table
     * @return string
     */
    protected function renderHTML($table) {
        $datarow = '';
        $datahead = '';
        $i = 0;
        foreach ($table->data as $row) {
            $datarow .= '<tr>';
            foreach ($table->columns as $number => $value){
                $switch = is_object($row);
                switch ($switch) {
                    case false:
                        $datarow .= "<td>";
                        if ($i == 0) {
                            $datahead .= '<th>' . $value['label'] . '</th>';
                        }
                        switch ($value['type']) {
                            case 'text':
                                $datarow .= $row[$value['name']];
                                break;
                            case 'custom':
                                $datarow .= $value['render']($row);
                                break;
                            case '':
                                break;
                        }
                    break;
                    case true:
                        $datarow .= "<td>";
                        if ($i == 0) {
                            $datahead .= '<th>' . $value['label'] . '</th>';
                        }
                        switch ($value['type']) {
                            case 'text':
                                $datarow .= $row->$value['name'];
                                break;
                            case 'custom':
                                $datarow .= $value['render']($row);
                                break;
                            case '':
                                break;
                        }
                        break;
                }
                $datarow .= "</td>";
            }
            $datarow .= '</tr>';
            $i++;
        }
        $html = '<br><table class="display" cellspacing="0" width="100%">';
        $html .= "<thead><tr> $datahead </tr></thead>";
        $html .= "<tfoot><tr> $datahead </tr></tfoot><tbody>";
        $html .= $datarow;
        $html .= '</tbody></table>';
        return $html;
    }
    public function renderJS($jsOptionString) {
        $js = '<script>';
        $js .= '$(".display").DataTable(';
        $js .= $jsOptionString;
        $js .= ');';
        $js .= '</script>';
        return $js;
    }

}