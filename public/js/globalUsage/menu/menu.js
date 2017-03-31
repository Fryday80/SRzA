$(document).ready (function menu_handler_js () {
    "use strict";

    var mode ='L',
        ulHeight = parseInt( $('ul.navigation').css('height') );

    /**
     * performs the menu show-hide action
     */
    function menuToggle() {
        $(".menu_items").toggleClass("hidden")
            .toggleClass("animation");
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
            $('.navigation .linkPic').removeClass("hidden");
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
            $(".navigation .linkPic").not("hidden").addClass("hidden");
            $(".menu_items").removeClass("animation");
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

    /**
     * binds the menu show-hide action
     */
    function menuActionsS () {
        if (mode == 'S') {
            $(".menu_button_img").on("click", menuToggle);
        }
    }

    function countItems() {
        var selector,
            ItemCount = $('.navigation li.level_0').length,
            zIndex = $('ul.navigation ul').css('z-index'),
            bodyWidth = parseInt( $('body').css('width')),
            ulWidth = parseInt( $('ul.navigation').css('width') ),
            liWidth = parseInt( $('ul.navigation li').css('width') ),
            difference = bodyWidth-ulWidth,
            ele = $('.navigation li');
        selector = '(max-width: ' + ( ( ItemCount*liWidth ) + difference ) + 'px)';

        function getProperty() {
            var $ul = $("<ul class='navigation'></ul>").hide().appendTo("body");
            ulHeight = parseInt( $ul.css("height") );
            $ul.remove();
        }
        function up (){
            $('*', this).css('z-index', 12);
        }
        function down(){
            $('*', this).css('z-index', zIndex);
        }
        function remove(){
            $("ul.navigation").removeAttr("style");
            $("ul.navigation li").removeAttr("style");
            $(ele).off("mouseover", up);
            $(ele).off("mouseout", down);
        }

        if (mode == 'S'){
            getProperty();
        }
        if (window.matchMedia('(min-width: 700px)').matches) {
            if (window.matchMedia(selector).matches) {
                $('ul.navigation').css('height', 'calc('+(2*ulHeight)+'px + 0.5vw)')
                    .css('height', 'calc('+(2*ulHeight)+'px + 0.5vw)');
                $(ele).on("mouseover", up);
                $(ele).on("mouseout", down);
            }
            else {
                remove();
            }
        }
        else {
            remove();
        }
        console.log(ulHeight);
    }
/* ------------------ WORKING SCRIPT -------------------- */
    setMode ();
    menuActionsS();
    countItems();

    $(window).resize ( function () {
        setMode ();
        menuActionsS();
        countItems();
    });
})