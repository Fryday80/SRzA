$(document).ready(function() {
    "use strict";
    var isDetailsOpen = false,
        canEdit = args['canEdit'] || false,
        $details = $('.event-details');

    $details.css({
        position: "absolute",
        top: "0px",
        left: "0px",
        "z-index": 1000,
        display: "none"
    });
    $details.addClass("box");

    function openDetails(event, jsEvent, formData = null) {
        // if (isDetailsOpen) return;
        isDetailsOpen = true;
        $details.css({
            left: jsEvent.originalEvent.pageX - $('#calendar').offset().left,
            top: jsEvent.originalEvent.pageY - $('#calendar').offset().top,
            display: "block"
        });
        if (formData !== null) $('#Event').formPush(formData);
        let relPos = {x:0,y:0};
        let handlerID = $(window).on('mousemove', function moveHandler(e) {
            relPos.x += e.originalEvent.movementX;
            relPos.y += e.originalEvent.movementY;
            console.log(relPos);
            let x = (relPos.x > 0)? Math.max(0, relPos.x - $details.width()): relPos.x;
            let y = (relPos.y > 0)? Math.max(0, relPos.y - $details.height()): relPos.y;
            let distance = Math.sqrt(Math.pow(x, 2) + Math.pow(y, 2));
            let opacity = 1 - (distance / 400);
            $details.css('opacity', opacity);
            if (distance > 300) {
                closeDetails();
                $(window).off('mousemove', null,moveHandler);
            }
        });
    }
    function closeDetails() {
        $details.hide();
        $details.css('opacity', 1);
        isDetailsOpen = false;
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
            // openDetails(event, jsEvent);
            openDetails(event, jsEvent, {
                title: event.title,
                description: event.description,
                allDay: event.allDay,
                startTime: event.start.format('YYYY-MM-DD[T]HH:mm'),
                endTime: event.end.format('YYYY-MM-DD[T]HH:mm'),
            })
        },
        eventMouseout: function(event, jsEvent, view) {
            //closeDetails(event);
        },
        eventClick: function(event, jsEvent, view) {
            // (stayOpenFlag)? closeDetails(event, true): openDetails(event, jsEvent);
        },
        eventResize: function(event, delta, revertFunc) {
            if (!confirm("is this okay?")) {
                revertFunc();
            } else {
                //send update to server
            }
        },
        dayClick: function(date, jsEvent, view, resourceObj) {
            console.log("dayClick");
        },
        selectAllow: function(selectInfo) {
            return canEdit;
        },
        eventDragStart: function(event, jsEvent, ui, view) {},
        eventDragStop: function(event, jsEvent, ui, view) {},
        eventDrop: function(event, delta, revertFunc) {
            if (!confirm("Are you sure about this change?")) {
                revertFunc();
            } else {
                //send update to server
            }
        },
        /**
         * @param start Date
         * @param end Date
         * @param jsEvent Event
         * @param view
         */
        select: function(start, end, jsEvent, view) {
            //open edit overlay
            openDetails(event, jsEvent, {
                startTime: start.format('YYYY-MM-DD[T]HH:mm'),
                endTime: end.format('YYYY-MM-DD[T]HH:mm'),
            })
        },
        unselect: function(view, jsEvent) {
            // console.log("eventDragStart", a,b,c);
        },
    });
    $('#calendar').css({position: 'absolute'});
    $('#calendar').append($details);
});

(function($) {
    "use strict";
    function populateFormData(formEle, data) {
        clearFormData(formEle);
        $('input, select, textarea', formEle).each(function() {
            let name = $(this).attr('name');
            if (data.hasOwnProperty(name) ) {
                $(this).val(data[name]);
            }
        });
    }
    function clearFormData(ele) {
        if ($(ele).is('form')) {
            $('input[type!="submit"], select, textarea', ele).each(function() {
                $(this).val('');
            });
        } else if ($(ele).is('input[type!="submit"], select, textarea') ) {
            $(ele).val('');
        }
    }
    function clearErrors(formEle) {
        $('.form-error-messages', formEle).remove();
    }

    $.fn.formSetErrors = function(errors = {}, clear = true) {
        if (clear) clearErrors(this);
        $('input, select, textarea', this).each(function() {
            let name = $(this).attr('name');
            if (errors.hasOwnProperty(name) && Array.isArray(errors[name]) ) {
                let error = errors[name];
                let $errorUl = $('<ul class="form-error-messages"></ul>');
                $(this).after($errorUl);
                for(let i = 0; i < error.length; i++) {
                    let $li = $errorUl.append('<li>'+ error[i] +'</li>');
                    $errorUl.append($li);
                }
            }
        });
        return this;
    };
    $.fn.formClearErrors = function() {
        clearErrors(this);
        return this;
    };
    $.fn.formPush = function(data) {
        if (!this.is('form')) return this;
        populateFormData(this, data);
        return this;
    };
    /**
     * returns object of form data. properties are the name attributes value
     * @returns {*}
     */
    $.fn.formPull = function() {
        if (!this.is('form')) return this;
        let data = this.serializeArray(),
            beautyData = {};
        for(let i = 0; i < data.length; i++) {
            beautyData[data[i].name] = data[i].value;
        }
        return beautyData;
    };
    /**
     * clear form data from <form>, <input> or <select>
     * @param elementName only if it's called on a <form>. Removes only the data from the <input> or <select> with the elementName in the name attribute
     * @returns {jQuery}
     */
    $.fn.formClear = function(elementName = null) {
        if ($(this).is('form')) {
            if (elementName === null) {
                clearFormData(this);
            } else {
                clearFormData($('input[name="'+elementName+'"], select[name="'+elementName+'"], textarea[name="'+elementName+'"]'));
            }
        } else if ($(this).is('input, select, textarea')) {
            clearFormData(this);
        }
        return this;
    };
})(jQuery);