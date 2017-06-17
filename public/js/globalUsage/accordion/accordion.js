/** accordion **/
$(function() {
    //<accordion class="hightcontent"></accordion>
    $("accordion").each(function(i, ele) {
        $(ele).accordion({
            heightStyle: "content",
            icons: { "header": "", "activeHeader": "" }
        });
    })
});
(function($) {
    "use strict";
    function populateFormData(formEle, data) {
        clearFormData(formEle);
        $('input, select, textarea', formEle).each(function() {
            let name = $(this).attr('name');
            if (data.hasOwnProperty(name) ) {
                $(this).val(data[name]);
            }
        });
    }
    function clearFormData(ele) {
        if ($(ele).is('form')) {
            $('input[type!="submit"], select, textarea', ele).each(function() {
                $(this).val('');
            });
        } else if ($(ele).is('input[type!="submit"], select, textarea') ) {
            $(ele).val('');
        }
    }
    function clearErrors(formEle) {
        $('.form-error-messages', formEle).remove();
    }
    $.fn.formSetErrors = function(errors = {}, clear = true) {
        if (clear) clearErrors(this);
        //clean array data
        for (var errorName in errors) {
            if (errors.hasOwnProperty(errorName)) {
                var error = errors[errorName];
                if (typeof errors[errorName] === 'object' && !Array.isArray(errors[errorName]) ) {
                    var asArray = [];
                    for (var errorMsg in errors[errorName]) {
                        asArray.push(errors[errorName][errorMsg]);
                    }
                    errors[errorName] = asArray;
                }
            }
        }
        $('input[type="radio"]', this).each(function() {
            let name = $(this).attr('name');
            if (errors.hasOwnProperty(name)) {
                //continue till last label
                if ($(this).parent('label').next('label').length > 0) return;
                let error = errors[name];
                let $errorUl = $('<ul class="form-error-messages"></ul>');
                console.log($(this).parent('label').parent('fieldset'));
                $(this).parent('label').after($errorUl);
                for (let i = 0; i < error.length; i++) {
                    let $li = $errorUl.append('<li>' + error[i] + '</li>');
                    $errorUl.append($li);
                }
            }
        });
        $('input:not([type="radio"]), select, textarea', this).each(function() {
            let name = $(this).attr('name');
            if (errors.hasOwnProperty(name)) {
                let error = errors[name];
                let $errorUl = $('<ul class="form-error-messages"></ul>');
                $(this).after($errorUl);
                for (let i = 0; i < error.length; i++) {
                    let $li = $errorUl.append('<li>' + error[i] + '</li>');
                    $errorUl.append($li);
                }
            }
        });
        return this;
    };
    $.fn.formClearErrors = function() {
        clearErrors(this);
        return this;
    };
    $.fn.formPush = function(data) {
        if (!this.is('form')) return this;
        populateFormData(this, data);
        return this;
    };
    /**
     * returns object of form data. properties are the name attributes value
     * @returns {*}
     */
    $.fn.formPull = function() {
        if (!this.is('form')) return this;
        let data = this.serializeArray(),
            beautyData = {};
        for(let i = 0; i < data.length; i++) {
            beautyData[data[i].name] = data[i].value;
        }
        return beautyData;
    };
    /**
     * clear form data from <form>, <input> or <select>
     * @param elementName only if it's called on a <form>. Removes only the data from the <input> or <select> with the elementName in the name attribute
     * @returns {jQuery}
     */
    $.fn.formClear = function(elementName = null) {
        if ($(this).is('form')) {
            if (elementName === null) {
                clearFormData(this);
            } else {
                clearFormData($('input[name="'+elementName+'"], select[name="'+elementName+'"], textarea[name="'+elementName+'"]'));
            }
        } else if ($(this).is('input, select, textarea')) {
            clearFormData(this);
        }
        return this;
    };
})(jQuery);