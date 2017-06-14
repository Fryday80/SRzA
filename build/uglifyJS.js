// Uglifyer:   https://github.com/mishoo/UglifyJS2/tree/harmony
// path patterns: https://github.com/isaacs/node-glob
var UglifyJS    = require("uglify-es"),
    fs          = require('fs'),
    colors      = require('colors'),
    glob        = require('glob'),
    cPre = 'JS -> '.yellow, //console prefix
    files = {},
    count = 0,
    readyCount = 0,
    doMake = false,
    target,
    options = {
        toplevel: true,
        compress: {
            global_defs: {
                "@console.log": "all"
            },
            passes: 2
        },
        output: {
            beautify: false,
            preamble: "/* uglified */"
        }
    };
function make() {
    "use strict";
    var result = UglifyJS.minify(files, options);
    if (result.error) {
        console.log(cPre + result.error);
    } else {
        console.log(cPre + 'write compiled js to: %s'.green, target);
        fs.writeFileSync(target, result.code);
    }
    reset();
    console.log(cPre + 'COMPLETE'.green);
}
function makeWhenComplet() {
    "use strict";
    //check if make command was called
    if (!doMake) return;
    //check if we are ready
    if (count != readyCount) return;
    //check if we have a target
    if (!target) return;
    make();
}
function reset() {
    "use strict";
    count = 0;
    readyCount = 0;
    files = {};
    target = null;
}
exports.testPath = function(path, options = {}) {
    var files = glob.sync(path, options);
    console.log(cPre + 'Test glob pattern: %s', path);
    if (files.length === 0) {
        console.log(cPre + 'No files found!'.red);
    }

    for(var i = 0; i < files.length; i++) {
        console.log(cPre + 'found: %s', files[i]);
    }
};
exports.uglify = function(srcPath, destPath){
    "use strict";
    var options = {};
    glob(srcPath, options, function (er, files) {
        for(var i = 0; i < files.length; i++) {
            var srcPath = files[i];
            var code = fs.readFileSync(srcPath);
            var result = UglifyJS.minify(code.toString(), options);
            if (result.error) {
                console.log(cPre + result.error);
            } else {
                if (files.length > 1 || !destPath) {
                    destPath = srcPath.substring(0, srcPath.lastIndexOf(".")) + ".min" + srcPath.substring(srcPath.lastIndexOf("."));
                }
                fs.writeFileSync(destPath, result.code);
                console.log(cPre + 'Add File: %s', srcPath);
            }
        }
    });
};
exports.add = function(file) {
    "use strict";
    //@todo check if file exists
    count++;
    fs.readFile(file, (err, code) => {
        if (err) {
            files[file] = null;
            console.log(cPre + 'Warning: ', err);
        } else {
            console.log(cPre + 'Add File: %s ', file);
            files[file] = code.toString();
        }
        readyCount++;
        makeWhenComplet();
    });
};
exports.make = function(targetPath, options) {
    "use strict";
    if (!targetPath) {
        console.error(cPre + 'You need to specify a target path!');
        return;
    }
    //@todo merge options
    target = targetPath;
    doMake = true;
    makeWhenComplet();
};