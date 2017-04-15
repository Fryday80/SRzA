<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 15.11.2016
 * Time: 09:12
 */
namespace Application\View\Helper;

use Application\DataObjects\Action;
use Application\DataObjects\ActiveUsers;
use Zend\View\Helper\AbstractHelper;


Class DashboardHelper extends AbstractHelper {

    private $sm;

    function __construct( $sm )
    {
        $this->sm = $sm;
    }

    public function render( $data )
    {
        switch ( $data ){
            case ( is_array( $data ) && ( $data[0] instanceof Action ) ): // ActionLog
            case ( ( $data instanceof ActiveUsers ) ):                    // Active Users
            case ( $data == 'SystemLog' ):                                // SystemLog
                return $this->renderData( $data );
                break;
            default:
                return trigger_error( "don't know type of data", E_USER_ERROR );
                break;
        }
    }
    protected function renderData( $data )
    {        
        switch ( $data ) {
            case ( is_array( $data ) && ( $data[0] instanceof Action ) ):
                $return = '<ul id="dashLiveList" class="dash-list">';
                /** @var  $item Action */
                foreach ( $data as $item )
                {
                    $return .= '<li>' . $item->actionType . ' @ ' . date('H:i d.m.Y', $item->time) . ': ' . $item->title . ': ' . $item->msg . ' <span data-timestamp="' . $item->time . '></span></li>';
                }
                $return .= '</ul>';
                return $this->wrapInBox( $return, 'Live Clicks', 'right' );
                break;
            case ( ( $data instanceof ActiveUsers ) ):
                $data = $data->toArray();
                $return = '<ul class="dash-list">';
                foreach ( $data as $row )
                {
                    $userData = ( isset ( $row['user_name'] ) ) ? $row['user_name'] : $row['user_id']; // just til name is provided //@todo check out name of user_id
                    $return .= '<li> User: ' . $userData . ' - last action: ' . date( 'H:i', $row['time'] ) ;
                }
                $return .= '</ul>';
                return $this->wrapInBox( $return, 'Active Users', 'left' );
                break;
        }
    }

    public function wrapInBox( $inside, $named, $float )
    {
        return "<box class = 'dashboard dashboard-$float'>
                    <boxtitel>
                        <span class='own_text_small'>$named</span>
                    </boxtitel>
                    <boxcontent>$inside</boxcontent>
                </box>";
    }
    public function getStyle()
    {
        return'<style>
            .dashboard
            {
                text-shadow: none;
            }
        
            .dashboard-left
            {
                float: left;
                width: 27%;
            }
            .dashboard-right
            {
                float: right;
                width: 67%;
            }
            .dash-list {
                max-height: 200px;
                overflow-y: scroll;
                list-style-type: none
            }
            .dash-list li {
                box-shadow: inset 0px 0px 2vw 0px rgba(41, 0, 0, 0.5);
            }
            @media screen and (max-width: 1350px){
                .dashboard
                {
                    width: 100%;
                    margin-right: 0;
                    float: none !important;
                }
                .dashboard *
                {
                //font-family: normal;
                    font-size: 1.5vw !important;
                }
        
                .dash-list li {
                    box-shadow: inset 0px 0px 2vw 0px rgba(41, 0, 0, 0.5);
                }
            }
            @media screen and (max-width: 800px){
                .dashboard
                {
                    width: 100%;
                    margin-right: 0;
                }
                .dashboard *
                {
                    font-family: normal;
                    font-size: 3vw !important;
                }
        
                .dash-list li {
                    box-shadow: inset 0px 0px 2vw 0px rgba(41, 0, 0, 0.5);
                }
            }
        </style>';
    }

    public function getJs()
    {
        return '<script type="text/javascript">
            $("[data-timestamp]").each(function() {
                //convert time to nice string and push in ele.html
            })
        //script for live ticks
        function livereload() {
            //so und hier müssen wir ürgendwie den neusten timestamp aus der live liste hollen und dann laden wir uns alle neuen
            // actions und prependen die in die liste timestamp ist doch auch Time() in js
            $.ajax({
                url: "/system/json",
                type: "POST",
                data: JSON.stringify({method: "getLiveActions", since: null}),
                complete: function(e) {
                console.log(e);
                setTimeout(livereload, 1500);
            }
            });
        }
        $("<li><span class=\'name\'></span></li>")
        $("#dashLiveList").prepend();
    </script>';
    }
}