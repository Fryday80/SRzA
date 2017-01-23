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

    function runL () {
        $(".menuItems").removeClass("hidden");
        $(".navbutton").not("hidden").addClass("hidden");
        $(".navtitel").removeClass ("hidden");
        $(".level_0 ul").removeClass("positionRelative");
        $(".level_0 li").removeClass("displayBlock");
    }

    function runS () {
        $(".menuItems").not("hidden").addClass ("hidden");
        $(".navbutton").removeClass("hidden");
        $(".navtitel").not("hidden").addClass ("hidden");
        $(".level_0 ul").not("positionRelative").addClass("positionRelative");
        $(".level_0 li").not("displayBlock").addClass("displayBlock");
        $(".level_0").css("background-color","#FAEBd7");
    }

    function menuActionsS () {

        $(".menu_closed").off("click", menuToggle);
        if (mode == 'S') {
            $(".menu_closed").on("click", menuToggle);
            if ($(".menuItems").not("hidden")) {
            }
            function menuToggle() {
                console.log ("hier");
                $(".menuItems").toggleClass("hidden");
            }
        } else {
        }
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
    $(window).resize ( function () {
        setMode ();
        menuActionsS();
    });
    addLinkDecorators();
    menuActionsS();
})