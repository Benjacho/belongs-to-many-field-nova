let mix = require("laravel-mix");

mix
    .setPublicPath("dist")
    .js('resources/js/field.js', 'js')
    .sass('resources/sass/field.scss', 'css')
    .vue({ version: 3 })
    .webpackConfig({
        externals: {
            vue: 'Vue',
        },
    })