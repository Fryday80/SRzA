//global js for pnotify shorthandlers and ajax functions with build in error handling
(function(){
    "use strict";
    function prepareData(type, data) {
        switch(type) {
            case http.JSON_DATA:
                return {
                    contentType : 'application/json',
                    dataType: 'json',
                    data: JSON.stringify(data)
                };
                return ;
            case http.FORM_DATA:
                data = $(data).serializeArray();
                let beautyData = {};
                for(let i = 0; i < data.length; i++) {
                    beautyData[data[i].name] = data[i].value;
                }
                return {
                    contentType : 'application/json',
                    dataType: 'json',
                    data: JSON.stringify(beautyData)
                };
            case http.BLOB_DATA:
                if (typeof data !== 'object' || !data.blob || !data.name) {
                    throw new Error('data must be a object in form: { blob: BLOB_OBJECT, name: BLOB_NAME}');
                }
                if (data.blob instanceof Blob) {
                    var formData = new FormData();
                    formData.append('image', data.blob, data.name);
                    return {
                        contentType: false,
                        dataType: 'json',
                        data: formData
                    };
                } else
                    throw new Error('data is not a blob!');
        }
    }
    function base64ToBlob(base64, mime) {
        mime = mime || '';
        var sliceSize = 1024;
        var byteChars = window.atob(base64);
        var byteArrays = [];

        for (var offset = 0, len = byteChars.length; offset < len; offset += sliceSize) {
            var slice = byteChars.slice(offset, offset + sliceSize);

            var byteNumbers = new Array(slice.length);
            for (var i = 0; i < slice.length; i++) {
                byteNumbers[i] = slice.charCodeAt(i);
            }

            var byteArray = new Uint8Array(byteNumbers);

            byteArrays.push(byteArray);
        }
        return new Blob(byteArrays, {type: mime});
    }
    window.apps.http = window.http = {
        JSON_DATA: 0,
        FORM_DATA: 1,
        BLOB_DATA: 2,
        get: function() {},
        postJson: function(url, data, success = false, progress = false, error = true) {
            return this.post(url, this.JSON_DATA, data, success, progress, error);
        },
        postForm: function(url, formElement, success = false, progress = false, error = true) {
            return this.post(url, this.FORM_DATA, data, success, progress, error);
        },
        postBlob: function(url, blob, name, success = false, progress = false, error = true) {
            let data =  {
                blob: blob,
                name: name
            };
            return this.post(url, this.BLOB_DATA, data, success, progress, error);
        },
        /**
         * @param url {string}
         * @param dataType {number} http.JSON_DATA, http.FORM_DATA or http.BLOB_DATA
         * @param data
         * @param progress {bool}
         * @param success {bool}
         * @param error {bool}
         */
        post(url, dataType, data, success = false, progress = false, error = true) {
            var progressValue = 0.0,
                progressState = 'Start Loading';

            if (!url || typeof url !== 'string') console.error('url must be set!');

            if (progress)
                notify.startProgress("Upload to " + url);

            data = prepareData(dataType, data);

            let opt = {
                type: 'POST',
                url: url,
                data: data.data,
                dataType: data.dataType,
                processData: false,
                cache: false,
            };
            if (dataType != this.JSON_DATA) {
                opt.contentType = data.contentType;
            }
            opt.success = function(data, textStatus, jqXHR) {
                if (progress)
                    notify.stopProgress(url);
            };
            opt.error = function(jqXHR, textStatus, errorThrown) {
                if (error && error === true) {
                    window.notify.error(errorThrown, 'Ajax Error');
                    notify.stopProgress(url);
                }
            };
            opt.complete = function(data) {
                // console.log(data);
            };
            opt.xhr = function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        progressState = 'Send request';
                        progressValue = (evt.loaded / evt.total) / 2;
                        if (progress)
                            notify.setProgress(url, progressValue, progressState)
                    }
                }, false);
                xhr.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        progressState = 'Receiving data';
                        progressValue = (evt.loaded / evt.total) / 2 + 0.5;
                        if (progress)
                            progress(progressValue, progressState);
                    }
                }, false);
                return xhr;
            };
            return $.ajax(opt);
        },
    };
    /** @type {Progress[]} */
    var tasks = [],
        taskNameHash = [];

    class Progress {
        constructor(name, title = name) {
            this.name = name;
            this.value = 0;
            this.postTimeout = 400;
            var self = this;
            this.loader = new PNotify({
                title: title,
                text:  '<progress value="0" max="1" class="progress-bar" role="progressbar"></progress>\
                        <span class="value">0%</span>',
                // type: 'info',
                hide: false,
                addclass: 'stack-modal',
                stack: {'dir1': 'down', 'dir2': 'right', 'firstpos1': 150 , 'modal': true},
                buttons: {
                    closer: false,
                    sticker: false
                },
                history: {
                    history: false
                },
                before_open: function(notice) {
                    self.$valueEle = notice.get().find(".value");
                    self.$progress = notice.get().find("progress");
                    self.$progress.val(0);
                    self.$valueEle.html(0 + "%");
                }
            });
        }

        /**
         * @param amount
         * @param text
         * @returns {boolean}
         */
        do(amount, text) {
            this.value += amount;
            if (!text) text = this.loader.title;
            this.loader.update({
                title: text,
                icon: "fa fa-circle-o-notch fa-spin"
            });
            this.$progress.val(this.value);
            this.$valueEle.html(Math.round(this.value * 100) + "%");
            if (this.value >= 1) return this.end();
            return false;
        }
        set(value, text) {
            this.value = value;
            if (!text) text = this.loader.title;
            this.loader.update({
                title: text,
                icon: "fa fa-circle-o-notch fa-spin"
            });
            this.$progress.val(this.value);
            this.$valueEle.html(Math.round(this.value * 100) + "%");
            if (this.value >= 1) return this.end();
            return false;
        }

        /**
         * @returns {boolean}
         */
        end() {
            var self = this;
            setTimeout(function(){
                self.stop();
            }, this.postTimeout);
            return true;
        }
        stop() {
            this.loader.remove();
        }
    }

    window.apps.notify = window.notify = {
        error(msg, title) {
            var opt = {};
            opt.text = msg;
            opt.type = 'error';
            opt.addclass = "stack-topleft";
            if (title) opt.title = title;
            new PNotify(opt);
        },
        info(msg, title) {
            var opt = {};
            opt.text = msg;
            opt.type = 'info';
            opt.addclass = "stack-topleft";
            if (title) opt.title = title;
            new PNotify(opt);
        },
        success(msg, title) {
            var opt = {};
            opt.text = msg;
            opt.type = 'success';
            opt.addclass = "stack-topleft";
            if (title) opt.title = title;
            new PNotify(opt);
        },
        /**
         * @param name
         * @returns {Progress}
         */
        startProgress: function(name) {
            var progress = new Progress(name);
            var index = tasks.push(progress) - 1;
            taskNameHash[index] = name;
            return progress;
        },
        stopProgress: function(name) {
            this.getProgress(name).stop();
            //remove from tasks and hash
        },
        doProgress: function(name, amount, text) {
            var progress = this.getProgress(name);
            if (!progress) {
                console.warn('progress with name "'+name+'" not found');
                return;
            }
            progress.do(amount, text);
        },
        setProgress: function(name, value, text) {
            var progress = this.getProgress(name);
            if (!progress) {
                console.warn('progress with name "'+name+'" not found');
                return;
            }
            progress.set(value, text);
        },
        /**
         * @param name
         * @returns {Progress}
         */
        getProgress: function(name) {
            var i = taskNameHash.indexOf(name);
            if (i < 0) return null;
            return tasks[i];
        },
    };

    //form helper. controlled bei attributes
    $(document).ready(function() {
        //deselect event for radio boxes
        $('input[type="radio"]').bind('mousedown', function(){
            let grp = $('input[name="' + $(this).attr('name') + '"]').not($(this));
            grp.each(function() {
                if ($(this).prop('checked')) {
                    $(this).trigger('change', [{checked: false}]);
                }
            });
        });
        function getAllGroupMembers(groupName) {
            var members = [];
            $('[data-togglegrp]').each(function() {
                var groups = $(this).data('togglegrp').split(',');
                if (groups.indexOf(groupName) > -1) {
                    members.push($(this));
                }
            });
            return members;
        }
        $('[data-toggle]').on('change', function(e, overwrite) {
            var grpName = $(this).data('toggle');
            var grp = getAllGroupMembers(grpName);
            var flag = (overwrite && typeof overwrite.checked === 'boolean')? overwrite.checked : $(this).prop('checked');

            $.each(grp, function(index, $value) {
                let $target = $value.parent(),
                    type = $value.attr('type');

                if (type === 'radio') {
                    $target = $target.parent();
                } else if (type === 'checkbox') {
                    $target = $target.parent();
                }
                (flag)? $target.show() : $target.hide();
            });
            if (!flag) return;
            $.each(grp, function(index, $value) {
                if ($value.data('toggle')) $value.trigger('change');
            });
        });
        var roots = [];
        $('[data-toggle]').each(function() {
            let toggleGrp = $(this).data('togglegrp');
            if (toggleGrp) {
                $(this).trigger('change');
            } else {
                roots.push($(this));
            }
        });
        $.each(roots, function(index, $value) {
            $value.trigger('change');
        });
    });
})();