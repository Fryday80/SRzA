/**
 * Created by salt on 25.04.2017.
 */
(function() {
    "use strict";
    let $workspace = $('.tree').workspace({
        // zoom: true
        defaultLeft: 100,
        defaultTop: 1000,
    });
    let workspace = $workspace.getWorkspace();
    workspace.click(function(e) {
        let $family = ($(e.target).is('.family'))? $(e.target): $(e.target).parents('.family');
        if ($family.length > 0) {
            //clicked on family
            $family = $($family[0]);
            workspace.goToElement($family, 2);
            $('*', workspace.$dragable).removeClass('centered');
            $family.addClass('centered');
            return false;
        }
        let $char = ($(e.target).is('.character'))? $(e.target): $(e.target).parents('.character');
        if ($char.length > 0) {
            //clicked on character
            $char = $($char[0]);
            workspace.goToElement($('character', $char));
            $('*', workspace.$dragable).removeClass('centered');
            $char.addClass('centered');
            return false;
        }
        $('*', workspace.$dragable).removeClass('centered');
    });
    $(document).ready(function() {
        let $family = $('.ws-content>ul>li');
        console.log($family)
        workspace.goToElement($family);
    });
})();
