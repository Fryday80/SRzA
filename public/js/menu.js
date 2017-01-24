/** help:
 * div  navframe
 *     div .navbutton
 *     /div
 *     div .menuItems
 *          div .navtitel
 *          /div
 *          ul  .navigation
 *              li  .level_0
 *                  ul  .ul_level_1
 *                      li  .level_1
 *                          ...
 *                      /li
 *                  /ul
 *              /li
 *          /ul
 *     /div
 */


$(document).ready (function menu_handler_js () {

    function addLinkDecorators () {
        $(".bodycontainer a").not("#DataTables_Table_0_paginate a").prepend('<img class="links" src="/img/uikit/link.png">');
    }
    
    function menuToggle() {
        console.log ("hier");
        $(".menuItems").toggleClass("hidden");
        $(".menuItems").toggleClass("s_Show");
    }

    function runL () {
        $(".menu_closed").off("click", menuToggle);

        $(".menuItems").removeClass("hidden")
            .removeClass("s_Show");
        $(".navbutton").not("hidden").addClass("hidden");
        $(".navtitel").removeClass ("hidden");
        $(".level_0 ul").removeClass("positionRelative sub_level_ul");
        $(".level_0 li").removeClass("displayBlock");
        $(".level_0").removeClass("li_S_view level_0_S")
            .not("level_0_animated").addClass("level_0_animated");
        $(".navigation-background").removeClass("nbg");
    }

    function runS () {
        $(".menu_closed").off("click", menuToggle);

        $(".menuItems").not("hidden").addClass ("hidden")
            .removeClass("s_Show");
        $(".navbutton").removeClass("hidden");
        $(".navtitel").not("hidden").addClass ("hidden");
        $(".level_0 ul").not("positionRelative sub_level_ul").addClass("positionRelative sub_level_ul");
        $(".level_0 li").not("displayBlock").addClass("displayBlock");
        $(".level_0").not("li_S_view level_0_S").addClass("li_S_view level_0_S")
            .removeClass("level_0_animated");
        $(".navigation-background").not("nbg").addClass("nbg");
    }

    function menuActionsS () {
        if (mode == 'S') {
            $(".menu_closed").on("click", menuToggle);
        } else { }
    }

    /**
     * Sets the mode by Viewsize
     * returns string 'L', 'M' or 'S'
     * given in var mode
     */
    var mode ='L'
    function setMode () {

        if(window.matchMedia('(max-width: 700px)').matches) {
            mode = "S";
            runS();
        } else {
            mode ="L";
            runL();
        }
            console.log (mode);
            console.log ($(window).innerWidth ());
    }

    setMode ();
    addLinkDecorators();
    menuActionsS();

    $(window).resize ( function () {
        setMode ();
        menuActionsS();
    });
})