const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.options({
    processCssUrls: false
});
mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .scripts('resources/sources/js/*.js', 'public/assets/js/build.js')
    .styles('resources/sources/css/*.css', 'public/assets/css/build.css')
    /*  .css('resources/sources/css/*', 'public/assets/css/build.css') */
    /* .css('resources/sources/css/*', 'public/assets/css/build.css')*/
    /* .css('resources/sources/css/animate.css', 'public/assets/css/animate.css')
     .css('resources/sources/css/bootstrap.min.css', 'public/assets/css/bootstrap.min.css')
     .css('resources/sources/css/font-awesome.min.css', 'public/assets/css/font-awesome.min.css')
     .css('resources/sources/css/main.css', 'public/assets/css/main.css')
     .css('resources/sources/css/price-range.css', 'public/assets/css/price-range.css')
     .css('resources/sources/css/responsive.css', 'public/assets/css/responsive.css')*/
    /*   .js('resources/sources/js/bootstrap.min.js', 'public/js')
       .js('resources/sources/js/html5shiv.js', 'public/js')
       .js('resources/sources/js/jquery.js', 'public/js')
       .js('resources/sources/js/jquery.prettyPhoto.js', 'public/js')
       .js('resources/sources/js/jquery.scrollUp.min.js', 'public/js')
       .js('resources/sources/js/main.js', 'public/js')
       .js('resources/sources/js/price-range.js', 'public/js')*/
    .sourceMaps()
    .version();

