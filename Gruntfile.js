module.exports = function(grunt) {
    require('jit-grunt')(grunt);

    grunt.initConfig({
        less: {
            development: {
                options: {
                    compress: false,
                    yuicompress: false,
                    optimization: 2,
                    strictMath: true
                },
                files: [
                    {
                        "public/css/style.css": "less/style.less", // destination file and source file
                        "public/css/jsOverrides.css": "less/jsOverrides.less", // destination file and source file
                        "public/libs/jquery.workspace/jquery.workspace.css": "public/libs/jquery.workspace/jquery.workspace.less",
                    },
                    {
                        expand: true,
                        src: ['module/**/*.less'],
                        ext: '.css',
                        extDot: 'first'
                    }
                ]
            },
            production: {
                options: {
                    compress: true,
                    yuicompress: true,
                    optimization: 2,
                    strictMath: true
                },
                files: [
                    {
                        "public/css/style.css": "less/style.less", // destination file and source file
                        "public/css/jsOverrides.css": "less/jsOverrides.less", // destination file and source file
                    },
                    {
                        expand: true,
                        src: ['module/**/*.less'],
                        ext: '.css',
                        extDot: 'first'
                    }
                ]
            }
        },
        watch: {
            styles: {
                files: [
                    'less/**/*.less',
                    'module/**/*.less',
                    "public/libs/jquery.workspace/jquery.workspace.less",
                ],
                tasks: ['less'],
                options: {
                    nospawn: true
                }
            }
        }
    });

    grunt.registerTask('default', ['less', 'watch']);
};