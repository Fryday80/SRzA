(function() {
    "use strict";
    var ulHeight = parseInt($('ul.navigation').css('height'));

    /**
     *
     * @typedef {{
     *              mode: string,
     *              mobile: string,
     *              browser: string,
      *             setMode: menuState.setMode,
      *             setBrowserMode: menuState.browserMode,
      *             setMobileMode: menuState.mobileMode,
      *             resized: boolean,
      *             resizeAction: menuState.resizeAction,
      *             togglePin: boolean
      *         }} MenuState
     */
    /** @var MenuState*/
    var menuState = {
        mode: 'browser',
        mobile: "mobile",
        browser: "browser",
        resized: false,
        togglePin: false,
        setMode: function (modus) {
            this.mode = modus;
        },
        setBrowserMode: function () {
            this.mode = this.browser;
        },
        setMobileMode: function () {
            this.mode = this.mobile;
        },
        resizeAction: function () {
            this.resized = true;
        }
    };
    apps.menuHandler = window.menuHandler = {
        run: function () {
            var self = this;
            /* ------------------ RUNNING SCRIPT --START------------- */
            this.menuActionPin();
            this.modeSwitching();

            $(window).resize(function () {
                menuState.resizeAction();
                self.modeSwitching();
            });
            /* ------------------ RUNNING SCRIPT --END--------------- */
        },
        modeSwitching: function (){
                this.setMode();
                this.modeDependencyActions();
                this.menuRowDesigner();
        },
            /**
             * Sets the mode by view size
             * sets mode to menuState.browser || menuState.mobile
             */
        setMode: function(){
            if (window.matchMedia('(max-width: 700px)').matches) {
                menuState.setMobileMode();
            } else {
                menuState.setBrowserMode();
            }
        },
            /** binds pinToggle to img **/
        menuActionPin: function (){
            $('.log-pin').on("click", this.pinToggle);
        },

        /* toggle functions */
        /**
         * performs the menu show-hide action
         */
        menuToggle: function () {
            $(".menu_items").toggleClass("hidden")
                .toggleClass("mobile-animation");
        },

        /**
         * performs the user menu show-hide action
         */
        userMenuToggle: function () {
            $(".logout-list.myMenu").toggleClass("hidden")
                .toggleClass("mobile-animation");
        },

        /**
         * performs the mobile login show-hide action
         */
        loginMenuToggle: function () {
            $(".login-form").toggleClass("hidden")
                .toggleClass("mobile-animation");
        },

        /**
         * performs the z-index adjustment in browser view
         */
        pinToggle: function () {
            $(".log-pin-overlay").toggleClass("hidden");
            if (menuState.togglePin){
                menuState.togglePin = false;
                $('.logging-container').css('z-index', '1')
            } else {
                menuState.togglePin = true;
                $('.logging-container').css('z-index', '10')
            }
        },

        /**
         * Runs designing and handler scripts
         * in dependency of menuState.mode
         */
        modeDependencyActions: function () {
            var self = this;

            function mobileMenuHandling(){
                menuActionsMobile();
                menuDesignMobile();
            }

            /**
             * binds the menu show-hide action and sets all hidden
             */
            function menuActionsMobile() {
                $(".mobile-menu-toggle").on("click", self.menuToggle);

                $(".user-menu-toggle").on("click", self.userMenuToggle);

                $(".login-menu-toggle").on("click", self.loginMenuToggle);
            }
            function menuDesignMobile(){
                $(".menu_items")        .not('hidden').addClass("hidden");
                $(".logout-list.myMenu").not('hidden').addClass("hidden");
                $(".login-form")        .not('hidden').addClass("hidden");

            }

            /** removes click events to avoid multiple bindings */
            function removeMobileHandlers() {
                $(".mobile-menu-toggle").off("click", self.menuToggle);
                $(".login-menu-toggle") .off("click", self.loginMenuToggle);
                $(".user-menu-toggle")  .off("click", self.userMenuToggle);
            }
            /** reverts menuDesignMobile() */
            function removeDesignOfMobileViewInBrowserView(){
                /* remove 'hidden' from mobile views */
                $(".menu_items").removeClass("hidden");
                $(".logout-list.myMenu").removeClass("hidden");
                $(".login-form").removeClass("hidden");
            }

            if (menuState.resized) {
                /* remove click events to avoid multiple bindings */
                removeMobileHandlers();

                /* removes the animation to avoid view bugs when resized in open state or to normal view */
                $(".menu_items").removeClass("mobile-animation");

                if (menuState.mode === menuState.browser) {
                    /* remove 'hidden' from mobile views ... reverts menuDesignMobile() */
                    removeDesignOfMobileViewInBrowserView();
                }
            }

            if (menuState.mode === menuState.mobile){
                mobileMenuHandling();
            }
        },

        menuRowDesigner: function () {
            var Designer, self;

            self = this;

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
                $('*', self).css('z-index', 12);
            }

            function down() {
                $('*', self).css('z-index', Designer.zIndex);
            }

            function removeStyles() {
                $("ul.navigation").removeAttr("style");
                $("ul.navigation li").removeAttr("style");
                $(Designer.ele).off("mouseover", up);
                $(Designer.ele).off("mouseout", down);
            }


            if (menuState.mode === "browser") {
                /* to avoid view bugs when started in mobile view */
                if (menuState.resized) {
                    getPropertys();
                }
                if (window.matchMedia(Designer.selector).matches) {
                    $('ul.navigation').css('height', 'calc(' + (2 * Designer.ulHeight) + 'px + 0.5vw)')
                        .css('height', 'calc(' + (2 * Designer.ulHeight) + 'px + 0.5vw)');
                    $(Designer.ele).on("mouseover", up);
                    $(Designer.ele).on("mouseout", down);
                }
                else {
                    if (menuState.resized) {
                        removeStyles();
                    }
                }
            }
            else {
                if (menuState.resized) {
                    removeStyles();
                }
            }

        }

        };
})();