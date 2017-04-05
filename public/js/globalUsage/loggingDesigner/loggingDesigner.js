
$(document).ready(function () {
    var loginmodified = false;
        
    function loggingMover(){
        if( !(window.matchMedia('(max-width: 700px)').matches) ) {
            // temporary styling will be out sourced to css
            $(".logout").appendTo("body")
                .css("position", "absolute");
            $(".rightbarDown box.login").appendTo("body")
                .addClass("topBox");

            // real desingner function
            $("box.login.topBox").off("click");
            $("box.login.topBox").on("click", function () {
                $("box.login.topBox").toggleClass("login-active");
            });
            $("box.login.topBox").on("mouseout", function () {
                $("box.login.topBox").not(".login-inactive").addClass("login-inactive");
            });
            loginmodified = true;
        }
    }

    function setBack (){
        if( (window.matchMedia('(max-width: 700px)').matches)) {
            $(".logging").appendTo(".rightbarDown")
                .removeAttr("style")
                .removeClass("login-active")
                .removeClass("login-inactive");
        }
    }

    loggingMover();
    $(window).resize(function(){
        loggingMover();
        setBack();
    });
});