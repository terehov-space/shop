const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/frontend/app.js', 'public/js')
    .vue({version: 2})
    .sass('resources/scss/app.scss', 'public/css', [
        //
    ]);

mix.js('resources/js/admin/app.js', 'public/js/admin.js')
    .vue({version: 2})
    .sass('resources/scss/admin.scss', 'public/css/admin.css', [
        //
    ]);
