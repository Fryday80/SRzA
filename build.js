var shell = require('shelljs');
var js = require('./build/uglifyJS.js');
var colors = require('colors');
const replace = require('replace-in-file');

var cPre = 'Main -> '.yellow;

var TempPath = 'build/temp';
var Out = 'build/release';
var bRoot = Out + '/';
var msg;

// ---------- prepare system
// MODE: overwrite mode   .. out commentate for merge moode
// shell.rm('-r', Out);

// output folder
shell.mkdir(Out);
// temp folder
shell.mkdir(TempPath);

//build css
shell.exec('grunt build');

// ---------- create folder structure
// shell.mkdir('-p', '/tmp/a/b/c/d', '/tmp/e/f/g');
// site's folder
// empty folders
shell.mkdir(bRoot + 'cache');
shell.mkdir(bRoot + 'Data');
shell.mkdir(bRoot + 'logs');
shell.mkdir(bRoot + 'storage');
shell.mkdir(bRoot + 'temp');
shell.mkdir(bRoot + 'Upload');
// data folders
shell.mkdir('-p', bRoot + 'config/autoload');
shell.mkdir('-p', bRoot + 'public/img');

// only if Zend was updated
// shell.cp('-r', 'vendor/', bRoot);

// ---------- copy data
// cp('file1', 'dir1');
// cp('-R', 'path/to/dir/', '~/newCopy/');
shell.cp('config/application.config.php', bRoot + 'config/');
shell.cp('config/autoload/global.php', bRoot + 'config/autoload/');
shell.cp('-r', 'module/', bRoot);
shell.cp('-r', 'public/.htaccess',  bRoot + 'public/');
shell.cp('-r', 'public/index.php', bRoot + 'public/');
shell.cp('-r', ['public/css/', 'public/fonts/', 'public/img/', 'public/js/', 'public/libs/'], bRoot + 'public/');
shell.rm('-r', bRoot + 'public/img/psd');
shell.cp('-r', 'init_autoloader.php', bRoot);

// change contents
try {
    let changedFiles = replace.sync({
        files: bRoot + 'public/.htaccess',
        //Replacement to make (string or regex) 
        from: /development/g,
        to: 'production',
    });
    console.log(cPre + 'Modified files: %s', changedFiles.join(', '));
}
catch (error) {
    console.error(cPre + 'Error occurred: %s'.red, error.message);
}

//for pattern path -> https://github.com/isaacs/node-glob
//and a test function to view what files found by the pattern. secound argument is a options object(see node-glob)
// js.testPath(Out + '/module/**/*.js');

js.uglify(bRoot + 'module/**/*.js', true);
// js.uglify('public/js/globalUsage/loggingDesigner/loggingDesigner.js', 'public/js/globalUsage/loggingDesigner/loggingDesigner.min.js');

// global js
// minify and merge
js.add(  'public/js/globalUsage/popUp/popUp.js');
js.add(  'public/js/globalUsage/menu/menu.js');
js.add(  'public/js/globalUsage/loggingDesigner/loggingDesigner.js');
js.add(  'public/js/globalUsage/accordion/accordion.js');
js.add('public/libs/globalUsage/feedback/js/feedback.js');
js.make(TempPath+'/main.js');

// merge minified with *.min.js
shell.cat([
    'public/js/globalUsage/jquery/jquery-3.2.0.min.js',
    'public/libs/globalUsage/jquery-ui/jquery-ui.min.js',
    TempPath+'/main.js'
]).to(bRoot + 'public/loadJS.js');

// clean up
shell.rm('-rf', TempPath);