/** help - Menu structure:
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

/* ------------------ USED FUNCTIONS -------------------- */
    /**
     * adds the link images to all <a>
     */
    function addLinkDecorators () {
        $(".bodycontainer a").not("#DataTables_Table_0_paginate a").prepend('<img class="links" src="/img/uikit/link.png">');
    }

    /**
     * performs the menu show-hide action
     */
    function menuToggle() {
        console.log ("hier");
        $(".menuItems").toggleClass("hidden");
        $(".menuItems").toggleClass("s_Show");
    }

    /**
     * unsets the classes for mobile view ("S view")
     * when resizing from small to normal view
     */
    function runL () {
        /** removes click event for menu button if binded (case of resize S->L) **/
        $(".menu_closed").off("click", menuToggle);

        /** style class changes **/
        $(".menuItems")
            .removeClass("hidden")
            .removeClass("s_Show");

        $(".navbutton")
            .not("hidden").addClass("hidden");

        $(".navtitel")
            .removeClass ("hidden");

        $(".level_0 ul")
            .removeClass("positionRelative")
            .removeClass("sub_level_ul");

        $(".level_0 li")
            .removeClass("displayBlock")
            .removeClass("li_border_lr");

        $(".level_0")
            .removeClass("li_S_view level_0_S")
            .not("level_0_animated").addClass("level_0_animated");

        $(".navigation-background")
            .removeClass("nbg");
    }

    /**
     * sets the classes for mobile view ("S view")
     */
    function runS () {
        /** removes click event to avoid multiple bindings **/
        $(".menu_closed").off("click", menuToggle);

        /** style class changes **/
        $(".menuItems")
            .not("hidden").addClass ("hidden")
            .removeClass("s_Show");

        $(".navbutton")
            .removeClass("hidden");

        $(".navtitel")
            .not("hidden").addClass ("hidden");

        $(".level_0 ul")
            .not("positionRelative").addClass("positionRelative")
            .not("sub_level_ul").addClass("sub_level_ul");

        $(".level_0 li")
            .not("displayBlock").addClass("displayBlock")
            .not("li_border_lr").addClass("li_border_lr");

        $(".level_0")
            .not("li_S_view level_0_S").addClass("li_S_view level_0_S")
            .removeClass("level_0_animated");

        $(".navigation-background")
            .not("nbg").addClass("nbg");
    }

    /**
     * binds the menu show-hide action
     */
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
/* ------------------ WORKING SCRIPT -------------------- */
    setMode ();
    addLinkDecorators();
    menuActionsS();

    $(window).resize ( function () {
        setMode ();
        menuActionsS();
    });
})