console.log (window.innerWidth + "px width -- refreshes on reload");

$(document).ready(function () {

    /**
     *  toggles submenus visible or hidden via CSS class "hidden" on submenu class ".secondLevel"
     */
    function toggleSub() {
        $(".secondLevel").addClass("hidden");
        $(".secondLevel", this).toggleClass( "hidden" );
    }
    /**
     *  turns submenus hidden via CSS class "hidden" on submenu class ".secondLevel"
     */
    function hideSubs () {
        $(".secondLevel").addClass("hidden");
    }

    /**
     *  toggles the whole menu visible or hidden for mobile view
     */
    function toggleMenu() {
        $("#menuItems").toggleClass("mobilenavigation");
        $("#menuItems").toggle();
    }

    /**
    * binds menu event handlers depending on screen size on startup or re-binds them after resize
    *
    * unbinds event handlers for the case of resizing
    */
    function rebindMenuHandlers () {

        $("#navbutton").off("click", toggleMenu);
        $("#mainMenu li").off("click mouseover mouseout", toggleSub);
        console.log ("i am the rebinder");

        if ($(window)[0].innerWidth < 1000 ){
            $("#menuItems").hide();
            $("#navbutton").on("click", toggleMenu);
            $("#mainMenu li").on("click", toggleSub);
            if ($(window)[0].innerWidth > 700 ){
                $("#menuItems").show();
            }
        } else {
            $("#mainMenu li").on("mouseover mouseout", toggleSub);
            $("#menuItems").show();
        };
    }

    $(window).resize(function() {

        hideSubs(); /* hides ".secondLevel" in case menu actions with "click" had taken place */
        rebindMenuHandlers();
    });
    rebindMenuHandlers();

});
