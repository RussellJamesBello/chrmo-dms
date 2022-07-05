var components = ['select-field', 'checkbox-field', 'generic-table', 'animated-button'];

var options = {
	data: {
		new_offices_status: checkbox_statuses
	},

	methods: {
		disableNewOfficeCheckbox: function disableNewOfficeCheckbox(index) {
			if (this.new_offices_status[index] == true) Vue.set(this.new_offices_status, index, false);else Vue.set(this.new_offices_status, index, true);
		}
	}
};
