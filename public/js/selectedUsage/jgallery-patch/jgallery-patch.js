/**
 * Created by Fry on 08.04.2017.
 */
$(document).ready(function(){
    /** debug info **/
   deBugger('File: jgallery-patch.js');

    /** waiting for the DOM **/
    setTimeout(function(){

        function fixZIndex(){
            deBugger('func fixZIndex triggered');
            if ($("span.fa.change-mode").hasClass('fa-compress')) {
                $('#content').css("z-index",2000);
                $('.logout').hide();
                deBugger('fullscreen detected')
            }
            else {
                $('#content').removeAttr("style");
                $('.logout').show();
            }
        }
        
        $( "span.fa.change-mode" )      //das ist das element mim icon für fullscreen $( ".change-mode" ) müsste sogar reichen
            .on("click", function(){
                deBugger('click on $( "span.fa.change-mode" ) triggered');
                fixZIndex();
            });
        deBugger($( "span.fa.change-mode" ));
        deBugger("jgallery-patch loaded");
    },10);
});