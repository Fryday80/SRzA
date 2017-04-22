(function () {
    "use strict";

    var characters = [],
        template = document.querySelector('#characterListItemTemplate'),
        $charForm = $('#charForm'),
        $charFormTitle = {
            $command: $('boxtitel .command', $charForm),
            $charName: $('boxtitel .charName', $charForm),
        };

    function showCharForm() {
        $charForm.removeClass('hide');
        $charForm.addClass('show');
    }
    function hideCharForm() {
        $charForm.addClass('show');
        $charForm.removeClass('hide');
    }
    function getCharByID(id) {
        for(let i = 0; i < characters.length; i++) {
            if (parseInt(characters[i].id) === id) {
                return characters[i];
            }
        }
    }
    //ajax call
    function loadChars() {
        let $charList = $('.characterList');
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
                    var clone = document.importNode(template.content, true);
                    $('li', clone)
                        .data('id', chars[i].id)
                        .text(chars[i].name);
                    console.dir(clone);
                    $charList.append(clone);
                    // $charList.append($('<li>', {
                    //     'data-id': chars[i].id,
                    //     text: chars[i].name
                    // }));
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
                //@todo handle error
                console.error(e);
            } else {
                //add characters
//                    let chars = e.data;
//                    if (!chars) return;
//                    characters = chars;
//                    for (let i = 0; i < chars.length; i++) {
//                        $charList.append($('<li>', {
//                            'data-id': chars[i].id,
//                            text: chars[i].name
//                        }));
//                    }
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

            $('html, body').animate({
                scrollTop: $charForm.offset().top
            }, 500);
            //populate values to form
            $('#Character input[name="id"]').val(char.id);
            $('#Character input[name="name"]').val(char.name);
            $('#Character input[name="surename"]').val(char.surename);
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
        $('#Character select[name="family_id"]').val(0);
        $('#Character select[name="tross_id"]').val(0);
        $('#Character select[name="job_id"]').val(0);

        $charFormTitle.$charName.text('');
        $charFormTitle.$command.text('Neuer');
        showCharForm();

        $('html, body').animate({
            scrollTop: $charForm.offset().top
        }, 500);
    })
})();