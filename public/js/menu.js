window.menuapp = {
    organizeMenu:function menu_js() {

        /**
        * adds classes to the Menus ul>li structure
        * down to 3 Levels
        * hides menus that are not first level by adding class "hidden"
        **/
        function giveClassesToMenu () {
            $(".navigation>li").addClass ("firstLevel-li");
            $(".navigation>li>ul").addClass ("secondLevel-ul");
            $(".navigation>li>ul>li").addClass ("secondLevel-li hidden");
            $(".navigation>li>ul>li>ul").addClass ("thirdLevel-ul");
            $(".navigation>li>ul>li>ul>li").addClass ("thirdLevel-li hidden");
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

        giveClassesToMenu ();
        spotTheTopics ();
        attachdropdown ();
    }
}



$(document).ready (function menu_handler_js () {

    var li_L1 = ".firstLevel-li";
    var li_L2 = ".secondLevel-li";
    var li_L3 = ".thirdLevel-li";




    /**
    * toggels submenu in Medium Screen/ M-view
    * selected via event.data= "first" or "second"
    * adds class "hidden" on elements.not(this) and removes it from (elements,this)
    **/
    function toggleSub (event) {
        if (event.data == "first") {
            $(li_L2).not (this).addClass ("hidden");
            $(li_L2, this).removeClass ("hidden");
        }
        if (event.data == "second") {
            $(li_L3).not (this).addClass ("hidden");
            $(li_L3, this).removeClass ("hidden");
        }
    }

    /**
    * hides all submenus for Medium Screen/ M-view
    * uses class "hidden" selected via event.data= "first" or "second"
    **/
    function hideSubs (event) {
        if (event.data == "first" ) {
            $(li_L2).addClass ("hidden");
        }
        if (event.data == "second") {
            $(li_L3).addClass ("hidden");
        }
    }

    /**
    * toggles view of the whole Menu for Mobile/ S-view
    * uses class "hidden"
    **/
    function toggleMenu () {
        $("#navbutton").toggleClass ("hidden")
    }

    function reBindEventHandler () {
        console.log('rebind');
        //unbind
        $(li_L1).off("mouseover", toggleSub);
        $(li_L2).off("mouseover", toggleSub );
        $(li_L1).off("mouseout", hideSubs );
        $(li_L2).off("mouseout", hideSubs );
        //bind dependend from window.size
        if ($(window)[0].innerWidth < 1000 ){
                                                                                    console.log ($(window)[0].innerWidth);
            $(li_L1).on("mouseover", null,'first', toggleSub );
            $(li_L2).on("mouseover", null, 'second', toggleSub );
            $(li_L1).on("mouseout", null,'first', hideSubs );
            $(li_L2).on("mouseout", null, 'second', hideSubs );
            if ($(window)[0].innerWidth < 700 ){
                $(".navigation").addClass("hidden")
                $("#navbutton").on("click", toggleMenu)
            }
        } else {
            $(".navigation li").removeClass("hidden");
        }
    };

    $(window).resize ( function () {
        reBindEventHandler ();
    });

    reBindEventHandler ();

});
