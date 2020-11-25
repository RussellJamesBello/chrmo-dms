
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const registered_components = {
	'input-field': require('./components/form/InputField.vue'),
	'select-field': require('./components/form/SelectField.vue'),
	'left-labeled-input-field': require('./components/form/LeftLabeledInputField.vue'),
	'tagging-dropdown-field': require('./components/form/TaggingDropdownField.vue'),
	'search-field': require('./components/form/SearchField.vue'),
	'checkbox-field' : require('./components/form/CheckboxField.vue'),
	'radio-checkbox-field' : require('./components/form/RadioCheckboxField.vue'),
	'delete-modal' : require('./components/form/DeleteModal.vue'),

	'edit-page-item': require('./components/page_view/EditPageItem.vue'),
	'page-item': require('./components/page_view/PageItem.vue'),
	'pages-viewer': require('./components/page_view/PagesViewer.vue'),

	'animated-button': require('./components/button/AnimatedButton.vue'),

	'generic-table': require('./components/table/GenericTable.vue'),

	'simple-dropdown': require('./components/dropdown/SimpleDropdown.vue'),

	'header-data-view': require('./components/typography/HeaderDataView.vue'),
	'info-divider': require('./components/typography/InfoDivider.vue')
};

let vm_options = {};

//sets options for Vue if options is set
if(typeof options == 'object')
	vm_options = options;

//sets the components to be used if components is set
if(typeof components == 'object')
{
	//loop below will not work if vm_options.components is not assigned
	vm_options.components = {};
	let length = components.length;

	for(let key = 0; key < length; key++)
	{
		if(registered_components.hasOwnProperty(components[key]))
			vm_options.components[components[key]] = registered_components[components[key]];
	}
}

//sets the default element to bind to if the options object did not define it
if(vm_options.el == null)
	vm_options.el = '#specific_content';

const app = new Vue(vm_options);