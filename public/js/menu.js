window.menuapp = {
    organizeMenu:function menu_js() {

        /**
        * adds classes to the Menus ul>li structure
        * down to 3 Levels
        * hides menus that are not first level by adding class "hidden"
        **/
        function giveClassesToMenu () {
            $(".navigation>li").addClass ("firstLevel-li");
            $(".navigation>li>ul").addClass ("secondLevel-ul hidden");
            $(".navigation>li>ul>li").addClass ("secondLevel-li");
            $(".navigation>li>ul>li>ul").addClass ("thirdLevel-ul hidden");
            $(".navigation>li>ul>li>ul>li").addClass ("thirdLevel-li");
        };

        /**
        * finds all menu items, that have sub levels
        * down to 3 levels
        * adds class "topic" to all of them
        * adds class "firstLevel-top" or "secondLevel-top" depending on the Level
        **/
        function spotTheTopics () {

            var spotHelperOne = $(".firstLevel-li").has ("ul");
            spotHelperOne.each(function (index) {
                $(spotHelperOne[index]).addClass("topic firstLevel-top");
            });

            var spotHelperTwo = $(".secondLevel>li").has ("ul");
            spotHelperTwo.each (function (index) {
                $(spotHelperTwo[index]).addClass ("topic secondLevel-top");
            });
        };

        /**
        * adds and <img> in front of every li that has class "topic"
        * <img> with class "dropdown"
        **/
        function attachdropdown () {
    //        if ($(ele).has ("img")) {
    //            console.log ('hat angeblch dropdown');
    //            console.log (ele);
    //            // do nothing
    //        }
    //        else {
    //            var $ele = $('img');
    //            $ele.addClass('dropdown second hidden');
    //            $ele.attr('src', '/media/uikit/arrow-down.png');
    //            $(ele).append($ele);
    //        }
            $(".topic").prepend('<img class="dropdown second hidden" style="margin-top: auto" src="/media/uikit/arrow-down.png" />');
        };

        function menushowS () {
            if ($(window).innerWidth () <700) {
                $(".menuItems").addClass ("hidden");
            }
        }

        menushowS ();
        giveClassesToMenu ();
        spotTheTopics ();
        attachdropdown ();
    }
}



$(document).ready (function menu_handler_js () {

    var li_L1 = ".firstLevel-li";
    var li_L2 = ".secondLevel-li";
    var li_L3 = ".thirdLevel-li";
    var li_U1 = ".firstLevel-ul";
    var li_U2 = ".secondLevel-ul";
    var li_U3 = ".thirdLevel-ul";

    var mode = "L";

    function setMode () {
        if($(window).innerWidth () <1000) {
            mode ="M";
            $(".menuItems").removeClass ("hidden");
        }
        if($(window).innerWidth () <700) {
            mode = "S";
            $(".menuItems").addClass ("hidden");
        } else {
            mode ="L";
            $(".menuItems").removeClass ("hidden");
        }
        console.log (mode);
        console.log ($(window).innerWidth ());
    }

    function a($ele) {
        var state = 'close';

        function update() {
            if (state == 'close') {
                $('>ul', $ele).addClass("hidden");
            } else {
                $('>ul', $ele).removeClass("hidden");
                $('.navigation').trigger('myapp.closemenus', $ele);
            }
        };
        function toggle(e) {
            state = (state == 'close')? 'open': 'close';
            update();
        }
        function open(e) {
            state = 'open';
            update();
        }
        function close(e) {
            state = 'close';
            update();
        }

        $ele.on("click", function () {
            if (mode !== 'S') return;
            toggle});
        $ele.on("mouseenter", function() {
            if (mode === 'S' | mode === 'L') return;
            open();
        });
        $ele.on("mouseleave", function() {
            if (mode === 'S' | mode === 'L') return;
            close();
        });
        $ele.on('myapp.closemenus', function($e) {
            console.log($e === $ele);
            if ($e === $ele) return;
            state = close();
            update();
        });

    }


    /**
    * toggels submenu in Medium Screen/ M-view
    * selected via event.data= "first" or "second"
    * adds class "hidden" on elements.not(this) and removes it from (elements,this)
    **/
    function toggleSub (event) {
        if (event.data == "first") {
//            $(li_U2).addClass ("hidden");  //alle ul not this>ul
            $(li_U2, this).toggleClass ("hidden"); // show
            console.log($(li_U2, this));
        }
        if (event.data == "second") {
            $(li_U3).not (this).addClass ("hidden");
            $(li_U3, this).toggleClass ("hidden");
        }

    }

    /**
    * hides all submenus for Medium Screen/ M-view
    * uses class "hidden" selected via event.data= "first" or "second"
    **/
//    function hideSubs (event) {
//        if (event.data == "first" ) {
//            $(li_U2).addClass ("hidden");
//        }
//        if (event.data == "second") {
//            $(li_U3).addClass ("hidden");
//        }
//    }

    /**
    * toggles view of the whole Menu for Mobile/ S-view
    * uses class "hidden"
    * adds toggles class "mobileMenuCenter" to move open menu more to the middle of the screen
    **/
    /*function toggleMenu () {
        $(".menuItems").toggle ();
        $(".navframe").toggleClass ("mobileMenuCenter");
    }

    function reBindEventHandler () {
        console.log('rebind');
        console.log ($(window)[0].innerWidth);
        //unbind
        $(".navbutton").off("click", toggleMenu)
        $(li_L1).off("click", toggleSub);
        $(li_L2).off("click", toggleSub );
        $(li_L1).off("mouseover", toggleSub);
        $(li_L2).off("mouseover", toggleSub );
        $(li_L1).off("mouseout", hideSubs );
        $(li_L2).off("mouseout", hideSubs );
        //bind dependend from window.size
        if ($(window)[0].innerWidth < 1000 & $(window)[0].innerWidth >700){
            $(li_L1).on("mouseover", null,'first', toggleSub );
            $(li_L2).on("mouseover", null, 'second', toggleSub );
            $(li_L1).on("mouseout", null,'first', hideSubs );
            $(li_L2).on("mouseout", null, 'second', hideSubs );
        }
        if ($(window)[0].innerWidth < 700 ){
            $(".navigation").addClass("hidden");
            $(".navbutton").on("click", toggleMenu)
            $(li_L1).on("click", null,'first', toggleSub );
            $(li_L2).on("click", null, 'second', toggleSub );
            $(li_L1).on("mouseout", null,'first', hideSubs );
            $(li_L2).on("mouseout", null, 'second', hideSubs );
        } else {
            $(".navigation ul").removeClass("hidden");
        }
    };

    $(window).resize ( function () {
        reBindEventHandler ();
    });

    reBindEventHandler ();*/
    $(".navbutton").on("click", function(){
        console.log ($(".menuItems"))
        $(".menuItems").toggleClass("hidden");
        if ($(".menuItems").is("hidden")) {
                $(".navframe").removeClass ("mobileMenuCenter");
            } else {
                $(".navframe").addClass ("mobileMenuCenter");
            }
    });
    $(".navigation li").each ( function (i, element) {
        a ($(element));
    } );
    setMode ();
    $(window).resize ( function () {
        setMode ();
    });

});
