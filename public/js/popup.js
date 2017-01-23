
$(document).ready(function () {

//    sessionStorage.clear(); // for testing reasons

    function disclaimerPop () {
        var ele;

        var markup = $.ajax({
            url: "/disclaimer.txt",    // text for disclaimer
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

        ele = $( "<div class='disclaimer'></div>" ).dialog({
            modal: true,
            title: "Disclaimer",
            open: function () {
                $(".ui-dialog-titlebar-close").hide();      //removes  X in corner
                $('.ui-dialog').css({
                    'width': $(window).width(),          //100% doesn't work
                    'height': $(window).height(),
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
    $(".disclaim").on("click", disclaimerPop);
});