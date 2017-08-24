<?php
namespace Application\View\Helper;

use Application\Model\AbstractModels\DataTableAbstract;


Class DataTableHelper extends MyViewHelper {

	protected $jsFiles  = array ('/libs/datatables/datatables.min.css');
	protected $cssFiles = array ('/libs/datatables/datatables.js');

	/**
	 * @param DataTableAbstract $table
	 *
	 * @return string HTML string
	 */
    public function render(DataTableAbstract $table) {
        $table->prepare();
        $return  = $this->renderHTML($table);
		$return .= $this->renderJS($table->getSetupString());
		return $return;
    }

    /**
     *
     * @param DataTableAbstract $table
     * @return string
     */
    protected function renderHTML($table) {
        $datarow = '';
        $datahead = '';
        $i = 0;

        if ($table->data !== null && !empty($table->data)) {
            foreach ($table->data as $row) {
                $datarow .= '<tr>';

                foreach ($table->columns as $number => $value) {
                    $datahead .= ($i == 0) ? '<th>' . $value['label'] . '</th>' : '';
                    $datarow .= "<td>";

                    switch ($value['type']) {
                        case 'text':
                            if ( is_object($row) ) {
                                $key = $value['name'];
                                $datarow .= (isset($row->$key)) ? $row->$key : '' ;
                            } else {
                                $datarow .= (isset($row[$value['name']])) ? $row[$value['name']] : '' ;
                            }
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
        } else {
            if ($table->columns !== null) {
                $columnsCount = 0;
                foreach ($table->columns as $number => $value) {
                    $datahead .= ($i == 0) ? '<th>' . $value['label'] . '</th>' : '';
                    $columnsCount++;
                }
                $datarow = '<tr>';
                for ($ic = 0; $ic < $columnsCount; $ic++) {
                    $datarow .= '<td>no data</td>';
                }
                $datarow .= '</tr>';
            } else {
                $datarow = '<tr><td>no data and no column config</td></tr>';
                $datahead = '<th>no data and no column config</th>';
            }
        }

        return '<br><table class="display" cellspacing="0" width="100%">' .
                "<thead><tr>$datahead</tr></thead>" .
                "<tfoot><tr>$datahead</tr></tfoot>" .
                "<tbody>$datarow</tbody></table>";
    }

    public function renderJS($jsOptionString) {
        return '<script>' .
                    "$('.display').DataTable( $jsOptionString )" .
                '</script>';
    }
}