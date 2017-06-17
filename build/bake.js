var shell       = require('shelljs');
var glob        = require('glob');
var colors      = require('colors');
var toArray	    = require('lodash.toarray');
var filesize    = require('filesize');
var fs          = require('fs');
var util        = require('util');


function getFilesizeInBytes(filename) {
    const stats = fs.statSync(filename)
    const fileSizeInBytes = stats.size
    return fileSizeInBytes
}
global.prompt = require('prompt');

var section = 'Start',
    logPrefix = section + ' -> ',
    next = null;


var oldLog = console.log;
// console.log = function(...args) {
//     oldLog(logPrefix, ...args);
// };
global.ask = function() {
}
global.section = function(name) {
    section = name;
    logPrefix = section + ' -> ';
};
global.log = function(...args) {
    oldLog(logPrefix.white.inverse, ...args);
};
global.warn = function(...args) {
    oldLog(logPrefix.yellow, ...args);
};
global.error = function(...args) {
    oldLog(logPrefix.red, ...args);
};

// function myConsole() {
//     var nativeConsole = console;
//
//     // ... some code
//
//     return {
//         log : function() {
//             nativeConsole.log('something');
//         }
//     }
//
// }
//
// global.console = myConsole();
/**
 *  ShellJS wrapper
 */
global.shell = shell;
/**
 * exec(command [, options] [, callback])
 * @param args
 */
global.exec = function(...args) {
    shell.exec.apply(null, args);
};


global.testPath = function(path, options = {}) {
    var files = glob.sync(path, options);
    console.log(logPrefix.yellow + 'Test glob pattern: %s', path);
    if (files.length === 0) {
        console.log(logPrefix.yellow + 'No files found!'.red);
    }

    for(var i = 0; i < files.length; i++) {
        var size = getFilesizeInBytes(files[i]);
        size = filesize(size, {base: 10});
        console.log(logPrefix.yellow + 'found: %s | %s', size, files[i]);
    }
};



require('./test.js');