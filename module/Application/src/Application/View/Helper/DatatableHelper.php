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
    public $jsOptions = '"lengthMenu": [ [25, 10, 50, -1], [25, 10, 50, "All"] ],';


    function __construct($sm)
    {
        $this->view = $sm->get('viewhelpermanager')->get('basePath')->getView();
        $this->view->headLink()->appendStylesheet($this->view->basePath('/libs/datatables/datatables.min.css'));
        $this->view->headScript()->prependFile($this->view->basePath('/libs/datatables/datatables.min.js'));
    }

    function render($data, $colums = false)
    {
            $datarow = '';
            $datahead = '';
            $datafoot = '';
            $col = 0;
            if (!$colums){
                $colums = $this->fallback_Config($data);
            }

            foreach ($colums as $colum => $setting){
                $datahead .= '<th>' . $colums[$colum]['headline'] . '</th>';
                $datafoot .= (isset($colums[$colum]['footline'])) ? '<th>' . $colums[$colum]['headline'] . '</th>' : '<th>' . $colums[$colum]['headline'] . '</th>';
                $col++;
            }
            foreach($data as $row){
                $datarow .= '<tr>';
                for ($i = 0; $i<$col; $i++){
                    $key = $colums[$i]['key'];
                    $datarow .= '<td>' . $row[$key] . '</td>';
                }
                $datarow .= '</tr>';
            }

            $table = "<table class=\"display\" cellspacing=\"0\" width=\"100%\">
                        <thead> <tr> $datahead </tr> </thead>
                        <tfoot> <tr> $datafoot </tr> </tfoot> <tbody>";
            $table .= $datarow;
            $table .= '</tbody> </table>';
            $table .= $this->getTableScript($this->jsOptions);
            return $table;
    }


    private function fallback_Config ($data){
        $i = 0;
        $colums = array();
        foreach ($data[0] as $key => $value){
            $colums[$i]['key'] = $key;
            $colums[$i]['headline'] = $key;
            $colums[$i]['footline'] = $key;
            $i++;
        }
        return $colums;
    }

    /**
     * @param $options string
     * @return string of datatables js script
     */
    public function getTableScript ($options=false)
    {
        $options = ($options)?:$this->jsOptions;
        $startScript = '<script>  $(".display").DataTable( {';
        $endScript   = '} ); </script>';
        return $startScript . $options . $endScript;
            //https://datatables.net/reference/index for preferences/documentation
    }

    /**
     * @param $jsOptions string overrides default settings for datatables js script
     *
     * for documentation -> https://datatables.net/reference/index for preferences/documentation
     */
    public function setJSOptions($jsOptions)
    {
        $this->jsOptions = $jsOptions;
    }
}