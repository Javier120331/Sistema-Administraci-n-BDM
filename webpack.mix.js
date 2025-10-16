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

mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css');
mix.copy("vendor/bootstrap-select/bootstrap-select/dist", "public/vendor/bootstrap-select");
mix.copy("vendor/jasny/bootstrap/docs/dist", "public/vendor/jasny-bootstrap");
mix.copy("vendor/ivaynberg/select2/dist", "public/vendor/select2");
mix.copy("vendor/moment/moment/min", "public/vendor/moment");
mix.copy("vendor/twbs/bootstrap/dist", "public/vendor/bootstrap");
mix.copy("vendor/fullcalendar/fullcalendar/dist/", "public/vendor/fullcalendar");
mix.copy("vendor/tinymce/tinymce/", "public/vendor/tinymce");
