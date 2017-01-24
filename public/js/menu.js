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
        $(".js-L-view").removeClass("hidden");
        $(".js-S-view").not("hidden").addClass("hidden");
        
        $(".menuItems")
            .removeClass("s_Show");
        
        $(".level_0 ul")
            .removeClass("sub_level_ul");

        $(".level_0 li")
            .removeClass("li_border_lr");

        $(".level_0")
            .removeClass("level_0_S")
            .removeClass("li_S_view")
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
        $(".js-S-view").removeClass("hidden");
        $(".js-L-view").not("hidden").addClass("hidden");
        
        $(".menuItems")
            .removeClass("s_Show");
        
        $(".level_0 ul")
            .not("sub_level_ul").addClass("sub_level_ul");

        $(".level_0 li")
            .not("li_border_lr").addClass("li_border_lr");

        $(".level_0")
            .not("li_S_view").addClass("li_S_view")
            .not("level_0_S").addClass("level_0_S")
            .removeClass("level_0_animated");

        $(".navigation-background")
            .not("nbg").addClass("nbg");
    }
//    @todo bugfix    view <400p width => menu crashes
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