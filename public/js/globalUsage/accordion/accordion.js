/** accordion **/
$(function() {
    //<accordion class="hightcontent"></accordion>
    $("accordion").each(function(i, ele) {
        $(ele).accordion({
            heightStyle: "content",
            icons: { "header": "", "activeHeader": "" }
        });
    })
});