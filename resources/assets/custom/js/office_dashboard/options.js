const components = [
	'generic-table',
	'simple-dropdown',
	'input-field',
	'select-field',
	'radio-checkbox-field',
	'delete-modal',
	'animated-button'
];

let options = {
	data: {
		isOffice: null,
		current_form_action: '',
		current_delete_name: ''
	},

	methods: {
		changeOfficeType(isOfficeType){
			if(isOfficeType)
				this.isOffice = true;
			else
				this.isOffice = false;
		},

		changeCurrentRemove(form_url, document_name){
			this.current_form_action = form_url;
			this.current_delete_name = document_name;
		},

		reinitializeValues(){
			this.current_form_action = '';
			this.current_delete_name = '';
		}
	}
};