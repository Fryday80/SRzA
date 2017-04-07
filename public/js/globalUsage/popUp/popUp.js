
$(document).ready(function () {

//    sessionStorage.clear(); // for testing reasons

    function disclaimerPop (popup) {
        var ele,
            popupType,
            text,
            boxtitel,
            usedClass;

        if (popup == 'disclaimer') {
            text = "/disclaimer.txt";
            usedClass = popup;
            boxtitel = "Disclaimer";
            popupType = "ok-deny";
        }
        if (popup == 'impressum') {
            text = "/impressum.txt";
            usedClass = popup;
            boxtitel = "Impressum";
            popupType = "ok";
        }

        var markup = $.ajax({
            url: text,    // text for disclaimer
            async: true,
            success: function(e) {
                // console.log('disclaimer pop success');
            },
            error: function(err) {
                console.log(err);
            }
        });
        markup.done(function (e){
            $(ele).html(e);
        });

        if (popupType == "ok-deny"){
            ele = $( "<div class='"+usedClass+"'></div>" )
                .dialog({
                    modal: true,
                    title: boxtitel,
                    height: "auto",
                    width: "auto",
                    open: function () {
                        $(".ui-dialog-titlebar-close").hide();      //removes  X in corner
                    },
                    buttons: {
                        ok: function () {
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
        if (popupType == "ok") {
            ele = $( "<div class='"+usedClass+"'></div>" )
                .dialog({
                    modal: true,
                    title: boxtitel,
                    height: "auto",
                    width: "auto",
                    open: function () {
                        $(".ui-dialog-titlebar-close").hide();      //removes  X in corner
                    },
                    buttons: {
                        ok: function () {
                            $(this).dialog("close");
                        }
                    }
                });
        }
    }
    if(sessionStorage && !sessionStorage.getItem('isshow')){
        disclaimerPop();
    }
    $(".disclaim").on("click", function () {
        disclaimerPop("disclaimer");
    });
    $(".impressum").on("click", function () {
        disclaimerPop("impressum");
    });
});