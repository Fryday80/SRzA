// Uglifyer:   https://github.com/mishoo/UglifyJS2/tree/harmony
// path patterns: https://github.com/isaacs/node-glob
var UglifyJS    = require("uglify-es"),
    fs          = require('fs'),
    colors      = require('colors'),
    glob        = require('glob'),
    filesize    = require('filesize'),
    cPre = 'JS -> '.yellow, //console prefix
    files = [],
    target,
    overallSize = 0,
    overallCompressedSize = 0,
    overallFileCount = 0,
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
function reset() {
    "use strict";
    files = {};
    target = null;
}
function getFilesizeInBytes(filename) {
    const stats = fs.statSync(filename)
    const fileSizeInBytes = stats.size
    return fileSizeInBytes
}
exports.testPath = function(path, options = {}) {
    var files = glob.sync(path, options);
    console.log(cPre + 'Test glob pattern: %s', path);
    if (files.length === 0) {
        console.log(cPre + 'No files found!'.red);
    }

    for(var i = 0; i < files.length; i++) {
        var size = getFilesizeInBytes(files[i]);
        size = filesize(size, {base: 10});
        console.log(cPre + 'found: %s | %s', size, files[i]);
    }
};
/**
 * if destPath is true, src will be overwritten.
 * if destPath is null, compiled version gets the min extension
 * @param {string} srcPath with glob patterns
 * @param {string|boolean|null} destPath without glob patterns
 */
exports.uglify = function(srcPath, destPath){
    "use strict";
    var options = {},
        overwrite = destPath;
    if (typeof destPath === 'string' && glob.hasMagic(destPath) ) {
        console.log(cPre + 'Destination path must be a blank path without patterns: %s'.red, destPath);
        return;
    }

    var files = glob.sync(srcPath, options),
        size = 0,
        sizeCompressed = 0,
        collection = '';

    for(var i = 0; i < files.length; i++) {
        var srcPath = files[i];
        var srcSize = getFilesizeInBytes(srcPath);
        var code = fs.readFileSync(srcPath);
        var result = UglifyJS.minify(code.toString());
        var desSize = 0;
        if (result.error) {
            console.log(cPre + result.error);
            continue;
        } else {
            if (typeof destPath === 'string') {
                //concat to one file
                desSize = Buffer.byteLength(result.code, 'utf8');
                collection += result.code;
            } else {
                if (destPath === true) {
                    //overwrite
                    destPath = srcPath;
                } else {
                    //add min extension
                    destPath = srcPath.substring(0, srcPath.lastIndexOf(".")) + ".min" + srcPath.substring(srcPath.lastIndexOf("."));
                }
                fs.writeFileSync(destPath, result.code);
                desSize = getFilesizeInBytes(destPath);
            }
        }
        overallFileCount++;
        overallSize += srcSize;
        overallCompressedSize += desSize;
        size += srcSize;
        sizeCompressed += desSize;
        var beforSize = filesize(srcSize, {base: 10});
        var afterSize = filesize(desSize, {base: 10});
        console.log(cPre + 'File: [%s -> %s] %s', beforSize, afterSize, srcPath);
    }
    if (typeof overwrite === 'string') {
        //save all to one file
        fs.writeFileSync(destPath, collection);
        var destSize = getFilesizeInBytes(destPath);
        console.log(cPre + 'Write [%s] to: %s'.green, filesize(destSize, {base: 10}), destPath);
    }
};
exports.add = function(filePath) {
    "use strict";
    //@todo check if file exists
    var content = fs.readFileSync(filePath);

    if (!content) return;
    var size = Buffer.byteLength(content, 'utf8');
    content = UglifyJS.minify(content.toString(), options);
    if (content.error) {
        console.log(cPre + result.error);
        return;
    }
    var minSize = Buffer.byteLength(content.code, 'utf8');
    files.push(content.code);
    overallFileCount++;
    overallSize += size;
    overallCompressedSize += minSize;
    var beforSize = filesize(size, {base: 10});
    var afterSize = filesize(minSize, {base: 10});
    console.log(cPre + 'File: [%s -> %s] %s', beforSize, afterSize, filePath);
};
exports.make = function(targetPath, options) {
    "use strict";
    if (!targetPath) {
        console.error(cPre + 'You need to specify a target path!');
        return;
    }
    //@todo merge options
    target = targetPath;
    var content = files.join();
    fs.writeFileSync(target, content);
    var destSize = filesize(getFilesizeInBytes(target), {base: 10});
    console.log(cPre + 'Write [%s] to: %s'.green, destSize, target);
    reset();
};
exports.stats = function() {
    "use strict";
    console.log(cPre + '**********************************************');
    console.log(cPre + 'Overall Compression of JavaScript code:');
    console.log(cPre + 'Files:              %s', overallFileCount);
    console.log(cPre + 'Size uncompressed:  %s', filesize(overallSize, {base: 10}));
    console.log(cPre + 'Size compressed:    %s', filesize(overallCompressedSize, {base: 10}));
    console.log(cPre + 'This is a reduction of %s\%', Math.round(overallCompressedSize / overallSize * 10000) / 100 );
    console.log(cPre + '**********************************************');
};