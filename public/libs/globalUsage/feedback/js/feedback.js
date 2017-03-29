/**
 * Created by Fry on 29.03.2017.
 */


function animateFeedbackResponse(selector) {
    var $elements = [$("#content"), ( selector == "success" ) ? $(".success") : $(".error")];
    var background = ( selector == "success" ) ? "green" : "red";
    var origin_bg = [];
    var origin_op = [];

    jQuery.each($elements, function (index, $value) {
        origin_bg[index] = $value.css("background-color");
        origin_op[index] = $value.css("opacity") ? $value.css("opacity") : "1";
        $value.css("background-color", background)
            .css("opacity", "1")
            .css("display", "block");
        $value.animate({
                "opacity": origin_op[index],
                "background-color": origin_bg[index]
            },
            2000,
            function complete() {
                $value.removeAttr("style");
            }
        );
    });
}
    
window.feedback = {
    success: function () {
        $(document).ready(animateFeedbackResponse('success'))
    },
    error: function () {
        $(document).ready(animateFeedbackResponse('error'))
    }
}