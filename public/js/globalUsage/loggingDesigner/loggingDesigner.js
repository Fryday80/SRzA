
$(document).ready(function () {
    var container = $('.login-container');

    function setMode() {
        if (container.has('loggedOut'))
            container.addClass('login-design');
        if (container.has('loggedIn'))
            container.addClass('logout-design');
    }
    function clickHandler (){
        $('.login-container').on('click',function(){
            $('.login-container').toggleClass('login-active');
        })
    };
    clickHandler();
    setMode();
    //
    //
    //
    //
    //
    // var state;
    // state = {
    //     htmlModified: false,
    //     loggingFunctionSet: false,
    // };
    //
    // /**
    //  * append css actions and functionality (css slide actions, stay open on click)
    //  * to login box
    //  * dependent on state var "loggingFunctionSet"
    //  * and changes it's state
    //  */
    // function loggingFunction(){
    //     if (!state.loggingFunctionSet) {
    //         $("box.login.topBox").on("click", function () {
    //             $("box.login.topBox").toggleClass("login-active");
    //         });
    //         $("box.login.topBox").on("mouseout", function () {
    //             $("box.login.topBox").not(".login-inactive").addClass("login-inactive");
    //         });
    //         state.loggingFunctionSet = true;
    //     }
    // }
    //
    // /**
    //  * remove css actions and functionality (css slide actions, stay open on click)
    //  * from login box
    //  * dependent on state var "loggingFunctionSet"
    //  * and changes it's state
    //  */
    // function resetLoggingFunction (){
    //     if(state.loggingFunctionSet) {
    //         $("box.login.topBox").off("click");
    //         state.loggingFunctionSet = false;
    //     }
    // }
    //
    // /**
    //  * change the HTML structure for browser view
    //  * dependent on state var "htmlModified"
    //  * and changes it's state
    //  */
    // function setBrowserHTML(){
    //     if (!state.htmlModified) {
    //         $(".logout").appendTo("body");
    //         $(".rightbarDown box.login").appendTo("body")
    //             .addClass("topBox");
    //         state.htmlModified = true;
    //     }
    // }
    //
    // /**
    //  * change the HTML structure for mobile view
    //  * dependent on state var "htmlModified"
    //  * and changes it's state
    //  */
    // function setMobileHTML (){
    //     if (state.htmlModified) {
    //         $(".logging").appendTo(".rightbarDown")
    //             .removeClass("login-active")
    //             .removeClass("login-inactive");
    //         state.htmlModified = false;
    //     }
    // }
    //
    // /**
    //  * runs the functions dependant from view size => mobile or browser view
    //  */
    // function run (){
    //     if( (window.matchMedia('(max-width: 700px)').matches))
    //     {
    //         setMobileHTML();
    //         resetLoggingFunction();
    //     }
    //     else
    //     {
    //         setBrowserHTML();
    //         loggingFunction();
    //     }
    // }
    //
    // run();
    // $(window).resize(function(){
    //     run();
    // });
});