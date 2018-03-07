var gulp = require('gulp');
var rimraf = require('rimraf');
var config = require('../config');

gulp.task('watch', [
    'sprite:watch',
    'jade:watch',
    'sass:watch',
    'copy:watch',
    'font:watch',
    'js:watch'
]);


gulp.task('delete', function (cb) {
    rimraf('./'+config.dest.root, cb);
});
gulp.task('default', ['server', 'watch'], function() {});
gulp.task('build', ['font','sprite','copy','js','sass'], function() {});