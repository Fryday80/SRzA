/**
*   instant run in base html to attach sublevel Classes that spot out, witch has a sublevel
*   adapts the view for the smallest view
*/
window.menuapp = {
    organizeMenu:function menu_js() {
    }
}



$(document).ready (function menu_handler_js () {

    var menuItems = [];
    var mode = "L";

    /**
     * Sets the mode by Viewsize
     * returns string 'L', 'M' or 'S'
     * given in var mode
     */
    function setMode () {
        if(window.matchMedia('(max-width: 1200px)').matches) {
            mode ="M";
            $(".menuItems").removeClass ("hidden");
            $(".navframe").removeClass ("mobileMenuCenter");
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
            $(".navframe").removeClass ("mobileMenuCenter");

        }
        console.log ("view mode: "+mode);
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

    function S_view_fix () {
        $(".menuItems").toggleClass("hidden");

        if ($(".menuItems").is(".hidden")) {
                $(".navframe").removeClass ("mobileMenuCenter");
            } else {
                $(".navframe").addClass ("mobileMenuCenter");
            }
    }



    $(".navbutton").on("click", S_view_fix);

    $(".navigation li").each ( function (i, element) {
        menuItems.push(stateMachine ($(element)));
    } );

    setMode ();
    $(window).resize ( function () {
        setMode ();
    });

});
