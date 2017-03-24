/**
 * @license Copyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	config.language = 'de';
	config.uiColor = '#AADC6E';
    config.filebrowserBrowseUrl = "/media/filebrowser";
    config.extraPlugins = 'iframedialog';
    config.allowedContent = true;
    // {
    //     script: true,
    //         div: true,
    //         accordion: true,
    //         box: true,
    //
    //         $1: {
    //         // This will set the default set of elements
    //         elements: CKEDITOR.dtd,
    //             attributes: true,
    //             styles: true,
    //             classes: true
    //     }
    // }
};
CKEDITOR.on('dialogDefinition', function (event)
{
    var editor = event.editor;
    var dialogDefinition = event.data.definition;
    var dialogName = event.data.name;
    var dialogObj;
    var _fm,
        _fmModel;

    CKEDITOR.dialog.addIframe("fileManager", "test title", "", 400, 400, function(e) {}, {
        resizable: CKEDITOR.DIALOG_RESIZE_BOTH,
        onOk : function()
        {
            var iframeWin = dialogObj.definition.getContents('iframe').elements[0].contentWindow;
            var selected = _fmModel.itemsModel.getSelected();
            if (selected.length === 0 && !selected[0].hasOwnProperty("getUrl")) {
                _fm.error("es muss eine datei ausgewählt sein", {
                    delay: 4000
                });
                return false;
            }
            CKEDITOR.tools.callFunction(CKEDITOR.instances[event.editor.name]._.filebrowserFn, selected[0].getUrl());
        },
    });

    var cleanUpFuncRef = CKEDITOR.tools.addFunction(function () {
        dialogObj.hide();
    });
    var selectionChangedRef = CKEDITOR.tools.addFunction(function (fm, fmModel) {
        _fmModel = fmModel;
        _fm = fm;
    });

    var tabCount = dialogDefinition.contents.length;
    for (var i = 0; i < tabCount; i++) {
        var browseButton = dialogDefinition.contents[i].get('browse');
        if (browseButton !== null) {
            browseButton.hidden = false;
            browseButton.onClick = function (dialog, i) {
                editor._.filebrowserSe = this;
                var src = 'http://localhost/media/filebrowser' + // Change it to wherever  Filemanager is stored.
                '?CKEditorFuncNum=' + CKEDITOR.instances[event.editor.name]._.filebrowserFn +
                '&CKEditorCleanUpFuncNum=' + cleanUpFuncRef +
                '&DialogSelectionChangedFuncNum=' + selectionChangedRef +
                '&langCode=de' +
                '&CKEditor=' + event.editor.names


                dialogObj = new CKEDITOR.dialog(editor, 'fileManager');
                dialogObj.definition.getContents('iframe').elements[0].src = src;
                dialogObj.show();
            }
        }
    }
}); // dialogDefinition
