const mix = require('laravel-mix');

mix.js('resources/js/main.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .copy('node_modules/video.js/dist/video-js.css', 'public/css')
   .copy('node_modules/videojs-resolution-switcher/dist/videojs-resolution-switcher.css', 'public/css')
   .copy('node_modules/video.js/dist/video.min.js', 'public/js')
   .copy('node_modules/videojs-resolution-switcher/dist/videojs-resolution-switcher.js', 'public/js')
   .setPublicPath('public');
