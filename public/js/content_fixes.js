window.content_fixes = {
    veranstalter: function style () {
        //$(".offer").addClass("hidden");
        content_fixes.hideAll();
        $(".offerclick1").on("click", function (){
            content_fixes.hideAll();
            $(this).addClass("bgcol");
            $(".offer1").removeClass("hidden");
        } );
        $(".offerclick2").on("click", function (){
            content_fixes.hideAll();
            $(this).addClass("bgcol");
            $(".offer1").removeClass("hidden");
            $(".offer2").removeClass("hidden");
        } );
        $(".offerclick3").on("click", function (){
            content_fixes.hideAll();
            $(this).addClass("bgcol");
            $(".offer1").removeClass("hidden");
            $(".offer2").removeClass("hidden");
            $(".offer3").removeClass("hidden");
            $(".offer3_1").removeClass("hidden");
        } );
        $(".offerclick4").on("click", function (){
            content_fixes.hideAll();
            $(this).addClass("bgcol");
            $(".offer1").removeClass("hidden");
            $(".offer2").removeClass("hidden");
            $(".offer3").removeClass("hidden");
            $(".offer4").removeClass("hidden");
            $(".offer3_1").not(".hidden").addClass("hidden");
        } );
        $(".offershide").on("click", function (){
            content_fixes.hideAll();
        });
    },
    hideAll: function () {
        $(".offer").not("hidden").addClass("hidden");
        $(".offerclick").removeClass("bgcol");

    }
}
