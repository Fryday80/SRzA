const replace = require('replace-in-file');
var js = require('./build/uglifyJS.js');
var colors = require('colors');
var shell = require('shelljs');
// Shell operations CheatSheet:
// shell.mkdir('-p', '/tmp/a/b/c/d', '/tmp/e/f/g');
// cp('file1', 'dir1');
// cp('-R', 'path/to/dir/', '~/newCopy/');

var cPre = 'Main -> '.yellow;
var Message = false;
var progress = '';

var TempPath = 'build/temp/';
var Out = 'build/release/';

//==== SETTINGS ====
var clearExisting = false;
var ZendSkeletonUpdated = false;

// ---------- prepare system
if (clearExisting) {
    shell.rm('-r', Out);
}
// output folder
shell.mkdir(Out);
// temp folder
shell.mkdir(TempPath);

//build css
shell.exec('grunt build');
console.log('\x1Bc');

// ---------- create folder structure
// site's folder
// empty folders
shell.mkdir(Out + 'cache');
shell.mkdir(Out + 'Data');
shell.mkdir(Out + 'logs');
shell.mkdir(Out + 'storage');
shell.mkdir(Out + 'temp');
shell.mkdir(Out + 'Upload');
shell.mkdir(Out + 'zendCache');
// data folders
shell.mkdir('-p', Out + 'config/autoload');
shell.mkdir('-p', Out + 'public/img');

if (ZendSkeletonUpdated){
    // only if Zend was updated
    shell.cp('-r', 'vendor/', Out);
}

// ---------- copy data
shell.cp('config/application.config.php', Out + 'config/');
shell.cp('config/autoload/global.php', Out + 'config/autoload/');
shell.cp('-r', 'module/', Out);
shell.cp('-r', 'public/.htaccess',  Out + 'public/');
shell.cp('-r', 'public/index.php', Out + 'public/');
shell.cp('-r', ['public/css/', 'public/fonts/', 'public/img/', 'public/js/', 'public/libs/'], Out + 'public/');
shell.rm('-r', Out + 'public/img/psd');
shell.cp('-r', 'init_autoloader.php', Out);
shell.cp('-r', 'vendor/', Out);

// modify contents
try {
    let changedFiles = replace.sync({
        files: Out + 'public/.htaccess',
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

js.uglify(Out + 'module/**/*.js', true);
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
]).to(Out + 'public/loadJS.js');

// clean up
shell.rm('-rf', TempPath);
js.stats();