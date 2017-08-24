(function() {
    "use strict";
    var ulHeight = parseInt($('ul.navigation').css('height'));

    /**
     *
     * @typedef {{ mode: string,
      *         changeMode: state.changeMode,
      *         browserMode: state.browserMode,
      *         mobileMode: state.mobileMode,
      *         resized: boolean,
      *         resizeAction: state.resizeAction}} State
     */
    /** @var State*/
    var state = {
        mode: 'browser',
        resized: false,
        changeMode: function (modus) {
            this.mode = modus;
        },
        browserMode: function () {
            this.mode = 'browser';
        },
        mobileMode: function () {
            this.mode = 'mobile';
        },
        resizeAction: function () {
            this.resized = true;
        }
    };

    apps.menuHandler = {

        run: function () {
            /* ------------------ RUNNING SCRIPT -------------------- */
            setMode();
            menuRowDesigner();

            $(window).resize(function () {
                state.resizeAction();
                setMode();
                menuRowDesigner();
            });


            /**
             * performs the menu show-hide action
             */
            function menuToggle () {
                $(".menu_items").toggleClass("hidden")
                    .toggleClass("mobile-animation");
            }

            /**
             * Sets the mode by view size
             * sets mode to "browser" || "mobile"
             * and runs designing script
             */
            function setMode () {
                /**
                 * binds the menu show-hide action
                 */
                function menuActionsMobile() {
                    $(".menu_button_img").on("click", menuToggle);
                }

                if (state.resized) {
                    /** removes click event to avoid multiple bindings **/
                    $(".menu_button_img").off("click", menuToggle);
                    /** removes the animation to avoid view bugs when resized in open state or to normal view **/
                    $(".menu_items").removeClass("mobile-animation");
                }

                if (window.matchMedia('(max-width: 700px)').matches) {
                    state.changeMode("mobile");
                    menuActionsMobile();
                } else {
                    state.changeMode("browser");
                }
            }

            function menuRowDesigner () {
                var Designer;

                Designer = {
                    selector: '',
                    ItemCount: $('.navigation li.level_0').length,
                    zIndex: $('ul.navigation ul').css('z-index'),
                    bodyWidth: parseInt($('body').css('width')),
                    ulWidth: parseInt($('ul.navigation').css('width')),
                    ulHeight: ulHeight,
                    liWidth: parseInt($('ul.navigation li').css('width')),
                    difference: "",
                    ele: $('.navigation li'),
                };

                Designer.difference = Designer.bodyWidth - Designer.ulWidth,
                    Designer.selector = '(max-width: ' +
                        ( ( Designer.ItemCount * Designer.liWidth ) + Designer.difference )
                        + 'px)';

                function getPropertys() {
                    var $ul = $("<ul class='navigation'></ul>").hide().appendTo("body");
                    Designer.ulHeight = parseInt($ul.css("height"));
                    $ul.remove();
                }

                function up() {
                    $('*', this).css('z-index', 12);
                }

                function down() {
                    $('*', this).css('z-index', Designer.zIndex);
                }

                function removeStyles() {
                    $("ul.navigation").removeAttr("style");
                    $("ul.navigation li").removeAttr("style");
                    $(Designer.ele).off("mouseover", up);
                    $(Designer.ele).off("mouseout", down);
                }

                if (window.matchMedia('(min-width: 700px)').matches) {
                    /** to avoid view bugs when started in mobile view **/
                    if (state.resized) {
                        getPropertys();
                    }
                    if (window.matchMedia(Designer.selector).matches) {
                        $('ul.navigation').css('height', 'calc(' + (2 * Designer.ulHeight) + 'px + 0.5vw)')
                            .css('height', 'calc(' + (2 * Designer.ulHeight) + 'px + 0.5vw)');
                        $(Designer.ele).on("mouseover", up);
                        $(Designer.ele).on("mouseout", down);
                    }
                    else {
                        if (state.resized) {
                            removeStyles();
                        }
                    }
                }
                else {
                    if (state.resized) {
                        removeStyles();
                    }
                }

            }

        },
    }
})();