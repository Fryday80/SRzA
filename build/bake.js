var shell   = require('shelljs');
var colors  = require('colors');
var fs      = require('fs');
var toArray	= require('lodash.toarray');
var util    = require('util');
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
require('./test.js');