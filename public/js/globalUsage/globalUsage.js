/**
 * change all select.iconselect's jquery ui selectmenu
 */
(function() {
    "use strict";
    $.widget("custom.iconselectmenu", $.ui.selectmenu, {
        _renderItem: function (ul, item) {
            var li = $("<li>", {
                style: item.element.attr("data-li-style"),
                "class": item.element.attr("data-li-class")
            });
            var wrapper = $("<div>", {
                text: item.label
            });

            if (item.disabled) {
                li.addClass("ui-state-disabled");
            }

            $("<span>", {
                style: item.element.attr("data-span-style"),
                "class": item.element.attr("data-span-class")
            })
                .appendTo(wrapper);

            return li.append(wrapper).appendTo(ul);
        }
    });
})();
$(document).ready(function() {
    "use strict";
    //hack for strange widget error
    let $selected = $("select");
    if ($selected.iconselectmenu == undefined) return;
    $("select.iconselect").iconselectmenu();
});
/**
 * menu management
 */
$(document).ready (function menu_handler_js () {
    "use strict";
    
    var ulHeight = parseInt( $('ul.navigation').css('height') );

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
        changeMode: function ( modus ) {
            this.mode = modus;
        },
        browserMode: function () {
            this.mode = 'browser';
        },
        mobileMode: function () {
            this.mode = 'mobile';
        },
        resized: false,
        resizeAction: function () {
            this.resized = true;
        }
    };

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

        if(state.resized) {
            /** removes click event to avoid multiple bindings **/
            $(".menu_button_img").off("click", menuToggle);
            /** removes the animation to avoid view bugs when resized in open state or to normal view **/
            $(".menu_items").removeClass("mobile-animation");
        }

        if(window.matchMedia('(max-width: 700px)').matches) {
            state.changeMode("mobile");
            runMobileDesign();
            menuActionsMobile();
        } else {
            state.changeMode("browser");
            runBrowserDesign();
        }
    }

    function menuRowDesigner() {
        var Designer;

        Designer = {
            selector : '',
            ItemCount : $('.navigation li.level_0').length,
            zIndex : $('ul.navigation ul').css('z-index'),
            bodyWidth : parseInt($('body').css('width')),
            ulWidth : parseInt($('ul.navigation').css('width')),
            ulHeight: ulHeight,
            liWidth : parseInt($('ul.navigation li').css('width')),
            difference : "",
            ele : $('.navigation li'),
        };

        Designer.difference = Designer.bodyWidth - Designer.ulWidth,
            Designer.selector = '(max-width: ' +
                ( ( Designer.ItemCount*Designer.liWidth ) + Designer.difference )
                + 'px)';

        function getPropertys() {
            var $ul = $("<ul class='navigation'></ul>").hide().appendTo("body");
            Designer.ulHeight = parseInt( $ul.css("height") );
            $ul.remove();
        }
        function up (){
            $('*', this).css('z-index', 12);
        }
        function down(){
            $('*', this).css('z-index', Designer.zIndex);
        }
        function removeStyles(){
            $("ul.navigation").removeAttr("style");
            $("ul.navigation li").removeAttr("style");
            $(Designer.ele).off("mouseover", up);
            $(Designer.ele).off("mouseout", down);
        }

        if (window.matchMedia('(min-width: 700px)').matches) {
            /** to avoid view bugs when started in mobile view **/
            if(state.resized){
                getPropertys();
            }
            if (window.matchMedia(Designer.selector).matches) {
                $('ul.navigation').css('height', 'calc('+(2*Designer.ulHeight)+'px + 0.5vw)')
                    .css('height', 'calc('+(2*Designer.ulHeight)+'px + 0.5vw)');
                $(Designer.ele).on("mouseover", up);
                $(Designer.ele).on("mouseout", down);
            }
            else {
                if(state.resized) {
                    removeStyles();
                }
            }
        }
        else {
            if(state.resized) {
                removeStyles();
            }
        }
    }
    /* ------------------ WORKING SCRIPT -------------------- */
    setMode ();
    menuRowDesigner();

    $(window).resize ( function () {
        state.resizeAction();
        setMode ();
        menuRowDesigner();
    });
});

/**
 * redesign of the login/logout box
 */
$(document).ready(function loggingDesigner() {
    var state;
    state = {
        htmlModified: false,
        loggingFunctionSet: false,
    };

    /**
     * append css actions and functionality (css slide actions, stay open on click)
     * to login box
     * dependent on state var "loggingFunctionSet"
     * and changes it's state
     */
    function loggingFunction(){
        if (!state.loggingFunctionSet) {
            $("box.login.topBox").on("click", function () {
                $("box.login.topBox").toggleClass("login-active");
            });
            $("box.login.topBox").on("mouseout", function () {
                $("box.login.topBox").not(".login-inactive").addClass("login-inactive");
            });
            state.loggingFunctionSet = true;
        }
    }

    /**
     * remove css actions and functionality (css slide actions, stay open on click)
     * from login box
     * dependent on state var "loggingFunctionSet"
     * and changes it's state
     */
    function resetLoggingFunction (){
        if(state.loggingFunctionSet) {
            $("box.login.topBox").off("click");
            state.loggingFunctionSet = false;
        }
    }

    /**
     * change the HTML structure for browser view
     * dependent on state var "htmlModified"
     * and changes it's state
     */
    function setBrowserHTML(){
        if (!state.htmlModified) {
            $(".logout").appendTo("body");
            $(".rightbarDown box.login").appendTo("body")
                .addClass("topBox");
            state.htmlModified = true;
        }
    }

    /**
     * change the HTML structure for mobile view
     * dependent on state var "htmlModified"
     * and changes it's state
     */
    function setMobileHTML (){
        if (state.htmlModified) {
            $(".logging").appendTo(".rightbarDown")
                .removeClass("login-active")
                .removeClass("login-inactive");
            state.htmlModified = false;
        }
    }

    /**
     * runs the functions dependant from view size => mobile or browser view
     */
    function run (){
        if( (window.matchMedia('(max-width: 700px)').matches))
        {
            setMobileHTML();
            resetLoggingFunction();
        }
        else
        {
            setBrowserHTML();
            loggingFunction();
        }
    }

    run();
    $(window).resize(function(){
        run();
    });
});

/**
 * popUps for disclaimer and impressum
 */
$(document).ready(function poppingUp() {
//    sessionStorage.clear(); // for testing reasons
    /** remove the href event default of the no js fallback **/
    function removeNoScriptFallback (){
        $(".disclaim").click(function( event ) {
            event.preventDefault();
        });
        $(".impressum").click(function( event ) {
            event.preventDefault();
        });
    }
    
    /**
     * @param title     string
     * @param content   string
     * @param buttons   e.g. {ok: functionXY, deny: functionYX}
     * @param popUpClass string
     *
     * @return open dialog
     */
    function openPopup(title, content, buttons, popUpClass) {
        let ele,
            usedClass = popUpClass || "popUp",
            buttonSetup = buttons || {ok: closeButton};

        ele = $( "<div class='"+usedClass+"'></div>" )
            .html(content)
            .dialog({
                modal: true,
                title: title,
                height: "auto",
                width: "auto",
                open: onOpen,
                buttons: buttonSetup
            });
    }

    /**
     * removes X-button from title bar
     * to force decision via buttons
     */
    function onOpen() {
        $(".ui-dialog-titlebar-close").hide();
    }

    /**
     * close dialog
     * */
    function closeButton(){
        sessionStorage.setItem('isshow', 1);
        $(this).dialog("close");
    }

    /**
     * redirect to google
     * e.g. if disclaimer is denied
     */
    function redirectToGoogle() {
        var url = "http://www.google.de";
        window.location = url;
    }

    /**
     * @param url       string .. guess what of an url
     * @param buttons   e.g. {ok: functionXY, deny: functionYX}
     * @param popUpClass string
     *
     * @return opens dialog set up with url content
     */
    function openPopUpByUrl (url, buttons, popUpClass) {
        var title,
            content;
        $.ajax({
            url: url,
            async: true,
            json: true,
            success: function (e){
                title = e.title;
                content = (e.success)? e.content : e.error;
                openPopup(title, content, buttons, popUpClass);
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    /**
     * run dialog for disclaimer
     */
    function disclaimerPop() {
        openPopUpByUrl('/disclaimer', {ok: closeButton, deny: redirectToGoogle}, 'disclaimer');
    }

    /**
     * run dialog for impressum
     */
    function impressumPop(){
        openPopUpByUrl('/impressum', {ok: closeButton},'impressum');
    }

    if(sessionStorage && !sessionStorage.getItem('isshow')){
        disclaimerPop();
    }

    removeNoScriptFallback();
    $(".disclaim").on("click", function () {
        disclaimerPop();
    });
    $(".impressum").on("click", function () {
        impressumPop();
    });
});

/**
 * accordion
 */
$(function() {
    //<accordion class="hightcontent"></accordion>
    $("accordion").each(function(i, ele) {
        $(ele).accordion({
            heightStyle: "content",
            icons: { "header": "", "activeHeader": "" }
        });
    })
});