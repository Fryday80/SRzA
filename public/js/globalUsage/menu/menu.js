$(document).ready (function menu_handler_js () {
    "use strict";

    var mode ='browser',
        resized = false,
        ulHeight = parseInt( $('ul.navigation').css('height') );

    /**
     * performs the menu show-hide action
     */
    function menuToggle() {
        $(".menu_items").toggleClass("hidden")
            .toggleClass("mobile-animation");
    }

    /**
     * Sets the mode by view size
     * sets mode to "browser" || "mobile"
     * and runs designing script
     */
    function setMode () {
        /** resets the browser style, when resized **/
        function runBrowserDesign () {
            /** style class changes **/
            $(".js-L-view").removeClass("hidden");
            $(".js-S-view").not("hidden").addClass("hidden");
            $('.navigation .linkPic').removeClass("hidden");
        }

        /** sets the mobile style or
         * resets the menu to closed state @resize
         */
        function runMobileDesign () {
            /** style class changes **/
            $(".js-S-view").removeClass("hidden");
            $(".js-L-view").not("hidden").addClass("hidden"); //resets the menu to closed state
            $(".navigation .linkPic").not("hidden").addClass("hidden");
        }

        /**
         * binds the menu show-hide action
         */
        function menuActionsMobile () {
            $(".menu_button_img").on("click", menuToggle);
        }

        if(resized) {
            /** removes click event to avoid multiple bindings **/
            $(".menu_button_img").off("click", menuToggle);
            /** removes the animation to avoid view bugs when resized in open state or to normal view **/
            $(".menu_items").removeClass("mobile-animation");
        }

        if(window.matchMedia('(max-width: 700px)').matches) {
            mode = "mobile";
            runMobileDesign();
            menuActionsMobile();
        } else {
            mode ="browser";
            if(resized) {
                runBrowserDesign();
            }
        }
    }

    function menuRowDesigner() {
        var selector,
            ItemCount = $('.navigation li.level_0').length,
            zIndex = $('ul.navigation ul').css('z-index'),
            bodyWidth = parseInt( $('body').css('width')),
            ulWidth = parseInt( $('ul.navigation').css('width') ),
            liWidth = parseInt( $('ul.navigation li').css('width') ),
            difference = bodyWidth-ulWidth,
            ele = $('.navigation li');

        selector = '(max-width: ' + ( ( ItemCount*liWidth ) + difference ) + 'px)';

        function getPropertys() {
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
        function removeStyles(){
            $("ul.navigation").removeAttr("style");
            $("ul.navigation li").removeAttr("style");
            $(ele).off("mouseover", up);
            $(ele).off("mouseout", down);
        }

        if (window.matchMedia('(min-width: 700px)').matches) {
            /** to avoid view bugs when started in mobile view **/
            if(resized){
                getPropertys();
            }
            if (window.matchMedia(selector).matches) {
                $('ul.navigation').css('height', 'calc('+(2*ulHeight)+'px + 0.5vw)')
                    .css('height', 'calc('+(2*ulHeight)+'px + 0.5vw)');
                $(ele).on("mouseover", up);
                $(ele).on("mouseout", down);
            }
            else {
                if(resized) {
                    removeStyles();
                }
            }
        }
        else {
            if(resized) {
                removeStyles();
            }
        }
    }
/* ------------------ WORKING SCRIPT -------------------- */
    setMode ();
    menuRowDesigner();

    $(window).resize ( function () {
        resized = true;
        setMode ();
        menuRowDesigner();
    });
})