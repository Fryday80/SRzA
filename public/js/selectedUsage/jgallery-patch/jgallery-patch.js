$(document).ready(function(){
    /** waiting for the DOM **/
    setTimeout(function(){

        function fixZIndex(){
            if ($("span.fa.change-mode").hasClass('fa-compress')) {
                $('#content').css("z-index",2000);
                $('.logout').hide();
            }
            else {
                $('#content').removeAttr("style");
                $('.logout').show();
            }
        }
        
        $( "span.fa.change-mode" )      //das ist das element mim icon für fullscreen $( ".change-mode" ) müsste sogar reichen
            .on("click", function(){
                fixZIndex();
            });
    },10);
});