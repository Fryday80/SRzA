<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 15.11.2016
 * Time: 09:12
 */
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Application\Utility\TablehelperConfig;


Class DataTableHelper extends AbstractHelper {

    protected $view;
    
    public $additionalButtons = array();
    public $jsOptions = false;
    private $configObject = false;
    


    function __construct($sm)
    {
        $this->view = $sm->get('viewhelpermanager')->get('basePath')->getView();
        $this->view->headLink()->appendStylesheet($this->view->basePath('/libs/datatables/datatables.min.css'));
        $this->view->headScript()->prependFile($this->view->basePath('/libs/datatables/datatables.min.js'));
    }

    /**
     * creates HTML table and js script
     * @param $data array
     * @param false|empty|object $configObject
     * @param false|array $colums
     * @return string
     */
    function render($data, $configObject = false, $colums = false)
    {
        $this->jsConfigManager($configObject);
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
        $table .= $this->getTableScript();
        return $table;
    }


    /**
     * creates the $colums variable if nothing given <br>
     * => shows all colums
     *
     * @param $data array
     * @return array
     */
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
     * returns HTML js script for datatables helper
     * @return string HTML string of datatables js script
     */
    private function getTableScript ()
    {
        $startScript = '<script>  $(".display").DataTable( ';
        $endScript   = ' ); </script>';
        return $startScript . $this->jsOptions . $endScript;
            //https://datatables.net/reference/index for preferences/documentation
    }

    /**
     * sets the js options for the js script, <br>
     * creates a new object if none given
     * @param epty|false|object $configObject
     */
    private function jsConfigManager ($configObject = false){
        switch ($configObject){
            case false:
            case '':
                $this->configObject = new TablehelperConfig();
                break;
            case true:
                $this->configObject = $configObject;
        }
        $this->jsOptions = $this->configObject->getSetupString();
    }
}