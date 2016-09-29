window.navigation = {};
(function () {
    var listeners = {};
    
    navigation.trigger = function (type) {
        var i = 0;
        for (i = 0; i < listeners[type].length; i++) {
            listeners[type][i]();
        }
    };
    navigation.on = function (type, cb) {
        if (!Array.isArray(listeners[type]) ) {
            listeners[type] = [];
        }
        listeners[type].push(cb);
    };
    
/* ---------------------------------------------------------------------------------------------------*/
    
    $(document).ready(function () {
        
        navigation.trigger("error_one");
        
        function showSub($li) {
            
            if ($li == "#mainMenu"){
                $(".secondLevel").addClass("hidden");
                $(".secondLevel", this).removeClass(hidden);
                
            } else 
            if ($li == "#mobileMenu"){
                
            }
//            $("#mobilemenu ul").addClass("hidden");
/*            $("#mobilemenu ul").not(this).addClass("hidden");
            $("ul", $li).removeClass("hidden");*/
//
        };
          
        $("#mainMenu li").on("click", function() {
            showSub($(this));
            navigation.trigger("error_two");
        });
          /*  $("#mainMenu li").on("mouseover", function () {
                var flag;
              //  $("#mainMenu ul").not(this).hide();//des schreit nach nen bug...kann sein, das war für das clicken notwendig
                $("ul", this).show();
                var clickHandler = function () {
                    Submenu = 1;
                    console.log (Submenu);
                }
                $(this).on("click", clickHandler);
                //mouseout
                var reset = function() {
                    $(this).off("click", clickHandler);
                    $(this).off("mouseout", this);
                }
                $(this).on("mouseout", reset);
                
                if (Submenu !== 1) {
                    $(this) || ("ul li", this).on("mouseout", function () {
                        $("ul", this).hide();
                    });
    //                // eigentlich sollte er hier die mouseout funktion nicht starten, wenn geklickt wurde.. geht aber ned
                }
                navigation.trigger("error_two");
            });  */
    /* -------------------mobile view ----------------------------- */
        $("#navbutton").on("click", function () {
            $("#mobilemenutopics").toggleClass("hidden");
        });
        
        $("#mobilemenu li").on("click", function() {
            showSub($("#mobilemenu"));
            navigation.trigger("error_two");
        });
        

    });
})();
    
navigation.on("error_one", function() {
  console.log("nice wenns funtz :)");  
})
navigation.on("error_two", function() {
  console.log("nice wenns funtz :)  auch 2 mal");  
})