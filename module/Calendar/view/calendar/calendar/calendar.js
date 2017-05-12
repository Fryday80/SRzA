$(document).ready(function() {
    var stayOpenFlag = false,
        $details = $('<div>details</div>');

    $details.css({
        position: "absolute",
        top: "0px",
        left: "0px",
        "z-index": 1000,
        width: "100px",
        height: "100px",
        display: "none"
    });
    $details.addClass("box");

    console.log(args);

    function openDetails(event, jsEvent, stayOpen = false) {
        $details.css({
            left: jsEvent.originalEvent.pageX - $('#calendar').offset().left,
            top: jsEvent.originalEvent.pageY - $('#calendar').offset().top,
            display: "block"
        });
        if (stayOpen) stayOpenFlag = true;
    }
    function closeDetails(event, force = false) {
        if (stayOpenFlag && !force) return;
        stayOpenFlag = false;
        $details.hide();
    }
    $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'listYear,listMonth,month,agendaWeek,agendaDay'
        },
//            defaultDate: '2014-06-12',
        defaultView: 'month',
        editable: true,
        theme: true,
        selectable: true,
        eventSources: [
            {
                url: '/calendar/getEvents',
                type: 'POST',
                data: {
//                        custom_param1: 'something',
//                        custom_param2: 'somethingelse'
                },
                error: function() {
                    alert('there was an error while fetching events!');
                },
                color: 'yellow',   // a non-ajax option
                textColor: 'black' // a non-ajax option
            }
        ],
        //event handler
        eventMouseover: function(event, jsEvent, view) {
            openDetails(event, jsEvent);
        },
        eventMouseout: function(event, jsEvent, view) {
            closeDetails(event);
        },
        eventClick: function(event, jsEvent, view) {
            (stayOpenFlag)? closeDetails(event, true): openDetails(event, jsEvent, true);
        },
        eventDrop: function(event, delta, revertFunc) {
            if (!confirm("Are you sure about this change?")) {
                revertFunc();
            } else {
                //send update to server
            }
        },
        eventResize: function(event, delta, revertFunc) {
            if (!confirm("is this okay?")) {
                revertFunc();
            } else {
                //send update to server
            }
        },
        /**
         * @param date Date
         * @param jsEvent
         * @param view
         * @param resourceObj
         */
        dayClick: function(date, jsEvent, view, resourceObj) {

//                console.log(jsEvent, view);
        },
        selectAllow: function(selectInfo) {
//                console.log(selectInfo);
        }
    });
    $('#calendar').css({position: 'absolute'});
    $('#calendar').append($details);
//        $('#calendar').on("contextmenu", function(e) {
//            console.clear();
//            console.log("open menu");
//            e.preventDefault();
//            return false;
//        });
});