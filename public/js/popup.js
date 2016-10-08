
$(document).ready(function () {

//    sessionStorage.clear(); // for testing reasons

    function disclaimerPop () {
        var ele;

        var markup = $.ajax({
            url: "/disclaimer.txt",
            async: true,
            success: function(e) {
                console.log('success');
            },
            error: function(err) {
                console.log(err);
            }
        });
        markup.done(function (e){
            $(ele).html(e);
        });
        var wWidth = $(window).innerWidth;

        ele = $( "<div></div>" ).dialog({
            width: wWidth,
            height: 500,
            modal: true,
            title: "Disclaimer",
            open: function () {
                $(".ui-dialog-titlebar-close").hide();      //entfernt das X im eck
                $('.ui-dialog').css({
                    'width': $(window).width(),
                    'height': $(window).height(),           //addClass geht hier nicht, das überschreibt dann nicht
                    'left': '0px',
                    'top':'0px'
                });
                $(".ui-dialog-content").css({'height': '450px'});
                $(".ui-dialog-content a:link").css({'color': 'white'});
            },
            buttons: {
                accept: function () {
                    sessionStorage.setItem('isshow', 1);
                    $(this).dialog("close");
                }
                ,
                deny: function () {
                    var url = "http://www.google.de";
                    window.location = url;
                }
            }
         });
    }
    if(sessionStorage && !sessionStorage.getItem('isshow')){
        disclaimerPop();
    }
    $('.disclaim').on("click", disclaimerPop);

});
