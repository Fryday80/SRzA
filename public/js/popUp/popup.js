
$(document).ready(function () {

//    sessionStorage.clear(); // for testing reasons

    function disclaimerPop () {
        var ele;

        var markup = $.ajax({
            url: "/disclaimer.txt",    // text for disclaimer
            async: true,
            success: function(e) {
                console.log('disclaimer pop success');
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
            height: "auto",
            width: "auto",
            open: function () {
                $(".ui-dialog-titlebar-close").hide();      //removes  X in corner
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