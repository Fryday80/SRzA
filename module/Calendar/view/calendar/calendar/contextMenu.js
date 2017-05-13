(function($) {

    var defaultOptions = {
        desc: 'deine default settings hier rein',

    };

    class ContextMenu {
        constructor(ele, settings) {
            //so ele is des element mit der classe .szraContextMenu

            //ele.on('contextmenu??') e.preventDefault() oder return false
            var context = ('<ul class="context items"> <li> Termine <ul> <li class="context item-1">Neu ...</li><li class="context item-2">Bearbeiten</li><li class="context item-3">Löschen</li></ul></li></ul>');
        }
    }

    $.fn.srzaContextMenu = function(options) {
        let settings = $.extend(defaultOptions, options);
        return this.each(function() {
            //für jedes element des ein context menu bekommt ein neues erstellen und mit $.data aufs element schreiben
            let instance = new ContextMenu(this, settings);
            // instance.init();
            $(this).data('contextMenu', instance);
        });
    };

    $.fn.getContextMenu = function() {
        let instance = $(this).data('contextMenu');
        if (!(instance instanceof ContextMenu)) {
            throw new Error('This element has no ContextMenu');
        }
        return instance;
    };
}(jQuery));

$('.szraContextMenu').srzaContextMenu({
    desc: "und hier kanste überschreiben"
});