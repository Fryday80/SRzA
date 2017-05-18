$(document).ready(function() {
    "use strict";
    var isDetailsOpen = false,
        canEdit = args['canEdit'] || false,
        canAdd = args['canAdd'] || false,
        canDelete = args['canDelete'] || false,
        $details = $('.event-details'),
        detailsState = 'closed';//closed, preview, edit, add

    $details.css({
        position: "absolute",
        top: "0px",
        left: "0px",
        "z-index": 1000,
        display: "none"
    });
    $details.addClass("box");


    function pushPreviewDetails(formData) {
        var stt, ett;
        var format = 'DD[.]MM[.]YYYY';
        stt = new moment(formData['startTime']);
        ett = new moment(formData['endTime']);
        formData['date'] = (stt.format(format) == ett.format(format))? stt.format(format) : stt.format(format) + ' - ' + ett.format(format);
        formData['time'] = stt.format('HH:mm') + ' - ' + ett.format('HH:mm');
        $(".preview .wrapper .item").each( function() {
            $(this).html(formData[$(this).attr('name')]);
        });
    }

    function openDetails(event, jsEvent, formData = null, mode = 'preview') {
        // if (isDetailsOpen) return;
        setDetailsMode(mode);
        isDetailsOpen = true;
        $details.css({
            left: jsEvent.originalEvent.pageX - $('#calendar').offset().left,
            top: jsEvent.originalEvent.pageY - $('#calendar').offset().top,
            display: "block",
            opacity: 1.0
        });
        if (formData !== null) $('#Event').formPush(formData);
        pushPreviewDetails(formData);

        $(window).on('mousemove', function moveHandler(e) {

            let bBox = $details.get(0).getBoundingClientRect();
            let X = e.originalEvent.clientX - bBox.left;
            let Y = e.originalEvent.clientY - bBox.top;

            let x = (X > 0)? Math.max(0, X - bBox.width): X;
            let y = (Y > 0)? Math.max(0, Y - bBox.height): Y;
            let distance = Math.sqrt(Math.pow(x, 2) + Math.pow(y, 2));
            let opacity = 1 - (distance / 400);
            $details.css('opacity', opacity);
            if (distance > 300) {
                closeDetails();
                $(window).off('mousemove', null, moveHandler);
            }
        });
    }
    function closeDetails() {
        setDetailsMode('closed');
        // $details.hide();
        $details.css('opacity', 1.0);
        isDetailsOpen = false;
    }

    function changeEvent(e, form, state) {
        let url;
        let formData = form.formPull();
        e.preventDefault();

        switch(state){
            case 'delete':
                url = "/calendar/deleteEvent";
                break;
            case 'add':
                url = "/calendar/addEvent";
                break;
            case 'edit':
                url = "/calendar/editEvent";
                break;
        }
        let promise = $.ajax({
            url: url,
            type: "POST",
            data: JSON.stringify(formData)
        });

        promise.fail(function(jqXHR, textStatus, errorThrown) {
            //@todo handle error
            console.log(errorThrown);
            console.error(textStatus);
            //@todo remove load animation and show element
        });
        promise.done(function(e, textStatus, jqXHR) {
            //@todo on error is not decoded
            //e = JSON.parse(e);
            if (e.error) {
            //     if (e.code == 1) {
            //         removeAllCharFormErrors();
            //         // $('#Character input[name="id"]').val(char.id);
            //         let errors = e.formErrors;
            //         if (errors.name) $('#Character input[name="name"]').parent('label').after('<ul><li>'+ errors.name.isEmpty +'</li></ul>');
            //
            //         // $('#Character input[name="name"]').parent('label').next().val(char.name);
            //         if (errors.surename) $('#Character input[name="surename"]').parent('label').after('<ul><li>'+ errors.surename.isEmpty +'</li></ul>')
            //         // $('#Character input[name="gender"]').val([char.gender]);
            //         // $('#Character input[name="birthday"]').val(char.birthday);
            //         // $('#Character input[name="vita"]').val(char.vita);
            //
            //
            //
            //
            //         // $('#Character select[name="family_id"]').val(parseInt(char.family_id));
            //         // $('#Character select[name="tross_id"]').val(parseInt(char.tross_id));
            //         // $('#Character select[name="job_id"]').val(parseInt(char.job_id));
            //     }
            // } else {
            //     switch (e.code) {
            //         case 200:
            //             //saved
            //             let char = getCharByID(id);
            //             characters[characters.indexOf(char)] = e.data;
            //             break;
            //         case 201:
            //             //new created
            //             characters.push(e.data);
            //             createCharElement(e.data);
            //             break;
            //     }
            //     scrollToCharSelect();
            //     hideCharForm();
            }
            $('#calendar').fullCalendar( 'removeEvents', formData['id']);
            //@todo remove load animation and show element
        });
    }

    function confirmDelete(e, form, mode = 'delete') {
        var formData = form.formPull();
        console.log();
        let confirm = ('<div class="delete-pop-up" title="' + formData['title'] + '">Event wirklich löschen?</div>');
        $(confirm).dialog({
            left: '50%',
            right: '50%',
            buttons: {
                ja: function(){
                    $(this).dialog('close');
                    changeEvent(e, form, mode);
                },
                nein: function(){ $(this).dialog('close') }
            }
        });
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
        firstDay: 4,
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
            // hier kann man ihm doch noch ein "edit" mitgeben
            // openDetails(event, jsEvent);
            console.log(event);
            openDetails(event, jsEvent, {
                id: event.id,
                title: event.title,
                description: event.description,
                allDay: event.allDay,
                startTime: event.start.format('YYYY-MM-DD[T]HH:mm'),
                endTime: event.end.format('YYYY-MM-DD[T]HH:mm'),
            });
        },
        eventMouseout: function(event, jsEvent, view) {
            //closeDetails(event);
        },
        eventClick: function(event, jsEvent, view) {
            openDetails(event, jsEvent, {
                id: event.id,
                title: event.title,
                description: event.description,
                allDay: event.allDay,
                startTime: event.start.format('YYYY-MM-DD[T]HH:mm'),
                endTime: event.end.format('YYYY-MM-DD[T]HH:mm'),
            }, 'preview');
        },
        eventResize: function(event, delta, revertFunc) {
            if (!confirm("is this okay?")) {
                revertFunc();
            } else {
                //send update to server
            }
        },
        selectAllow: function(selectInfo) {
            return canAdd;
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
            }, 'add');
        },
        unselect: function(view, jsEvent) {
            // console.log("eventDragStart", a,b,c);
        },
    });
    $('#calendar').css({position: 'absolute'});
    $('#calendar').append($details);
    
    function setDetailsMode(mode) {
        switch(mode) {
            case 'preview':
                $details.attr('state', 'preview');
                break;
            case 'edit':
                $details.attr('state', 'edit');
                break;
            case 'add':
                $details.attr('state', 'add');
                break;
            case 'closed':
                $details.attr('state', 'closed');
                break;
        }
    }
    
    $('.event.edit-btn').on('click', function(){
        setDetailsMode('edit');
    });
    $('#Event').submit(function(e) {
        changeEvent( e, $('#Event'), $('.box.event-details').attr('state') );
    });
    $('.event.delete-btn').on('click', function(e){
        confirmDelete(e, $('#Event'), 'delete');
    });
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