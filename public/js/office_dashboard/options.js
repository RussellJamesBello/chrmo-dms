var components = ['generic-table', 'simple-dropdown', 'input-field', 'select-field', 'radio-checkbox-field', 'delete-modal', 'animated-button'];

var options = {
	data: {
		isOffice: null,
		current_form_action: '',
		current_delete_name: ''
	},

	methods: {
		changeOfficeType: function changeOfficeType(isOfficeType) {
			if (isOfficeType) this.isOffice = true;else this.isOffice = false;
		},
		changeCurrentRemove: function changeCurrentRemove(form_url, document_name) {
			this.current_form_action = form_url;
			this.current_delete_name = document_name;
		},
		reinitializeValues: function reinitializeValues() {
			this.current_form_action = '';
			this.current_delete_name = '';
		}
	}
};
