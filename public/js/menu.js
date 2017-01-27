/** help - Menu structure:
 * div  #navframe
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
        $("#navframe a").not("#DataTables_Table_0_paginate a").prepend('<img class="links" src="/img/uikit/link.png">');
    }

    /**
     * performs the menu show-hide action
     */
    function menuToggle() {
        $(".mobile_menuItems .topics").toggleClass("hidden");
        $(".mobile_menuItems .topics").toggleClass("level_0_animated");
    }
    
    /**
     * unsets the classes for mobile view ("S view")
     * when resizing from small to normal view
     */
    function runL () {
         /** style class changes **/
        $(".js-L-view").removeClass("hidden");
        $(".js-S-view").not("hidden").addClass("hidden");
        $(".menuItems .level_0").not("level_0_animated").addClass("level_0_animated");
    }

    /**
     * sets the classes for mobile view ("S view")
     */
    function runS () {
        /** style class changes **/
        $(".js-S-view").removeClass("hidden");
        $(".js-L-view").not("hidden").addClass("hidden"); //resets the menu to closed state
        $(".mobile_menuItems .topics").removeClass("level_0_animated"); //resets the menu to closed state
    }

    /**
     * binds the menu show-hide action
     */
    function menuActionsS () {
        if (mode == 'S') {
            $(".menu_closed").on("click", menuToggle);
        }
    }

    /**
     * Sets the mode by Viewsize
     * returns string 'L', 'M' or 'S'
     * given in var mode
     */
    var mode ='L';
    function setMode () {
        /** removes click event to avoid multiple bindings **/
        $(".menu_closed").off("click", menuToggle);

        if(window.matchMedia('(max-width: 700px)').matches) {
            mode = "S";
            runS();
        } else {
            mode ="L";
            runL();
        }
        console.log ("mode: "+mode);
        console.log ("width: "+$(window).innerWidth ());
        console.log ("height: "+$(window).innerHeight ());
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