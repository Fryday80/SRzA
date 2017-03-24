window.last_fixes = {
    organize: function style () {
        var css = [];
        css.push (".rightbarup");
        $(css).each(function (i, ele) {
            $("label", $(ele))
                .after('<br>')
                .addClass("width_90");

            $("input", $(ele))
                .after('<br>')
                .addClass("width_90");

            $("fieldset", $(ele)).addClass('width_95');

            /**
             * changes in Nav Sort
             */
/*
            $("#scroller").simplyScroll({
                autoMode: 'loop',
                className: 'vert',
                horizontal: false,
                frameRate: 20,
                speed: 3
            });  */
        });

        /**
         * manages Content switching
         */

        $(".angebote .box").not(".switchable .box").css("max-width", "50%")
            .css("margin", "1vw")
            .css("box-shadow", "0.5vw 0.5vw 0.5vw grey");
        $(".switchable .titel").addClass("titi")
            .removeClass("titel");
        $(".switchable .content").addClass("conti")
            .removeClass("content");
        $(".switchable")
            .css("box-shadow", "0.5vw 0.5vw 0.5vw grey")
            .css("margin", "1vw")
            .css("width", "40%")
            .css("float", "left");
        $(".switchable .conti").toggle();
        $(".switchable").on("click", function (e){
            $(".conti", this)
                .toggle("content");
            $(".titi", this)
                .toggle("titel");
            console.log ("huhu");
        });

    }
}
$(function() {
    //<accordion class="hightcontent"></accordion>
    $("accordion").each(function(i, ele) {
        $(ele).accordion({
            heightStyle: "content",
            icons: { "header": "", "activeHeader": "" }
        });
    })
});