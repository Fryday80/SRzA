window.menuapp = {
    organizeMenu:function menu_js() {

        function giveClassesToMenu () {
            $(".navigation>li").addClass ("firstLevel-li");
            $(".navigation>li>ul").addClass ("secondLevel-ul");
            $(".navigation>li>ul>li").addClass ("secondLevel-li hidden");
            $(".navigation>li>ul>li>ul").addClass ("thirdLevel-ul");
            $(".navigation>li>ul>li>ul>li").addClass ("thirdLevel-li hidden");
        };

        function spotTheTopics () {

            var spotHelperOne = $(".firstLevel-li").has("ul");
            spotHelperOne.each(function (index) {
                $(spotHelperOne[index]).addClass("topic firstLevel-top");
            });

            var spotHelperTwo = $(".secondLevel>li").has ("ul");
            spotHelperTwo.each (function (index) {
                $(spotHelperTwo[index]).addClass ("topic secondLevel-top");
            });
        };

        function attachdropdown (ele) {
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
            $(ele).prepend('<img class="dropdown second hidden" style="margin-top: auto" src="/media/uikit/arrow-down.png" />');
        };

        giveClassesToMenu ();
        spotTheTopics ();
        attachdropdown ();
    }
}



$(document).ready (function menu_handler_js () {
console.trace();
    function toggleSub (level, $element) {
        if (level == ".firstLevel-li") {
            console.log ("mouseover");
            $(".secondLevel-li").not ($element).addClass ("hidden");
            $(".secondLevel-li", $element).removeClass ("hidden");
        }
        if (level == ".secondLevel-li") {
            $(".thirdLevel-li", this).removeClass ("hidden");
        }
        // ich hab beide versionen getestet (die in den ifs)
// wenn ich das this im secondlevel-if weglasse zeigt er alle an, mit if keines das mouseover geht gar nicht
    }

    function reBindEventHandler () {
        //unbind
        $(".firstLevel-li").off("mouseover", toggleSub (".firstLevel-li"));
        //bind dependend from window.size
        if ($(window)[0].innerWidth < 1000 ){
            console.log ($(window)[0].innerWidth);
            $(".firstLevel-li").on("mouseover", toggleSub (".firstLevel-li", this));
            $(".secondLevel-li").on("mouseover", toggleSub (".secondLevel-li"));
            if ($(window)[0].innerWidth < 700 ){

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
