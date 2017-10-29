// Include gulp
var gulp = require('gulp');

// Include Our Plugins
var sass = require('gulp-sass'),
    prefix = require('gulp-autoprefixer'),
    notify = require('gulp-notify'),
    uglify = require('gulp-uglify'),
    include = require('gulp-include');

var sassIncludes = [
    'app/resources/sass/'
];

var jsIncludes = [
    'app/resources/js/',
];

gulp.task('sass', function() {
    return gulp.src(__dirname + '/app/resources/sass/*.scss')
        .pipe(sass({
            sourceStyle: 'compressed',
            includePaths: sassIncludes,
            errLogToConsole: false,
            onError: function(err) {
                console.log(err);
                return notify().write(err);
            }
        }))
        .on('error', notify.onError())
        .pipe(prefix(["last 2 versions", "> 1%", "ie 8", "ie 7"], { cascade: true }))
        // .pipe(cleanCss())
        .pipe(gulp.dest('./web/css'))
        // .pipe(notify("SASS compilation complete: <%=file.relative%>"))
        ;
});

gulp.task('js', function() {
    return gulp.src([__dirname + '/app/resources/js/*.js'])
        .pipe(include({
            includePaths: jsIncludes
        }))
        // .pipe(uglify())
        .on('error', notify.onError())
        .pipe(gulp.dest('./web/js'))
        // .pipe(notify("JS compilation complete: <%=file.relative%>"))
        .on('error', notify.onError())
        ;
});

// Watch Files For Changes
gulp.task('watch', function() {
    gulp.watch('./app/resources/sass/**/*.scss', ['sass']);
    gulp.watch('./app/resources/js/**/*.js', ['js']);
});

// Default Task
gulp.task('default', [
    'sass',
    'js',
    'watch'
]);
