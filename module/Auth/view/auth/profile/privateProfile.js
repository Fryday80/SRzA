(function () {
    "use strict";

    var characters = [],
        template = document.querySelector('#characterListItemTemplate'),
        $characters = $('#characters'),
        $charFormBox = $('#charForm'),
        $charForm = $('#Character'),
        $charList = $('.characterList'),
        $charFormTitle = {
            $command: $('boxtitel .command', $charFormBox),
            $charName: $('boxtitel .charName', $charFormBox),
        };


    function scrollToCharForm() {
        $('html, body').animate({
            scrollTop: $charFormBox.offset().top
        }, 500);
    }
    function scrollToCharSelect() {
        $('html, body').animate({
            scrollTop: $characters.offset().top
        }, 500);
    }
    function showCharForm() {
        removeAllCharFormErrors();
        $charFormBox.removeClass('hide');
        $charFormBox.addClass('show');
    }
    function hideCharForm() {
        $charFormBox.addClass('show');
        $charFormBox.removeClass('hide');
    }
    function getCharByID(id) {
        for(let i = 0; i < characters.length; i++) {
            if (parseInt(characters[i].id) == id) {
                return characters[i];
            }
        }
    }
    function createCharElement(char) {
        var clone = document.importNode(template.content, true);
        $('li', clone)
            .data('id', char.id)
            .text(char.name);
        $charList.append(clone);
    }
    function removeAllCharFormErrors() {
        $charForm.children('ul').remove();
    }
    //ajax call
    function loadChars() {
        $charList.find('li').remove();
        let data = {
            method: 'getChars',
        };
        let promise = $.ajax({
            url: "/profileJson",
            type: "POST",
            data: JSON.stringify(data)
        });

        promise.fail(function(jqXHR, textStatus, errorThrown) {
            //@todo handle error
            console.error(textStatus);
            //@todo remove load animation and show element
        });
        promise.done(function(e, textStatus, jqXHR) {
            //@todo on error is not decoded
            //e = JSON.parse(e);
            if (e.error) {
                //@todo handle error
                console.error(e);
            } else {
                //add characters
                let chars = e.data;
                if (!chars) return;
                characters = chars;
                for (let i = 0; i < chars.length; i++) {
                    createCharElement(chars[i]);
                }
            }
            //@todo remove load animation and show element
        });
    }
    function saveChar(id, formData) {
        let data = {
            method: 'saveChar',
            id: id,
            data: formData
        };
        let promise = $.ajax({
            url: "/castmanager/characters/jsonOwnerEdit",
            type: "POST",
            data: JSON.stringify(data)
        });

        promise.fail(function(jqXHR, textStatus, errorThrown) {
            //@todo handle error
            console.error(textStatus);
            //@todo remove load animation and show element
        });
        promise.done(function(e, textStatus, jqXHR) {
            //@todo on error is not decoded
            //e = JSON.parse(e);
            if (e.error) {
                if (e.code == 1) {
                    removeAllCharFormErrors();
                    // $('#Character input[name="id"]').val(char.id);
                    let errors = e.formErrors;
                    if (errors.name) $('#Character input[name="name"]').parent('label').after('<ul><li>'+ errors.name.isEmpty +'</li></ul>');

                    // $('#Character input[name="name"]').parent('label').next().val(char.name);
                    if (errors.surename) $('#Character input[name="surename"]').parent('label').after('<ul><li>'+ errors.surename.isEmpty +'</li></ul>')
                    // $('#Character input[name="gender"]').val([char.gender]);
                    // $('#Character input[name="birthday"]').val(char.birthday);
                    // $('#Character input[name="vita"]').val(char.vita);




                    // $('#Character select[name="family_id"]').val(parseInt(char.family_id));
                    // $('#Character select[name="tross_id"]').val(parseInt(char.tross_id));
                    // $('#Character select[name="job_id"]').val(parseInt(char.job_id));
                }
            } else {
                switch (e.code) {
                    case 200:
                        //saved
                        let char = getCharByID(id);
                        characters[characters.indexOf(char)] = e.data;
                        break;
                    case 201:
                        //new created
                        characters.push(e.data);
                        createCharElement(e.data);
                        break;
                }
                scrollToCharSelect();
                hideCharForm();
            }
            //@todo remove load animation and show element
        });
    }
    function reloadSelect($select, funcName, familyID, cb) {
        //@todo hide element and show load animation
        //remove guardians
        $select.find('option').remove();
        let data = {
            method: funcName,
            familyID: familyID,
        };
        let promise = $.ajax({
            url: "/castmanager/characters/json",
            type: "POST",
            data: JSON.stringify(data)
        });

        promise.fail(function(jqXHR, textStatus, errorThrown) {
            //@todo handle error
            console.error(textStatus);
            //@todo remove load animation and show element
            if (cb) cb('error');
        });
        promise.done(function(e, textStatus, jqXHR) {
            //@todo on error is not decoded
            //e = JSON.parse(e);
            if (e.error) {
                //@todo handle error
                console.error(e);
                if (cb) cb('error');
            } else {
                //add guardians
                let guardians = e.data;
                $select.append($('<option>', {
                    value: 0,
                    text: 'keiner'
                }));
                for (let i = 0; i < guardians.length; i++) {
                    $select.append($('<option>', {
                        value: guardians[i].id,
                        text: guardians[i].name
                    }));
                }
            }
            //@todo remove load animation and show element
            if (cb) cb('success');
        });
    }
    loadChars();
    //register events on form elements
    $("select[name='family_id']").on('change', function() {
        let familyID = $(this).val();
        let $select = $('select[name="guardian_id"]');
        reloadSelect($select, "getPossibleGuardians", familyID);
    });
    $("select[name='tross_id']").on('change', function() {
        let familyID = $(this).val();
        let $select = $('select[name="supervisor_id"]');
        reloadSelect($select, "getPossibleSupervisors", familyID);
    });

    //register events on char list
    $('.characterList').on('click', function(e) {
        if ($(e.target).is('li')) {
            //select element
            $('.characterList li').removeClass('selected');
            $(e.target).addClass('selected');
            let charID = parseInt($(e.target).data('id'));
            let char = getCharByID(charID);
            if (!char) {
                hideCharForm();
                return;
            }
            $charFormTitle.$charName.text('"'+char.name+' '+char.surename+'"');
            $charFormTitle.$command.text('Edit');
            showCharForm();

            scrollToCharForm();
            //populate values to form
            $('#Character input[name="id"]').val(char.id);
            $('#Character input[name="name"]').val(char.name);
            $('#Character input[name="surename"]').val(char.surename);

            $('#Character input[name="gender"]').val([char.gender]);
            $('#Character input[name="birthday"]').val(char.birthday);
            $('#Character input[name="vita"]').val(char.vita);

            $('#Character select[name="family_id"]').val(parseInt(char.family_id));
            $('#Character select[name="tross_id"]').val(parseInt(char.tross_id));
            $('#Character select[name="job_id"]').val(parseInt(char.job_id));
            //trigger change event on fam and tross
            let familyID = $("select[name='family_id']").val();
            var $guardianSelect = $('select[name="guardian_id"]');
            reloadSelect($guardianSelect, 'getPossibleGuardians', familyID, function() {
                $guardianSelect.val(parseInt(char.guardian_id));
            });

            familyID = $("select[name='tross_id']").val();
            var $familySelect = $('select[name="supervisor_id"]');
            reloadSelect($familySelect, 'getPossibleSupervisors', familyID, function() {
                $familySelect.val(parseInt(char.supervisor_id));
            });
        }
    });
    $('#Character').submit(function(e) {
        let data = $('#Character').serializeArray();
        let beautyData = {};
        for(let i = 0; i < data.length; i++) {
            beautyData[data[i].name] = data[i].value;
        }
        let id = $('#Character input[name="id"]').val();
        saveChar(id, beautyData);
        e.preventDefault();
    });
    $('#addCharButton').on('click', function() {
        //deselect
        $('.characterList li').removeClass('selected');
        //clear form and open
        $('#Character input[name="id"]').val(-1);
        $('#Character input[name="name"]').val('');
        $('#Character input[name="surename"]').val('');

        $('#Character input[name="birthday"]').val('');
        $('#Character input[name="vita"]').val('');

        $('#Character select[name="family_id"]').val(0);
        $('#Character select[name="tross_id"]').val(0);
        $('#Character select[name="job_id"]').val(0);

        $charFormTitle.$charName.text('');
        $charFormTitle.$command.text('Neuer');
        showCharForm();
        scrollToCharForm();
    })
})();