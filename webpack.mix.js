const mix = require('laravel-mix');
const path = require('path');

mix.browserSync(
  {
    proxy: 'color-vue.dev'
  });

mix.webpackConfig({
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './resources/assets/js'),
      src: path.resolve(__dirname, './resources/assets/js'),
      components: path.resolve(__dirname, './resources/assets/js/components'),
      views: path.resolve(__dirname, './resources/assets/js/views'),
      styles: path.resolve(__dirname, './resources/assets/js/styles'),
      utils: path.resolve(__dirname, './resources/assets/js/utils'),
      router: path.resolve(__dirname, './resources/assets/js/router'),
      vendor: path.resolve(__dirname, './resources/assets/js/vendor'),
      store: path.resolve(__dirname, './resources/assets/js/store'),
      api: path.resolve(__dirname, './resources/assets/js/api'),
      assets: path.resolve(__dirname, './resources/assets/js/assets')
    }
  }
});

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

mix.js('resources/assets/js/admin.js', 'public/js')
  .sass('resources/assets/sass/app.scss', 'public/css');
