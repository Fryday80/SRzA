console.log (window.innerWidth + "px width -- refreshes on reload");

jQuery(document).ready(function () {

        console.trace();
    function giveClassesToMenu () {
        $(".navigation>li>ul").addClass("secondLevel hidden");
        $(".secondLevel>li>ul").addClass("thirdLevel hidden");
    }
    giveClassesToMenu ();

    function attachdropdown (ele) {
//        if ($(ele).has ("img")) {
//            console.log ('hat angeblch dropdown');
//            console.log (ele);
//            // do nothing
//        }
//        else {
//            var $ele = $('img');
//            $ele.addClass('dropdown second');
//            $ele.attr('src', '/media/uikit/arrow-down.png');
//            $(ele).append($ele);
//        }
        $(ele).prepend('<img class="dropdown second" style="margin-top: auto" src="/media/uikit/arrow-down.png" />');
    }

    function attach2Menu () {
        var helper = $(".navigation>li").has("ul");
        helper.each(function (index) {
            $(helper[index]).addClass("topic");
        });

        var helper2 = $(".secondLevel>li").has ("ul");
        helper2.each (function (index) {
            $(helper2[index]).addClass ("topic");
        });

        $(".topic").each (function(index, value) {
            attachdropdown(value);
        });
    }

    attach2Menu ();

    /**
     *  toggles submenus visible or hidden via CSS class "hidden" on submenu class ".secondLevel"
     */
    function toggle2nd() {
        $(".secondLevel").not(this).addClass("hidden");
        $(".secondLevel", this).toggleClass( "hidden" );
    }
    /**
     *  toggles submenus visible or hidden via CSS class "hidden" on submenu class ".thirdLevel"
     */
    function toggle3rd() {
        $(".thirdLevel").not(this).addClass("hidden");
        $(".thirdLevel", this).toggleClass( "hidden" );
    }
    /**
     *  turns all submenus hidden via CSS class "hidden" on submenu class ".secondLevel" and ".thirdLevel"
     */
    function hideSubs () {
        $(".secondLevel").addClass("hidden");
        $(".thirdLevel").addClass("hidden");
    }

    /**
     *  toggles the whole menu visible or hidden for mobile view
     */
    function toggleMenu() {
        $("#menuItems").toggleClass("mobilenavigation");
        $("#menuItems").toggle();
    }

    /**
    * binds menu event handlers depending on screen size on load or re-binds them after resize
    *
    * unbinds event handlers for the case of resizing
    */
    function rebindMenuHandlers () {

        $("#navbutton").off("click", toggleMenu);
        $(".navigation>li").off("click mouseover", toggle2nd);
        $(".secondLevel>li").off("mouseover", toggle3rd);
        $(".thirdLevel").off("mouseout", hideSubs);
        console.log ("i am the rebinder");

        if ($(window)[0].innerWidth < 1000 ){
            $(".navigation li img").removeClass("hidden");
            $("#menuItems").hide();
            if ($(window)[0].innerWidth > 700 ){
                $("#menuItems").show();
            }
        } else {
            $("#menuItems").show();
        };
//            $(".navigation li img").addClass("hidden");
            $(".navigation>li").on("mouseover", toggle2nd);
            $(".secondLevel>li").on("mouseover", toggle3rd);
            $(".thirdLevel").on("mouseout", hideSubs);

            $("#navbutton").on("click", toggleMenu);
            $(".dropdown").on("click", toggle2nd);
            $(".dropdown").on("click", toggle3rd);
    }

    $(window).resize(function() {

        hideSubs(); /* hides ".secondLevel" in case menu actions with "click" had taken place */
        rebindMenuHandlers();
    });
    rebindMenuHandlers();

});
