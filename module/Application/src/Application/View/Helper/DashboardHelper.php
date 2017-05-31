<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 15.11.2016
 * Time: 09:12
 */
namespace Application\View\Helper;

use Application\Model\DataObjects\Action;
use Application\Model\DataObjects\ActionLogSet;
use Application\Model\DataObjects\ActiveUsersSet;
use Application\Model\DataObjects\BasicDashboardDatas;
use Application\Model\DataObjects\DashboardDataCollection;
use Application\Model\DataObjects\SystemLogSet;
use Zend\View\Helper\AbstractHelper;


Class DashboardHelper extends AbstractHelper {

    private $sm;

    function __construct( $sm )
    {
        $this->sm = $sm;
    }

    public function render( $data )
    {
        if (($data instanceof DashboardDataCollection)){
            $return = $this->renderData($data->getActionLog());
            $return .= $this->renderData($data->getSystemLog());
            $return .= $this->renderData($data->getActiveUsers());
            return $return;
        }
        else {
            switch ($data) {
                case (($data instanceof BasicDashboardDatas)):
                    return $this->renderData($data);
                    break;
                default:
                    return trigger_error("don't know type of data", E_USER_ERROR);
                    break;
            }
        }
    }
    protected function renderData( $dataObject )
    {
        if ($dataObject == null) return '';
        switch ( $dataObject ) {
            case ( ( $dataObject instanceof ActionLogSet ) ):
                $return = '<ul id="dashLiveList" class="dash-list">';
                /** @var  $item Action */
                foreach ( $dataObject->data as $item )
                {
                    $return .= '<li class="entry basicdata" data-timestamp="' . $item->time . '" data-id="' . $item->actionID . '">' . $item->actionType . ' @ ' . date('H:i d.m.Y', $item->time) . ': ' . $item->title . ': ' . $item->msg . '</li>';
                }
                $return .= '</ul>';
                return $this->wrapInBox( $return, 'Live Clicks', 'right' );
                break;
            case ( ( $dataObject instanceof ActiveUsersSet ) ):
                $data = $dataObject->toArray();
                $return = '<ul class="dash-list">';
                foreach ( $data as $row )
                {
                    $userData = ( isset ( $row['user_name'] ) ) ? $row['user_name'] : $row['user_id']; // just til name is provided //@todo check out name of user_id
                    $return .= '<li> User: ' . $userData . ' - last action: ' . date( 'H:i', $row['time'] ) ;
                }
                $return .= '</ul>';
                return $this->wrapInBox( $return, 'Active Users', 'left' );
                break;
            case ( ( $dataObject instanceof SystemLogSet ) ):
                return null;
                break;

        }
    }

    // turn private when finished
    public function wrapInBox( $inside, $named, $float )
    {
        return "<box class = 'dashboard dashboard-$float'>
                    <boxtitle>
                        <span class='own_text_small'>$named</span>
                    </boxtitle>
                    <boxcontent>$inside</boxcontent>
                </box>";
    }
}