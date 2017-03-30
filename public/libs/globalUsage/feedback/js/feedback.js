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
    var selection,
        $feedbackBox,
        appendToHTML,
        feedbackText,
        background,
        $elements = [],
        origin_bg = [],
        origin_op = [],
        feedbackDuration;

    // standards:
    selection = '.'+selector;
    appendToHTML = "body";
    feedbackText =  ( selector == "success" ) ? "success" : "error";
    background =    ( selector == "success" ) ? "green" : "red";
    message = message || "";
    feedbackDuration = 3000;

    /**
     * construct the feedback box
     *
     * @param   selector    string either 'success' or 'error'
     * @returns {*|jQuery}  new constructed element
     */
    function createFeedbackBoxes (selector){
        $feedbackBox = $('<div class = "'+selector+' box"><div class="feedbackMessage"></div></div>').appendTo(appendToHTML);

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
    function changeMessage(selection, message) {
        $('.feedbackMessage', selection).html(message);
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
        changeMessage(selection, message);
    }
    /**
     * create array of elements, where the animation is used
     *
     * @type {*[]}
     */
    $elements.push ( $(selection) );
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
            feedbackDuration,
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