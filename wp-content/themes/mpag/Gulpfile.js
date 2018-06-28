var project = "mpag", // Project name, used for build zip.
  url = "mpag.local", // Local Development URL for BrowserSync. Change as-needed.
  bower = "./bower_components/";

var gulp = require("gulp"),
  browserSync = require("browser-sync"),
  reload = browserSync.reload,
  sass = require("gulp-sass"),
  sourcemaps = require("gulp-sourcemaps"),
  autoprefixer = require("gulp-autoprefixer"),
  minifycss = require("gulp-uglifycss"),
  uglify = require("gulp-uglify"),
  concat = require("gulp-concat"),
  rename = require("gulp-rename"),
  filter = require("gulp-filter");

var plumber = require("gulp-plumber");
var gutil = require("gulp-util");
var gulp_src = gulp.src;
gulp.src = function() {
  return gulp_src.apply(gulp, arguments).pipe(
    plumber(function(error) {
      // Output an error message
      gutil.log(
        gutil.colors.red("Error (" + error.plugin + "): " + error.message)
      );
      // emit the end event, to properly end the task
      this.emit("end");
    })
  );
};

// =============================================================================
// =============================================================================
// Configure BrowserSync
gulp.task("browser-sync", function() {
  var files = ["**/*.php", "**/*.{png,jpg,gif}"];
  browserSync.init(files, {
    // Read here http://www.browsersync.io/docs/options/
    proxy: url,

    // port: 8080,

    // Tunnel the Browsersync server through a random Public URL
    // tunnel: true,

    // Attempt to use the URL "http://my-private-site.localtunnel.me"
    // tunnel: "ppress",

    // Inject CSS changes
    injectChanges: true
  });
});

// =============================================================================
// =============================================================================
// Configure SASS

gulp.task("styles", function() {
  return gulp
    .src("./library/scss/*.scss")
    .pipe(sourcemaps.init())
    .pipe(
      sass({
        errLogToConsole: true,

        //outputStyle: 'compressed',
        outputStyle: "compact",
        // outputStyle: 'nested',
        // outputStyle: 'expanded',
        precision: 10
      })
    )
    .pipe(sourcemaps.write({ includeContent: false }))
    .pipe(sourcemaps.init({ loadMaps: true }))
    .pipe(
      autoprefixer(
        "last 2 version",
        "> 1%",
        "safari 5",
        "ie 8",
        "ie 9",
        "opera 12.1",
        "ios 6",
        "android 4"
      )
    )
    .pipe(sourcemaps.write("."))
    .pipe(
      minifycss({
        maxLineLen: 80
      })
    )
    .pipe(gulp.dest("./library/css"))
    .pipe(reload({ stream: true })); // Inject Styles when style file is created
  // .pipe(rename({ suffix: '.min' }))

  // .pipe(gulp.dest('./library/css'))
  // .pipe(reload({stream:true})) // Inject Styles when min style file is created
});

//script paths
var jsFiles = "assets/scripts/**/*.js",
  jsDest = "dist/scripts";

gulp.task("scripts", function() {
  return gulp
    .src(jsFiles)
    .pipe(concat("scripts.js"))
    .pipe(gulp.dest(jsDest))
    .pipe(rename("scripts.min.js"))
    .pipe(uglify())
    .pipe(gulp.dest(jsDest));
});

// =============================================================================
// =============================================================================
// BOWER PLUGINS

gulp.task("js-lib", function() {
  var files = [
    "bower_components/gsap/src/uncompressed/jquery.gsap.js",
    "bower_components/gsap/src/uncompressed/TweenMax.js",
    "bower_components/gsap/src/uncompressed/TimelineMax.js",
    "bower_components/scrollmagic/scrollmagic/uncompressed/ScrollMagic.js"
  ];

  return gulp
    .src(files, { base: "library/js/plugins/" })
    .pipe(sourcemaps.init())
    .pipe(uglify({ mangle: false }))
    .pipe(concat("plugins.js"))
    .pipe(sourcemaps.write("./"))
    .pipe(gulp.dest("./library/js/plugins/"));
});

// =============================================================================
// =============================================================================
// WATCH
gulp.task("default", ["styles", "js-lib", "browser-sync"], function() {
  gulp.watch("./library/scss/**/*.scss", ["styles"]);
  gulp.watch("./library/js/*.js", ["js-lib"]);
});
