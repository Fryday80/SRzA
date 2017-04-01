
$(document).ready(function () {
    function logOutMover(){

        console.log('logoutmiver');
        $(".log_me_out").appendTo("body")
            .css("position", "absolute")
    }

    if( !(window.matchMedia('(max-width: 700px)').matches) )
    {
        logOutMover();
        console.log('trigger');
    }
    else {
        console.log('not triggert');}

    console.log('styler is here');
});