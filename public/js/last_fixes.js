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
        });

        $(".bodycontainer a").prepend ('<img class="links" src="/img/uikit/link.png">');
        $("#headlinks a").prepend ('<img class="links" src="/img/uikit/link.png">');
    }
}
