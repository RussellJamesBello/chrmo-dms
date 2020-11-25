let mix = require('laravel-mix');

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

mix.js('resources/assets/js/app.js', 'public/js')
	//pages for logged in user
	.babel(['resources/assets/custom/js/authenticated.js'], 'public/js/authenticated.js')
	//login page
	.babel(['resources/assets/custom/js/login/options.js'], 'public/js/login/options.js')
	//add document page
	.styles(['resources/assets/custom/css/add_document/styles.css'], 'public/css/add_document/styles.css')
	.babel(['resources/assets/custom/js/add_document/logic.js'], 'public/js/add_document/logic.js')
	.babel(['resources/assets/custom/js/add_document/options.js'], 'public/js/add_document/options.js')
	.babel(['resources/assets/custom/js/add_document/reactive_data.js'], 'public/js/add_document/reactive_data.js')

	//employee list page
	.babel(['resources/assets/custom/js/employee_list/options.js'], 'public/js/employee_list/options.js')

	//employee list page
	.babel(['resources/assets/custom/js/employee_info/options.js'], 'public/js/employee_info/options.js')

	//edit employee page
	.babel(['resources/assets/custom/js/edit_employee/options.js'], 'public/js/edit_employee/options.js')

	//edit document page
	.babel(['resources/assets/custom/js/edit_document/options.js'], 'public/js/edit_document/options.js')
	.babel(['resources/assets/custom/js/edit_document/logic.js'], 'public/js/edit_document/logic.js')

	//show document page
	.babel(['resources/assets/custom/js/show_document/options.js'], 'public/js/show_document/options.js')

	//user dashboard page
	.babel(['resources/assets/custom/js/user_dashboard/options.js'], 'public/js/user_dashboard/options.js')

	//office dashboard page
	.babel(['resources/assets/custom/js/office_dashboard/options.js'], 'public/js/office_dashboard/options.js')
	.babel(['resources/assets/custom/js/office_dashboard/logic.js'], 'public/js/office_dashboard/logic.js')

	//edit user page
	.babel(['resources/assets/custom/js/edit_user/options.js'], 'public/js/edit_user/options.js')

	//edit office page
	.babel(['resources/assets/custom/js/edit_office/options.js'], 'public/js/edit_office/options.js')

	//edit division page
	.babel(['resources/assets/custom/js/edit_division/options.js'], 'public/js/edit_division/options.js')

	//style for all pages
	.styles(['resources/assets/custom/css/globals.css'], 'public/css/globals.css')
	.version();
