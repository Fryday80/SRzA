$(document).ready (function menu_handler_js () {
    "use strict";

    var mode ='L';

    /**
     * performs the menu show-hide action
     */
    function menuToggle() {
        $(".menu_items").toggleClass("hidden")
            .toggleClass("animation");
    }

    /**
     * binds the menu show-hide action
     */
    function menuActionsS () {
        if (mode == 'S') {
            $(".menu_button_img").on("click", menuToggle);
        }
    }

    /**
     * Sets the mode by Viewsize
     * returns string 'L' or 'S'
     * 'S' = mobile view
     * given in var mode
     */
    function setMode () {
        /**
         * unsets the classes for mobile view ("S view")
         * when resizing from small to normal view
         */
        function runL () {
            /** style class changes **/
            $(".js-L-view").removeClass("hidden");
            $(".js-S-view").not("hidden").addClass("hidden");
            $(".logging").removeClass("box")
                .not("log_me_out").addClass("log_me_out");
        }

        /**
         * sets the classes for mobile view ("S view")
         */
        function runS () {
            /** style class changes **/
            $(".js-S-view").removeClass("hidden");
            $(".js-L-view").not("hidden").addClass("hidden"); //resets the menu to closed state
            $(".logging").removeClass("log_me_out")
                .not("box").addClass("box");
        }
        
        
        /** removes click event to avoid multiple bindings **/
        $(".menu_button_img").off("click",  menuToggle);

        if(window.matchMedia('(max-width: 700px)').matches) {
            mode = "S";
            runS();
        } else {
            mode ="L";
            runL();
        }
    }
/* ------------------ WORKING SCRIPT -------------------- */
    setMode ();
    menuActionsS();

    $(window).resize ( function () {
        setMode ();
        menuActionsS();
    });
})