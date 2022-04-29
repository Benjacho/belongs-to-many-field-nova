let mix = require("laravel-mix");

mix
    .setPublicPath("dist")
    .js('resources/js/field.js', 'js')
    .vue({ version: 3 })
    .webpackConfig({
        externals: {
            vue: 'Vue',
        },
    })