<?php
namespace Auth\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;
use Auth\Model\AuthStorage;

class GoogelCalendarView extends AbstractHelper
{
    /**
     *
     * @var AuthStorage
     */
    protected $storage;
    public $events;

    public function __construct(AuthStorage $storage) {
        $this->storage = $storage;
        $this->events = $this->getServiceLocator()->get('CalendarService');
        return $this;
    }
    public function __invoke()
    {

    }

    public function listEvents($start=NULL, $end =NULL) {
        echo echo json_encode($this->events->getEventsFrom($start, $end));
    }


    /**
     * @param string $kind as "list" or "calendar"
     * @return array
     */
    private function getSettings ($kind = "list") {
        if ($kind == "list" || $kind == "List"){
            /* ***** G-Calendar SETTINGS:  ** */
            // 0= no     1=yes
            $setting = array (
                "Titel" => 0,               // show calendar title in title bar
                "Nav" => 0,                 // show nav in title bar
                "Date" => 0,                // show actual date in title bar
                "Print" => 0,               // show print button in title bar
                "Tabs" => 0,                // show tabs in title bar
                "Calendars" => 0,           // show calendar selection in title bar
                "Tz" => 0,                  // show time zone in title bar
                "mode" => "AGENDA",         // set mode
                "wkst" => 1,                // dunno??
                "bgcolor" => "%23FFFFFF"    // bg color
            );
        // location of timezone
            $timezone = 'ctz=Europe%2FBerlin';
        }
        // settings for iframe  *****general ****
        $iframe_settings = 'style="border-width:1px; border-radius: 10px"
                        width="95%" height="400" frameborder="0" scrolling="no">';

        /* ********* construction parts of google calendar iframe ******/

        $result["first_snippet"] = '<iframe src="https://calendar.google.com/calendar/embed?';
        $setting_string='';             // var for all settings
        $result["src"] = '';            // var for all calendars
        $timezone .= '" ';              // needed for syntax in "last_snippet"
        $result["last_snippet"] = $timezone.$iframe_settings.'</iframe>';

        // calendar settings
        $result["src_1"] = 'src=6h1fqs4om97fvrt8upgrgga1ds%40group.calendar.google.com&amp;color=%23711616&amp;';   // Guest Cal
        $result["src_2"] = 'src=sra_cal%40schwarze-ritter-augsburg.com&amp;color=%231B887A&amp;';                   // Members Cal
        $result["src_3"] = 'src=j0g40fq5m45tt6i3ma30dle0fo%40group.calendar.google.com&amp;color=%232952A3&amp;';   // Vorstand Cal

        // create setting string
        foreach ($setting as $key => $value) {
            if ($key == "mode" || $key == "wkst" || $key == "bgcolor") {
                $setting_string .= $key.'='.$value.'&amp;';
            } else {
                if ($value !== 1) {
                    $setting_string .= 'show'.$key.'='.$value.'&amp;';
                }
            }
        }
        $result["first_snippet"] = $result["first_snippet"].$setting_string;

        return $result;

    }

    /** builds the iframe html string for google calendar
     * Agendastyle
     * @return string html for iframe
     */
    public function googleCaledarList () {

        $set = $this->getSettings("list");
        foreach ($set as $key => $value) {
            $$key = $value;
        }

        $role = $this->storage->getRoleName();
        if ($role == 'Administrator' || $role == 'Vorstand'){
            $rolepower = 3;
        } else if ($role == 'Guest') {
            $rolepower = 1;
        }
        else {
            $rolepower = 2;
        }
        $rolepower = 3; // permission override for testing

        // used calerndar by users permission
        $rolepower++;
        for ($i=1; $i<$rolepower; i++) {
            $call = 'src_'.$i;
            $src .= $$call;
        }

        return $return = $first_snippet.$src.$last_snippet;
    }
}
