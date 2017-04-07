
$(document).ready(function () {
    var loggingFunctionSet = false,
        htmlModified = false;

    /**
     * append css actions and functionality (css slide actions, stay open on click)
     * to login box
     * dependent on state var "loggingFunctionSet"
     * and changes it's state
     */
    function loggingFunction(){
        if (!loggingFunctionSet) {
            $("box.login.topBox").on("click", function () {
                $("box.login.topBox").toggleClass("login-active");
            });
            $("box.login.topBox").on("mouseout", function () {
                $("box.login.topBox").not(".login-inactive").addClass("login-inactive");
            });
            loggingFunctionSet = true;
        }
    }

    /**
     * remove css actions and functionality (css slide actions, stay open on click)
     * from login box
     * dependent on state var "loggingFunctionSet"
     * and changes it's state
     */
    function resetLoggingFunction (){
        if(loggingFunctionSet) {
            $("box.login.topBox").off("click");
            loggingFunctionSet = false;
        }
    }

    /**
     * change the HTML structure for browser view
     * dependent on state var "htmlModified"
     * and changes it's state
     */
    function setBrowserHTML(){
        if (!htmlModified) {
            $(".logout").appendTo("body");
            $(".rightbarDown box.login").appendTo("body")
                .addClass("topBox");
            htmlModified = true;
        }
    }

    /**
     * change the HTML structure for mobile view
     * dependent on state var "htmlModified"
     * and changes it's state
     */
    function setMobileHTML (){
        if (htmlModified) {
            $(".logging").appendTo(".rightbarDown")
                .removeClass("login-active")
                .removeClass("login-inactive");
            htmlModified = false;
        }
    }

    /**
     * runs the functions dependant from view size => mobile or browser view
     */
    function run (){
        if( (window.matchMedia('(max-width: 700px)').matches))
        {
            setMobileHTML();
            resetLoggingFunction();
        }
        else
        {
            setBrowserHTML();
            loggingFunction();
        }
    }

    run();
    $(window).resize(function(){
        run();
    });
});