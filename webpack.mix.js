const mix = require('laravel-mix');

require('laravel-mix-svelte');

const path = require('path');
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

mix.js("resources/js/Booking/app.js", "public/tenancy/assets/js")
    .js("resources/js/Booking/booking.js", "public/tenancy/assets/js")
    .js("resources/js/Booking/payment.js", "public/tenancy/assets/js")
    .js("resources/js/Booking/class-payment.js", "public/tenancy/assets/js")
    .js("resources/js/plugin.js", "public/js/plugin.js")
    .svelte({
        dev: true
    })
    .postCss('resources/css/app.css', 'public/css', [
        require("tailwindcss")
    ])
    .postCss('resources/css/class.css', 'public/css', [
        require("tailwindcss")
    ])
    .combine([
        'resources/js/Booking/Plugins/jquery.min.js',
        'resources/js/Booking/Plugins/moment.min.js',
        'resources/js/Booking/Plugins/bootstrap.bundle.min.js',
        'resources/js/Booking/Plugins/blockui.min.js',
        'resources/js/Booking/Plugins/bootstrap_limitless.js',
        'resources/js/Booking/Plugins/hover_dropdown.min.js',
        'resources/js/Booking/Plugins/select2.min.js',
        'resources/js/Booking/Plugins/bootstrap_multiselect.js',
        'resources/js/Booking/Plugins/sweet_alert.min.js',
        'resources/js/Booking/Plugins/daterangepicker.min.js',
        'resources/js/Booking/Plugins/popper.min.js',
        'resources/js/Booking/Plugins/pnotify.min.js',
        'resources/js/Booking/Plugins/uniform.min.js',
        'resources/js/Booking/Plugins/tippy.min.js',
        'resources/js/Booking/Plugins/dragula.min.js',
        'resources/js/Booking/Plugins/sticky.min.js',
        'resources/js/Booking/Plugins/datatables.min.js',
        'resources/js/Booking/Plugins/datatables_user.js',
        'resources/js/Booking/Plugins/froala_editor.pkgd.min.js',
        'resources/js/Booking/Plugins/jscolor.min.js',
        'resources/js/Booking/Plugins/choices.min.js',
        'resources/js/html5-qrcode.min.js',
        'resources/js/voca.min.js',
    ], 'public/tenancy/assets/js/plugins.min.js')
    .sass("resources/sass/Booking/app.scss", "public/tenancy/assets/css")
    .sass("resources/sass/Booking/booking.scss", "public/tenancy/assets/css")
    .postCss("resources/css/extend.css", "public/css/booking-extend.css", [
		require("tailwindcss"),
	])
    .webpackConfig({
        module: {
            rules: [
                {
                    test: /\.(postcss)$/,
                    use: [
                        'vue-style-loader',
                        { loader: 'css-loader', options: { importLoaders: 1 } },
                        'postcss-loader'
                    ]
                }
            ],
        }
    })
    .alias({
        '@vuePath': path.join(__dirname, 'resources/js/Vue')
    });
