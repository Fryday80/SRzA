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

        // $("ul>li>a").before ('<img class="links" src="../img/uikit/link.png">');
        $("a").before ('<img class="links" src="/img/uikit/link.png">');
    }
}
