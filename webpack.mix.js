const mix = require("laravel-mix");
var path = require('path');
const webpack = require('webpack');
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
mix.webpackConfig({
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/'),
            '@Project': path.resolve(__dirname, 'resources/js/components/projects/')
        }
    },
    plugins: [
        new webpack.DefinePlugin({
            'process.env': {
                APP_URL: JSON.stringify(process.env.APP_URL),
                PUBLIC_URL: JSON.stringify(process.env.PUBLIC_URL),
                URL_SERVER: JSON.stringify(process.env.URL_SERVER),
                SERVER: JSON.stringify(process.env.HOST_SERVER),
                PORT_SEVER: JSON.stringify(process.env.PORT_SEVER),
            }
        })
    ]
});
mix.js("resources/js/app.js", "public/js")
    .vue()
    .postCss("resources/css/app.css", "public/css", [
        //
    ])
    .postCss("resources/css/datatable.css", "public/css", [
        //
    ]);
