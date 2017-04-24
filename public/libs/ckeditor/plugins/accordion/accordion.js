// CKEDITOR.plugins.add( 'accordion', {
//     icons: 'accordion',
//     init: function( editor ) {
//         editor.addCommand( 'insertAccordion', {
//             exec: function( editor ) {
//                 var titel = '',
//                     content = '';
//                 // get dialog to set titel & content
//                 editor.insertHtml( '<accordion><span>'+titel+'</span><div>+content+</div></accordion>' );
//             }
//         });
//         editor.ui.addButton( 'Accordion', {
//             label: 'Insert Accordion',
//             command: 'insertAccordion',
//             toolbar: 'insert,100'
//         });
//     }
// });

// Register the plugin within the editor.
CKEDITOR.plugins.add( 'accordion', {

    // Register the icons. They must match command names.
    icons: 'accordion',

    // The plugin initialization logic goes inside this method.
    init: function( editor ) {

        // Define the editor command that inserts a timestamp.
        editor.addCommand( 'insertAccordion', {

            // Define the function that will be fired when the command is executed.
            exec: function( editor ) {
                var now = new Date();

                // Insert the timestamp into the document.
                editor.insertHtml( 'The current date and time is: <em>' + now.toString() + '</em>' );
            }
        });

        // Create the toolbar button that executes the above command.
        editor.ui.addButton( 'Accordion', {
            label: 'Insert Accordion',
            command: 'insertAccordion',
            toolbar: 'insert'
        });
    }
});