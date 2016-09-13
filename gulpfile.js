var gulp        = require('gulp'),
    $           = require('gulp-load-plugins')(),
    eventStream = require('event-stream');

// Sass
gulp.task('sass', function () {

  return gulp.src(['./src/scss/**/*.scss'])
    .pipe($.plumber({
      errorHandler: $.notify.onError('<%= error.message %>')
    }))
    .pipe($.sourcemaps.init())
    .pipe($.sass({
      errLogToConsole: true,
      outputStyle    : 'compressed',
      sourceComments : 'normal',
      sourcemap      : true
    }))
    .pipe($.sourcemaps.write('./map'))
    .pipe(gulp.dest('./assets/css'));
});


// JS Hint
gulp.task('jshint', function () {
  return gulp.src(['./src/js/**/*.js'])
    .pipe($.plumber({
      errorHandler: $.notify.onError('<%= error.message %>')
    }))
    .pipe($.jshint('./src/js/.jshintrc'))
    .pipe($.jshint.reporter('jshint-stylish'));
});

gulp.task('jsBundle', function(){
  return gulp.src('./src/js/*.js')
    .pipe($.plumber({
      errorHandler: $.notify.onError('<%= error.message %>')
    }))
    .pipe($.sourcemaps.init())
    .pipe($.include({
      extensions: "js"
    }))
    .pipe($.uglify())
    .pipe($.sourcemaps.write('./map'))
    .pipe(gulp.dest('./assets/js/'));
});

// Copy
gulp.task('copy', function () {
  return eventStream.merge(
    gulp.src([
      './node_modules/angular/angular.min.js',
      './node_modules/angular/angular.min.js.map'
    ]).pipe(gulp.dest('./assets/js')),
    gulp.src('./node_modules/angular-ui-sortable/dist/sortable.min.js').pipe(gulp.dest('./assets/js'))
  );
});

// Build
gulp.task('build', ['sass', 'copy', 'jsBundle']);

// watch
gulp.task('watch', function () {
  // Make SASS
  gulp.watch('./src/scss/**/*.scss', ['sass']);
  // Check JS syntax and bundle them
  gulp.watch('./src/js/**/*.js', ['jshint', 'jsBundle']);
});
