let mix = require('laravel-mix');

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

mix.js(['resources/assets/js/app.js', 'node_modules/bootstrap-select/js/bootstrap-select.js'], 'public/js/all.js')
   .sass('resources/assets/sass/app.scss', 'public/css');

mix.less('node_modules/bootstrap-select/less/bootstrap-select.less', 'public/css/all.css');