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
                    $('#dashLiveList').prepend("<li class='entry' data-timestamp='" + actions[i].id + "'>" +
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
            complete: loadLive
        });
    }
    livereload( $('#dashLiveList li:nth-child(1)').data('timestamp'));
})();

(function () {
    "use strict";
    //script for live ticks
    function loadActive(e) {
        e.fail(function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR, textStatus, errorThrown);
        });
        e.done(function(e, textStatus, jqXHR) {
            let user = e.users;
            // active users
            if (user !== null) {
                for (let c = 0; c < user.length; c++){
                    //                    var dateRaw = new Date(user[c].time*1000);
                    //                    var hours = '0' + dateRaw.getHours();
                    //                    var minutes = '0' + dateRaw.getMinutes();
                    //                    var date = hours.substr(-2) + ':' + minutes.substr(-2);
                    // remove updated user
                    $("li.id" + user[c].userId ).remove();
                    // prepend updated user
                    $('#users').prepend("<li class='entry id" + user[c].userId + "' data-timestamp='" + user[c].id + "' data-id='" + user[c].userId + "'>" +
                        user[c].userName + ": " + user[c].url + "<b> @ </b>" + user[c].dateTime + "</li>");
                }
            }
            setTimeout(function() {
                let elm = $('#users li:nth-child(1)');
                let userTime = elm.data('timestamp');
                let userId = elm.data('id');
                activereload(userTime, userId);
            }, 4500);
        });
    }
    function activereload(userTime, userId) {
        let data = {
            method: "getActiveUsers",
            userTime: userTime,
            userId: userId,
        };
        $.ajax({
            url: "/system/json",
            type: "POST",
            data: JSON.stringify(data),
            complete: loadActive
        });
    }
    livereload( $('#users li:nth-child(1)').data('timestamp'), 0 );
})();