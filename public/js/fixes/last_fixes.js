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