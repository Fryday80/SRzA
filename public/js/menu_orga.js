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
