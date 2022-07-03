let mix = require("laravel-mix");

const path = require("path");
let directory = path.basename(path.resolve(__dirname));

const source = "packages/" + directory;
const dist = "public/vendor/core/packages/" + directory;

mix.sass(source + "/resources/assets/sass/revision.scss", dist + "/css")
    .js(source + "/resources/assets/js/revision.js", dist + "/js")

    .copy(
        source + "/resources/assets/js/html-diff.js",
        dist + "/js/html-diff.js"
    )

    .copyDirectory(dist + "/css", source + "/public/css")
    .copyDirectory(dist + "/js", source + "/public/js");
