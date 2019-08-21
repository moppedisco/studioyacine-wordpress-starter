// =============================================================================
// =============================================================================
// INIT

// package vars
const pkg = require("./package.json");

// gulp
const gulp = require("gulp");

// load all plugins in "devDependencies" into the variable $
const $ = require("gulp-load-plugins")({
  pattern: ["*"],
  scope: ["devDependencies"]
});

const onError = err => {
  console.log(err);
};

const banner = [
  "/**",
  " * @project        <%= pkg.name %>",
  " * @author         <%= pkg.author %>",
  " * @build          " + $.moment().format("llll") + " ET",
  " * @release        " +
    $.gitRevSync.long() +
    " [" +
    $.gitRevSync.branch() +
    "]",
  " * @copyright      Copyright (c) " +
    $.moment().format("YYYY") +
    ", <%= pkg.copyright %>",
  " *",
  " */",
  ""
].join("\n");

// =============================================================================
// =============================================================================
// scss - build the scss to the build folder, including the required paths, and writing out a sourcemap
gulp.task("scss", () => {
  $.fancyLog("===============> Compiling scss <===============");
  return gulp
    .src(pkg.paths.src.scss + pkg.vars.scssName)
    .pipe($.plumber({ errorHandler: onError }))
    .pipe($.sourcemaps.init({ loadMaps: true }))
    .pipe(
      $.sass({
        includePaths: pkg.paths.scss
      }).on("error", $.sass.logError)
    )
    .pipe($.cached("sass_compile"))
    .pipe($.autoprefixer())
    .pipe($.sourcemaps.write("./"))
    .pipe($.size({ gzip: true, showFiles: true }))
    .pipe(gulp.dest(pkg.paths.build.css));
});

// =============================================================================
// =============================================================================
// css task - combine & minimize any distribution CSS into the public css folder, and add our banner to it

gulp.task("css", ["scss"], () => {
  $.fancyLog("===============> Building CSS <===============");
  return gulp
    .src(pkg.globs.distCss)
    .pipe($.plumber({ errorHandler: onError }))
    .pipe($.newer({ dest: pkg.paths.dist.css + pkg.vars.siteCssName }))
    .pipe($.print())
    .pipe($.sourcemaps.init({ loadMaps: true }))
    .pipe($.concat(pkg.vars.siteCssName))
    .pipe(
      $.cssnano({
        discardComments: {
          removeAll: true
        },
        discardDuplicates: true,
        discardEmpty: true,
        minifyFontValues: true,
        minifySelectors: true
      })
    )
    .pipe($.header(banner, { pkg: pkg }))
    .pipe($.sourcemaps.write("./"))
    .pipe($.size({ gzip: true, showFiles: true }))
    .pipe(gulp.dest(pkg.paths.dist.css))
    .pipe($.filter("**/*.css"))
    .pipe($.browserSync.reload({ stream: true }));
});

// JS plugins task - combine the Javascript files into one bundle
gulp.task("js-bundle", () => {
    $.fancyLog("===============> Bundling JS plugins <===============");
    return gulp.src(pkg.globs.distJs)
        .pipe($.plumber({errorHandler: onError}))
        .pipe($.newer({dest: pkg.paths.build.js + "plugins.min.js"}))
        .pipe($.concat("plugins.min.js"))
        .pipe($.uglify())
        .pipe($.size({gzip: true, showFiles: true}))
        .pipe(gulp.dest(pkg.paths.build.js));
});


// =============================================================================
// =============================================================================
// babel js task - transpile our Javascript into the build directory
gulp.task("js-babel", () => {
  $.fancyLog(
    "===============> Transpiling JS via Babel... <==============="
  );
  return gulp
    .src(pkg.globs.babelJs)
    .pipe($.plumber({ errorHandler: onError }))
    .pipe($.newer({ dest: pkg.paths.build.js }))
    .pipe($.babel())
    .pipe($.size({ gzip: true, showFiles: true }))
    .pipe(gulp.dest(pkg.paths.build.js));
});

// =============================================================================
// =============================================================================
// js task - minimize any distribution Javascript into the public js folder, and add our banner to it
gulp.task("js", ["js-babel","js-bundle"], () => {
  $.fancyLog("===============> Building JS <===============");
  return gulp
    .src(pkg.globs.buildJs)
    .pipe($.plumber({ errorHandler: onError }))
    .pipe(
      $.if(
        ["*.js", "!*.min.js"],
        $.newer({ dest: pkg.paths.dist.js, ext: ".min.js" }),
        $.newer({ dest: pkg.paths.dist.js })
      )
    )
    .pipe($.if(["*.js", "!*.min.js"], $.uglify()))
    .pipe($.if(["*.js", "!*.min.js"], $.rename({ suffix: ".min" })))
    .pipe($.header(banner, { pkg: pkg }))
    .pipe($.size({ gzip: true, showFiles: true }))
    .pipe(gulp.dest(pkg.paths.dist.js))
    .pipe($.filter("**/*.js"))
    .pipe($.browserSync.reload({ stream: true }));
});

// =============================================================================
// =============================================================================
// Copy fonts to DISTRIBUTION
gulp.task("fonts", () => {
  return gulp.src(pkg.paths.src.fonts+'*.{eot,ttf,woff,woff2}')
      .pipe(gulp.dest(pkg.paths.dist.fonts));
});

// =============================================================================
// =============================================================================
// Configure BrowserSync

gulp.task('browser-sync', () => {
  $.browserSync.init({
    proxy: {
        target: pkg.urls.local
    }
  });
});

// =============================================================================
// =============================================================================
// Default task

gulp.task("default", ["css", "js",'fonts', "browser-sync"], () => {
  gulp.watch([pkg.paths.src.scss + "**/*.scss"], ["css"]);
  // gulp.watch([pkg.paths.src.css + "**/*.css"], ["css"]);
  gulp.watch([pkg.paths.src.js + "**/*.js"], ["js"]);
  gulp.watch([pkg.paths.templates + "**/*.{html,htm,twig}"], () => {
    gulp.src(pkg.paths.templates).pipe($.plumber({ errorHandler: onError }));
  }).on("change", $.browserSync.reload);
});
