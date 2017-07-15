//global js for pnotify shorthandlers and ajax functions with build in error handling
(function(){
    window.http = {
        get: function() {},
        post: function() {},
        postBlob: function() {},
    };
    /** @type {Progress[]} */
    var tasks = [],
        taskNameHash = [];

    class Progress {
        constructor(name, title = name) {
            this.name = name;
            this.value = 0;
            this.postTimeout = 200;
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


    window.notify = {
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
        doProgress: function(name, amount) {
            var progress = this.getProgress(name);
            if (!progress) {
                console.warn('progress with name "'+name+'" not found');
                return;
            }
            progress.do(amount);
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

    $(document).ready(function() {
        var progress = window.notify.startProgress('test');
            // notify.doProgress('test', 0.2);
        var inter = setInterval(function() {
            notify.doProgress('test', 0.2);
            if (progress.value >= 1) {
                clearInterval(inter);
            }
        }, 100);
    });
})();