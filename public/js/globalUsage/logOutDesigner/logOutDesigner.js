
$(document).ready(function () {
    // var parent = $("div.rightbarup");
        
    function loggingMover(){

        $(".log_me_out").appendTo("body")
            .css("position", "absolute");
        $("box.login").appendTo("body");
    }

    if( !(window.matchMedia('(max-width: 700px)').matches) )
    {
        loggingMover();
    }
});