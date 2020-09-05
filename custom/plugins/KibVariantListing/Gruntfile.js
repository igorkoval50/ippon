module.exports = function (grunt) {
    grunt.initConfig({
        jsObfuscate: {
            test: {
                options: {
                    concurrency: 2,
                    keepLinefeeds: false,
                    keepIndentations: false,
                    encodeStrings: true,
                    encodeNumbers: true,
                    moveStrings: true,
                    replaceNames: true
                },
                files: {
                    'Resources/views/kib/frontend/_public/src/js/jquery.variants_in_listing.js': [
                        'Resources/views/kib/frontend/_public/src/js/jquery.variants_in_listing.js'
                    ]
                }
            }
        }
    });

    grunt.loadNpmTasks('js-obfuscator');

    grunt.registerTask('default', ['jsObfuscate']);
};
