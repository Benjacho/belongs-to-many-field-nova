let mix = require("laravel-mix");

mix
  .setPublicPath("dist")
  .js("resources/js/field.js", "js")
  .vue()
  .sass("resources/sass/field.scss", "css");
