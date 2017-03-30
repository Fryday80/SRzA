/**
 * Created by Fry on 29.03.2017.
 */

/**
 * animates the feedback response on ".success" or ".error" elements dependent from value of var selector
 *
 * @param selector  string either 'success' or 'error'
 * @param message   string optional own feedback message
 */
function animateFeedbackResponse(selector, message) {
    // declarations
    var $elements = [],
        origin_bg = [],
        origin_op = [],
        $feedbackBox;

    // declarations with standards:
    var background = ( selector == "success" ) ? "green" : "red",
        message = message || "";

    /**
     * construct the feedback box
     *
     * @param   selector    string either 'success' or 'error'
     * @returns {*|jQuery}  new constructed element
     */
    function createFeedbackBoxes (selector){
        var feedbackText = (selector == 'success')?'success':'error';
        $feedbackBox = $('<box class = "'+selector+'"><div class="feedbackMessage"></div></box>').appendTo("body");

        $('.feedbackMessage').html(feedbackText);
    }
    /**
     * erase the feedback box
     */
    function dumpFeedbackBoxes(){
        $feedbackBox.remove();
    }
    /**
     * inject custom feedbackmessage to html
     * @param selector  string either 'success' or 'error'
     * @param message   string of new message
     */
    function changeMessage(selector, message) {
        $('.feedbackMessage', '.'+selector).html(message);
    }

    /**
     * prepare & create feedback element
     *
     * @type {*|jQuery}
     */
    createFeedbackBoxes(selector);
    /**
     * insert custom feedback text if given
     */
    if (!(message == "")) {
        changeMessage(selector, message);
    }
    /**
     * create array of elements, where the animation is used
     *
     * @type {*[]}
     */
    $elements.push ( $(('.'+selector)) );
    // additional elements without class "success" or "error"
    $elements.push( $(".body.box") );

    jQuery.each($elements, function (index, $value) {
        console.log($value);
        origin_bg[index] = $value.css("background-color");
        origin_op[index] = $value.css("opacity") ? $value.css("opacity") : "1";
        $('.feedbackMessage').css("background", "linear-gradient(to right, "+background+" , lightgrey, "+background+")");
        $value.css("background-color", background)
            .css("opacity", "1")
            .css("display", "block");
        $value.animate({
                "opacity": origin_op[index],
                "background-color": origin_bg[index]
            },
            3000,
            function complete() {
                $value.removeAttr("style");
                $('.feedbackMessage').removeAttr("style");
                dumpFeedbackBoxes();
            }
        );
    });
}

window.feedback = {
    /**
     * trigger positive feedback
     *
     * @param newFeedbackMessage string optional message
     */
    success: function (newFeedbackMessage) {
        $(document).ready(animateFeedbackResponse('success', newFeedbackMessage))
    },
    /**
     * trigger negative feedback
     *
     * @param newFeedbackMessage string optional message
     */
    error: function (newFeedbackMessage) {
        $(document).ready(animateFeedbackResponse('error', newFeedbackMessage))
    }
}