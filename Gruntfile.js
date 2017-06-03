module.exports = function(grunt) {
    require('jit-grunt')(grunt);
    require('google-closure-compiler').grunt(grunt);

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
                        "public/css/fun.css": "less/fun.less", // destination file and source file
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
            // production: {
            //     options: {
            //         compress: true,
            //         yuicompress: true,
            //         optimization: 2,
            //         strictMath: true
            //     },
            //     files: [
            //         {
            //             "public/css/style.css": "less/style.less", // destination file and source file
            //             "public/css/jsOverrides.css": "less/jsOverrides.less", // destination file and source file
            //         },
            //         {
            //             expand: true,
            //             src: ['module/**/*.less'],
            //             ext: '.css',
            //             extDot: 'first'
            //         }
            //     ]
            // }
        },
        'closure-compiler': {
            //https://github.com/google/closure-compiler-npm
            my_target: {
                files: {
                    // 'public/js/output2.min.js': ['public/js/globalUsage/jquery/*.js'],
                    // 'public/js/output.min.js': ['public/js/selectedUsage/**/*.js']
                    'public/js/main.js': [//
                        'public/js/globalUsage/accordion/*.js',
                        'public/js/globalUsage/loggingDesigner/*.js',
                        'public/js/globalUsage/menu/*.js',
                        'public/js/globalUsage/popUp/*.js',
                    ]
                },
                options: {
                    debug: true,
                    compilation_level: 'ADVANCED',
                    // compilation_level: 'SIMPLE',
                    // warning_level: 'ALL',
                    // language_in: 'ECMASCRIPT6_STRICT',
                    // language_out: 'ECMASCRIPT5_STRICT',
                    // language_in: 'ECMASCRIPT6',
                    // create_source_map: 'public/js/output.min.js.map',
                    // output_wrapper: '(function(){\n%output%\n}).call(this)\n//# sourceMappingURL=output.min.js.map'
                }
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
    grunt.registerTask('build', ['closure-compiler']);
};