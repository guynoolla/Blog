'use strict';

const
	// source and build folders
	dir = {
		src 			: './gulp',
		build 			: './public_html',
	},
	
	// Gulp and plugins
	gulp 					  = require('gulp'),
	gutil 					= require('gulp-util'),
	newer 					= require('gulp-newer'),
	imagemin 				= require('gulp-imagemin'),
	sass 					  = require('gulp-sass'),
	postcss					= require('gulp-postcss'),
	stripdebug 			= require('gulp-strip-debug'),
	terser 					= require('gulp-terser'),
	babelify 				= require('babelify'),
	buffer 					= require('vinyl-buffer'),
	//sourcemaps 			= require('gulp-sourcemaps'),
	source 					= require('vinyl-source-stream'),
	rename 					= require('gulp-rename'),
	browserify 			= require('browserify'),
	mergeStream			= require('merge-stream');
	//	clean 					= require('gulp-clean'),

	
// Browser-sync
var browsersync = false;

// image settings 
const img = {
	src 		:		dir.src + '/img/*',
	build 	:		dir.build + '/assets/css/img'	
}

gulp.task('img', () => {
	return gulp.src(img.src)
		.pipe(newer(img.build))
		.pipe(imagemin())
		.pipe(gulp.dest(img.build));
});

// CSS settings
var css = {
	src 		:		[dir.src + '/scss/style.scss', dir.src + '/scss/admin.scss'],
	watch 	:		dir.src + '/scss/**/**/*',
	build 	:		dir.build + '/assets/css',
	sassOpts: {
		outputStyle		: 'nested',
		imagePath 		: img.build,
		precision 		: 3,
		errLogToConsole : true
	},
	processors: [
		require('postcss-assets')({
			loadPaths: ['gulp/img/'],
			basePath: dir.build,
			baseUrl: '/assets/styles/img/'
		}),
		require('autoprefixer'),
		require('css-mqpacker'),
		//require('cssnano')
	]
};

// CSS processing
gulp.task('css', gulp.series('img', () => {
	return gulp.src(css.src)
		.pipe(sass(css.sassOpts))
		.pipe(postcss(css.processors))
		.pipe(gulp.dest(css.build));
}) );

// Javascript settings
const js = {
	src 				: dir.src + '/js/**/*',
	entries 		: [dir.src + '/js/main.js'], // dir.src + '/js/admin.js'],
	build 			: dir.build + '/assets/js/',
	filenames		: ['main.js'], // 'admin.js'],
};

// Javascript processing
gulp.task( 'js', () => {
	var tasks = js.entries.map((entry, idx) => {
		var bundler = browserify({
			entries: [entry],
			debug: true
		});
		bundler.transform( babelify, { presets: ['@babel/preset-env'] } )
		return bundler.bundle()
			.on('error', error => console.log(error))
			.pipe(source(js.filenames[idx]))
			.pipe(buffer())
			//.pipe(sourcemaps.init({ loadMaps: true }))
			//.pipe(stripdebug())
			.pipe(terser())
			.pipe(gulp.dest(js.build))
			.pipe(browsersync.stream({ match: [dir.src, entry] }))
		})

	//return mergeStream(tasks[0], tasks[1])
	return tasks[0];
});

// Browsersync options
const syncOpts = {
	proxy 				: 'activello.loc',
//server          : "./public_html",
	files 				: dir.build + '/*', // Was a solution of twice reload!
	open 				  : false,
	notify				: false,
	ghostMode			: false,
	ui: {
		port: 3000
	}
};

// browser-sync
gulp.task('browsersync', (done) => {
	if (browsersync === false) {
		browsersync = require('browser-sync').create();
		browsersync.init(syncOpts)
		done();
	}
});

//watch for file changes
gulp.task('change', () => {
	gulp.watch(css.watch).on("change", gulp.series('css', browsersync.reload));
	gulp.watch(js.src).on("change", gulp.series('js', browsersync.reload));
});

gulp.task("watch", gulp.series("browsersync", "change"))

// run all tasks
gulp.task('build', gulp.parallel('css','js'));
