var gulp        = require('gulp');
var browserSync = require('browser-sync');
var config = require('../config');
//webserver
gulp.task('server', function() {
    browserSync({
        proxy: 'roms',
        files: [config.dest.html + '*.html', config.dest.css + '*.css', config.dest.js + '*.js'],
        port: 8080,
        notify: false,
        ghostMode: false,
        online: false,
        open: true
    });
});