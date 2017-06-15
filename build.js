var shell = require('shelljs');
var js = require('./build/uglifyJS.js');

var TempPath = 'build/temp';
var Out = 'build/release';

// create folder structure
// shell.mkdir('-p', '/tmp/a/b/c/d', '/tmp/e/f/g');
shell.mkdir(TempPath);
shell.mkdir(Out);

//build css
shell.exec('grunt build');


// shell.exec('copy -r build/release');

//for pattern path -> https://github.com/isaacs/node-glob
//and a test function to view what files found by the pattern. secound argument is a options object(see node-glob)
js.testPath(Out + '/module/**/*.js');


js.uglify(Out + '/module/**/*.js', true);
// js.uglify('public/js/globalUsage/loggingDesigner/loggingDesigner.js', 'public/js/globalUsage/loggingDesigner/loggingDesigner.min.js');
//
// js.add('public/js/globalUsage/menu/menu.js');
// js.add('public/js/globalUsage/loggingDesigner/loggingDesigner.js');

// global js
js.add(  'public/js/globalUsage/popUp/popUp.js');
js.add(  'public/js/globalUsage/menu/menu.js');
js.add(  'public/js/globalUsage/loggingDesigner/loggingDesigner.js');
js.add(  'public/js/globalUsage/accordion/accordion.js');
js.add('public/libs/globalUsage/feedback/js/feedback.js');
js.make(TempPath+'/main.js');

shell.cat([
    'public/js/globalUsage/jquery/jquery-3.2.0.min.js',
    'public/libs/globalUsage/jquery-ui/jquery-ui.min.js',
    TempPath+'/main.js'
]).to(Out + '/myAwesomeScript.js');



// clean up
shell.rm('-rf', TempPath);