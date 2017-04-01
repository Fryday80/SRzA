
$(document).ready(function () {
    function logOutMover(){

        $(".log_me_out").appendTo("body")
            .css("position", "absolute")
    }

    if( !(window.matchMedia('(max-width: 700px)').matches) )
    {
        logOutMover();
    }

});