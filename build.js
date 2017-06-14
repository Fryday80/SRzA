var shell = require('shelljs');
var js = require('./build/uglifyJS.js');



// shell.exec('cd build && ls');
// shell.exec('ls');

//for pattern path -> https://github.com/isaacs/node-glob
//and a test function to view what files found by the pattern. secound argument is a options object(see node-glob)
js.testPath('/module/*/view/**/*.js');


// js.uglify('public/js/globalUsage/loggingDesigner/loggingDesigner.js');
// js.uglify('public/js/globalUsage/loggingDesigner/loggingDesigner.js', 'public/js/globalUsage/loggingDesigner/loggingDesigner.min.js');
//
// js.add('public/js/globalUsage/menu/menu.js');
// js.add('public/js/globalUsage/loggingDesigner/loggingDesigner.js');
// js.make('public/js/main.js');