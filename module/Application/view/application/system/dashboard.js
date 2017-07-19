//live ticks
(function () {
    "use strict";
    var bugFix = 0;
    //script for live ticks
    function loadLive(e) {
        e.fail(function(jqXHR, textStatus, errorThrown) {
            console.error(jqXHR.responseJSON);
            // console.error(jqXHR.responseJSON.msg);
        });
        e.done(function(e, textStatus, jqXHR) {
            let actions = e.actions;
            if (actions !== null) {
                if (bugFix == 0) {
                    actions.reverse();
                    bugFix = 1;
                }
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
            console.error(jqXHR.responseJSON);
            // console.error(jqXHR.responseJSON.msg);
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
    var $pop = $('<div id="pop" title="System Log"></div>'),
        $table = $('#sysTable');
    $('.systemLog').on('click', function(){
        $pop.append($table);
        $table.removeClass('hidden');
        $pop.dialog({
            width: "100%",
            onLoad: function (html) {
            }
        });
        $pop.css('max-height', '60vh');
    });
})();
//system config control
(function () {
    "use strict";
    //scan system vars and register handler
    var $systemPanel = $('.systemPanel'),
        $systemPanelList = $('boxcontent ul', $systemPanel),
        $cachePanel = $('.cachePanel');

    $("button", $systemPanel).on('click', function(e) {
        let type = $(this).data('type');
        if (type === 'function') {
            let valueName = $(this).attr('name');
            //handle function
            console.log(valueName, type);
            setSystemConfig(valueName, [], $(this));
        } else if (type === 'boolean') {
            //handle string, number
            let $input = $('input', $(this).parent())
            let valueName = $input.attr('name');
            let value = $input.prop('checked');
            let $img = $('img', $(this).parent());
            let imgSrc;
            let imgAlt;
            if (value == true){
                imgSrc = '/img/uikit/led-on.png';
                imgAlt = 'on';
            } else {
                imgSrc = '/img/uikit/led-off.png';
                imgAlt = 'off';
            }
            $img.attr('src', imgSrc)
                .attr('alt', imgAlt);
            //handle checkbox
            console.log(valueName, value, type);
            console.log($img);
            setSystemConfig(valueName, value, $(this));
        } else {
            let $input = $('input', $(this).parent())
            let valueName = $input.attr('name');
            let value = $input.val();
            console.log(valueName, value, type);
            setSystemConfig(valueName, value, $(this));
        }
    });

    $("button", $cachePanel).on('click', function(e) {
        let cacheName = $(this).data('name');
        let $li = $(this).parent('li');
        clearCache(cacheName, function() {
            console.log('success');
            $li.remove();
        }, function() {
            $li.stop();
            $li.show();
            $li.css('opacity', 1);
            //@todo show error
        });
        $li.fadeOut(500);
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
    function setSystemConfig(key, value, $button) {
        $button.removeClass('failMark');
        $button.removeClass('checkMark');
        $button.addClass('pendingMark');
        send({
            method: 'setSystemConfig',
            valueName: key,
            value: value,
        }).fail(function() {
            //@todo fetch config and flush whole config
            $button.removeClass('pendingMark');
            $button.addClass('failMark');
            console.log('error');
        }).done(function() {
            //@todo show symbol and add timeout to fadeout in a few secs
            $button.removeClass('pendingMark');
            $button.addClass('checkMark');
            setTimeout(function() {
                this.removeClass('checkMark');
            }.bind($button), 3000);
        });
    }
    function clearCache(name, success, fail) {
        send({
            method: 'clearCache',
            name: name,
        }).fail(function() {
            if (typeof fail === 'function') fail();
        }).done(function() {
            if (typeof success === 'function') success();
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