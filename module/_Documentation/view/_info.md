#JavaScript
###Global [/public/js/global.js]
####notify

- error/warn/info
```
    window.notify.error(msg, title);
    window.notify.info(msg, title);
    window.notify.success(msg, title);
    window.notify.startProgress(name);
    window.notify.doProgress(name, amount, text);
    window.notify.setProgress(name, value, text);
    window.notify.getProgress(name);
    window.notify.stopProgress(name);
```
- progress bar
```
    window.notify.startProgress(name);
    window.notify.doProgress(name, amount, text);
    window.notify.setProgress(name, value, text);
    window.notify.getProgress(name);
    window.notify.stopProgress(name);
```

#####http
```
window.http.postJson(url, data, false, false, false)
```