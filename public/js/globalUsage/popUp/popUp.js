
$(document).ready(function () {

//    sessionStorage.clear(); // for testing reasons

    /** remove the href event default of the no js fallback **/
    function removeNoScriptFallback (){
        $(".disclaim").click(function( event ) {
            event.preventDefault();
        });
        $(".impressum").click(function( event ) {
            event.preventDefault();
        });
    }
    

    /**
     * 
     * @param title     string
     * @param content   string
     * @param buttons   e.g. {ok: functionXY, deny: functionYX}
     * @param popUpClass string
     * 
     * @return open dialog
     */
    function openPopup(title, content, buttons, popUpClass) {
        let ele,
            usedClass = popUpClass || "popUp",
            buttonSetup = buttons || {ok: closeButton};

        ele = $( "<div class='"+usedClass+"'></div>" )
            .html(content)
            .dialog({
                modal: true,
                title: title,
                height: "auto",
                width: "auto",
                open: onOpen,
                buttons: buttonSetup
            });
    }

    /**
     * removes X-button from title bar
     * to force decision via buttons
     */
    function onOpen() {
        $(".ui-dialog-titlebar-close").hide();
    }

    /**
     * close dialog
     */
    function closeButton(){
        sessionStorage.setItem('isshow', 1);
        $(this).dialog("close");
    }

    /**
     * redirect to google
     * e.g. if disclaimer is denied
     */
    function denyButtonToGoogle() {
        var url = "http://www.google.de";
        window.location = url;
    }

    /**
     * 
     * @param url       string .. guess what of an url
     * @param buttons   e.g. {ok: functionXY, deny: functionYX}
     * @param popUpClass string
     * 
     * @return opens dialog set up with url content
     */
    function openPopUpByUrl (url, buttons, popUpClass) {
        var title,
            content;
        $.ajax({
            url: url,
            async: true,
            json: true,
            success: function (e){
                title = e.title;
                content = (e.success)? e.content : e.error;
                openPopup(title, content, buttons, popUpClass);
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    /** run dialog for disclaimer */
    function disclaimerPop() {
        openPopUpByUrl('/disclaimer', {ok: closeButton, deny: denyButtonToGoogle}, 'disclaimer');
    }

    /** run dialog for impressum */
    function impressumPop(){
        openPopUpByUrl('/impressum', {ok: closeButton},'impressum');
    }

    if(sessionStorage && !sessionStorage.getItem('isshow')){
        disclaimerPop();
    }

    removeNoScriptFallback();
    $(".disclaim").on("click", function () {
        disclaimerPop();
    });
    $(".impressum").on("click", function () {
        impressumPop();
    });
});