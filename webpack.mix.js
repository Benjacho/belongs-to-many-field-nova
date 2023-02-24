let mix = require("laravel-mix");

require('./nova.mix')

mix
    .setPublicPath("dist")
    .js("resources/js/field.js", "js")
    .vue({version: 3})
    .sass("resources/sass/field.scss", "css")
    .nova('benjacho/belongs-to-many-field-nova');
