const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
   .postCss('resources/css/app.css', 'public/css', [
       require('postcss-import'),
       require('tailwindcss'),
       require('autoprefixer'),
   ])
   .postCss('resources/css/home.css', 'public/css', [
       require('postcss-import'),
       require('tailwindcss'),
       require('autoprefixer'),
   ]);

if (mix.inProduction()) {
    mix.version();
}

// Configuración de Browsersync
mix.browserSync({
    proxy: 'http://127.0.0.1:8000', // La URL que `php artisan serve` utiliza
    files: [
        'resources/views/**/*.blade.php', // Archivos Blade
        'resources/css/**/*.css',         // Archivos CSS
        'resources/js/**/*.js',           // Archivos JS
        'app/**/*.php',                   // Archivos PHP en la app
        'routes/**/*.php'                 // Archivos PHP en las rutas
    ],
    open: false, // Opcional: no abrir automáticamente el navegador
    notify: false // Opcional: desactivar notificaciones de Browsersync en el navegador
});
