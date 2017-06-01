(function () {
    "use strict";
    //script for live ticks
    function loadLive(e) {
        e.fail(function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR, textStatus, errorThrown);
        });
        e.done(function(e, textStatus, jqXHR) {
            let actions = e.actions;
            // action log - live Clicks
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
                console.log(since);
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
            complete: loadLive
        });
    }
    livereload( $('#dashLiveList li:nth-child(1)').data('timestamp'), 0);
})();

(function () {
    "use strict";
    //script for live ticks
    function loadActive(e) {
        e.fail(function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR, textStatus, errorThrown);
        });
        e.done(function(e, textStatus, jqXHR) {
            console.log(e);
            let user = e.users;
            console.log(user);
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
        console.log('sdf');
    });
})();