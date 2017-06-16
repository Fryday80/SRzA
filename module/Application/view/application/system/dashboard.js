//live ticks
(function () {
    "use strict";
    //script for live ticks
    function loadLive(e) {
        e.fail(function(jqXHR, textStatus, errorThrown) {
            console.error(jqXHR.responseJSON.msg);
        });
        e.done(function(e, textStatus, jqXHR) {
            let actions = e.actions;
            if (actions !== null) {
    //                actions.reverse();
                for (let i = 0; i < actions.length; i++) {
    //                    var dateRaw = new Date (actions[i].time*1000);
    //                    var hours = '0' + dateRaw.getHours();
    //                    var minutes = '0' + dateRaw.getMinutes();
    //                    var date = hours.substr(-2) + ':' + minutes.substr(-2);
                    $('#dashLiveList').prepend("<li class='entry' data-timestamp='" + actions[i].microtime + "'>" +
                        actions[i].dateTime + ": " + actions[i].userName + " ...called: " + actions[i].url + "</li>");
                }
            }
            setTimeout(function() {
                let ele =  $('#dashLiveList li:nth-child(1)');
                let since = ele.data('timestamp');
                livereload(since);
            }, 4500);
        });
    }
    function livereload(since) {
        let data = {
            method: "getLiveActions",
            since: since,
        };
        $.ajax({
            url: "/system/json",
            type: "POST",
            data: JSON.stringify(data),
            complete: loadLive,
        });
    }
    livereload( $('#dashLiveList li:nth-child(1)').data('timestamp'), 0);
})();
//
(function () {
    "use strict";
    //script for live ticks
    function loadActive(e) {
        e.fail(function(jqXHR, textStatus, errorThrown) {
            console.error(jqXHR.responseJSON.msg);
        });
        e.done(function(e, textStatus, jqXHR) {
            let user = e.users;
            // active users
            if (user !== null) {
                // user.reverse();
                for (let c = 0; c < user.length; c++){
                    // remove updated user
                    if (user[c].userId == $('li[data-userId="' + user[c].userId + '"]').data('userId') ) {
                        if (user[c].microtime == $('li[data-microtime="' + user[c].microtime + '"]').data('microtime')) continue;
                    }
                    $('li[data-firstCall="' + user[c].firstCall + '"]').remove();
                    // prepend updated user
                    $('#users').prepend("<li class='entry' " +
                        "data-userId='" + user[c].userId +
                        "' data-microtime='" + user[c].microtime +
                        "' data-firstCall='" + user[c].firstCall +
                        "'>" +
                        user[c].userName + ": " + user[c].url + "<b> @ </b>" + user[c].dateTime + "</li>");
                }
            }
            setTimeout(function() {
                let elm = $('#users li:nth-child(1)');
                let microtime = elm.data('microtime');
                activereload(microtime);
            }, 4500);
        });
    }
    function activereload(microtime) {
        let data = {
            method: "getActiveUsers",
            microtime: microtime,
        };
        $.ajax({
            url: "/system/json",
            type: "POST",
            data: JSON.stringify(data),
            complete: loadActive
        });
    }
    activereload( 1 );
})();
//
(function(){
    var html = $('.dashboard.systemLog boxcontent').html();;
    // function closefunction(){
    //
    // }
    $('.systemLog').on('click', function(){
        // html = $('.dashboard.systemLog boxcontent').html();
        // html = $('#sysTable').html();
        $('<div id="pop" title="System Log"></div>').dialog({
            // close: closefunction,
            width: "100%",
            // top: "30%",
        });
        $('#pop').append(html);
        $('#pop ul').css('max-height', '60vh');
    });
})();
//system config control
(function () {
    "use strict";
    //scan system vars and register handler
    var $systemPanel = $('.systemPanel'),
        $cachePanel = $('.cachePanel');

    $("button", $systemPanel).on('click', function(e) {
        let type = $(this).data('type');
        if (type === 'function') {
            let valueName = $(this).attr('name');
            //handle function
            console.log(valueName, type);
            setSystemConfig(valueName, []);
        } else if (type === 'boolean') {
            //handle string, number
            let $input = $(this).parent().children('input');
            let valueName = $input.attr('name');
            let value = $input.prop('checked');
            //handle checkbox
            console.log(valueName, value, type);
            setSystemConfig(valueName, value);
        } else {
            let $input = $(this).parent().children('input');
            let valueName = $input.attr('name');
            let value = $input.val();
            console.log(valueName, value, type);
            setSystemConfig(valueName, value);
        }
    });

    $("button", $cachePanel).on('click', function(e) {
        let cacheName = $(this).attr('name');
        console.log('clear');
        clearCache(cacheName);
    });

    function getSystemConfig() {
        send({
            method: 'getSystemConfig'
        }).fail(function() {
            console.log('######### fail');
        }).done(function() {
            console.log('############ nice');
        });
    }
    function setSystemConfig(key, value) {
        send({
            method: 'setSystemConfig',
            valueName: key,
            value: value,
        }).fail(function() {
            console.log('fail setting system config');
        }).done(function() {
            console.log('set system config');
        });
    }
    function clearCache(name) {
        send({
            method: 'clearCache',
            name: name,
        }).fail(function() {
            console.log('failed clearing cache');
        }).done(function() {
            console.log('Cache cleared');
        });
    }
    function send(data) {
        return $.ajax({
            url: "/system/json",
            type: "POST",
            data: JSON.stringify(data),
        });
    }
})();