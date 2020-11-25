var components = ['select-field', 'generic-table', 'simple-dropdown', 'delete-modal'];

var options = {
	data: {
		current_form_action: '',
		current_delete_name: ''
	},

	methods: {
		requestEmployeesFromSpecificOffice: function requestEmployeesFromSpecificOffice($event) {
			window.location.href = location.protocol + '//' + location.host + location.pathname + '?office=' + $event;
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
