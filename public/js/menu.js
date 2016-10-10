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

        function menushowS () {
            if ($(window).innerWidth () <700) {
                $(".menuItems").addClass ("hidden");
            }
        }

        menushowS ();
        giveClassesToMenu ();
        spotTheTopics ();
    }
}



$(document).ready (function menu_handler_js () {

    var menuItems = [];

    var mode = "L";

    function setMode () {
        if(window.matchMedia('(max-width: 1000px)').matches) {
            mode ="M";
            $(".menuItems").removeClass ("hidden");
            for(var i = 0; i < menuItems.length; i++) {
                menuItems[i].close();
            }
            if(window.matchMedia('(max-width: 700px)').matches) {
                mode = "S";
                $(".menuItems").addClass ("hidden");
                for(var i = 0; i < menuItems.length; i++) {
                    menuItems[i].open();
                }
            }
        } else {
            mode ="L";
            $(".menuItems").removeClass ("hidden");
            for(var i = 0; i < menuItems.length; i++) {
                menuItems[i].open();
            }

        }
        console.log (mode);
        console.log ($(window).innerWidth ());
    }

    function stateMachine($ele) {
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

        if (mode == 'L') {
            state = open();
        }

//        $ele.on("click", function () {
//            if (mode !== 'S') return;
//            toggle();
//        });
        $ele.on("mouseenter", function() {
            if (mode !== 'M') return;
            open();
        });
        $ele.on("mouseleave", function() {
            if (mode !== 'M') return;
            close();
        });

        $ele.on('myapp.closemenus', function($e) {
            console.log($e === $ele);
            if ($e === $ele) return;
            state = 'close';
            update();
        });
        return {
            open: open,
            close: close
        };
    }

    $(".navbutton").on("click", function(){
        $(".menuItems").toggleClass("hidden");
        if ($(".menuItems").is(".hidden")) {
                $(".navframe").removeClass ("mobileMenuCenter");
            } else {
                $(".navframe").addClass ("mobileMenuCenter");
            }
    });


    $(".navigation li").each ( function (i, element) {
        menuItems.push(stateMachine ($(element)));
    } );

    setMode ();
    $(window).resize ( function () {
        setMode ();
    });

});
