var gulp = require('gulp'),
	less = require('gulp-less'),
	minify = require('gulp-babel-minify'),
	cleanCSS = require('gulp-clean-css'),
	concat = require('gulp-concat');

gulp.task('controlPanel', function () {
  gulp.src('./src/admin/**/*.less')
	  .pipe(less())
	  .pipe(cleanCSS())
	  .pipe(concat('controlpanel_modules.css'))
	  .pipe(gulp.dest('./src/resources/css/'));

  gulp.src('./src/styles/admin/css/**/*.less')
	  .pipe(less())
	  .pipe(cleanCSS())
	  .pipe(concat('controlpanel.css'))
	  .pipe(gulp.dest('./src/resources/css/'));

  gulp.src('./src/admin/**/*.js')
	  .pipe(minify({
	    mangle: {
	      keepClassName: true
	    }
	  }))
	  .pipe(concat('controlpanel.min.js'))
	  .pipe(gulp.dest('./src/resources/js/'));
});

gulp.task('youconix', function () {
  gulp.src([
    './src/js/youconix/jquery-3.2.1.js',
    './src/js/youconix/*.js',
    './src/js/youconix/authorization/*.js'
  ])
	  .pipe(minify({
	    mangle: {
	      keepClassName: true
	    }
	  }))
	  .pipe(concat('youconix.min.js'))
	  .pipe(gulp.dest('./src/resources/js/'));

  gulp.src('./src/js/youconix/graph/*.js')
	  .pipe(minify({
	    mangle: {
	      keepClassName: true
	    }
	  }))
	  .pipe(concat('graph.min.js'))
	  .pipe(gulp.dest('./src/resources/js/'));
  
  gulp.src([
    './src/styles/shared/css/HTML5_validation.less',
    './src/styles/shared/css/animation.less',
    './src/styles/shared/css/autosuggest.less',
    './src/styles/shared/css/registration.less',
    './src/styles/shared/css/tabs.less'
  ])
	  .pipe(less())
	  .pipe(cleanCSS())
	  .pipe(concat('youconix.css'))
	  .pipe(gulp.dest('./src/resources/css/'));
});

gulp.task('youconix widgets', function () {
  gulp.src('./src/js/youconix/widgets/*.js')
	  .pipe(minify({
	    mangle: {
	      keepClassName: true
	    }
	  }))
	  .pipe(concat('widgets.min.js'))
	  .pipe(gulp.dest('./src/resources/js/'));
  
  gulp.src('./src/styles/shared/css/widgets/*.less')
	  .pipe(less())
	  .pipe(cleanCSS())
	  .pipe(concat('widgets.css'))
	  .pipe(gulp.dest('./src/resources/css/'));
});

gulp.task('youconix installer', function () {
  gulp.src([
    './src/styles/shared/css/install.less',
    './src/styles/shared/css/installIndex.less'
  ])
	  .pipe(less())
	  .pipe(cleanCSS())
	  .pipe(concat('install.css'))
	  .pipe(gulp.dest('./src/resources/css/'));
});

gulp.task('default', function () {
  gulp.start('controlPanel');
  gulp.start('youconix');
  gulp.start('youconix widgets');
  gulp.start('youconix installer');
});
