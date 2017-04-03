
$(document).ready(function () {
    // var parent = $("div.rightbarup");
        
    function loggingMover(){
        // temporary styling will be out sourced to css
        $(".log_me_out").appendTo("body")
            .css("position", "absolute");
        $("box.login").appendTo("body");

        // real desingner function
        $("box.login").on("click", function(){
            $("box.login").toggleClass("login-active");
        });
        $("box.login").on("mouseout", function(){
            $("box.login").not(".login-inactive").addClass("login-inactive");
        });
    }

    if( !(window.matchMedia('(max-width: 700px)').matches) )
    {
        loggingMover();
    }
});