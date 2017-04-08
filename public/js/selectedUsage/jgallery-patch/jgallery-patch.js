/**
 * Created by Fry on 08.04.2017.
 */
$(document).ready(function(){

    function testMode(){
        console.log('yeehaw');
    }


    $( "span.fa.change-mode" )
        .on("click", function(){
            testMode();
        });
    console.log($( "jgallery-full-screen" ));
});