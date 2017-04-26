/**
 * Created by salt on 25.04.2017.
 */
(function() {
    "use strict";
    let $workspace = $('.tree').workspace({
        // zoom: true
    });
    let workspace = $workspace.getWorkspace();
    workspace.click(function(e) {
        let $family = $(e.target).parents('.family');
        if ($family.length > 0) {
            workspace.goToElement($family, 2);
            $('*', workspace.$dragable).removeClass('centered');
            $family.addClass('centered');
        }
    });
})();
