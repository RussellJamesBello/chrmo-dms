var components = ['header-data-view', 'info-divider', 'page-item', 'pages-viewer', 'delete-modal'];

var options = {
	data: {
		show_modal: false,
		page_selected: null,
		current_form_action: '',
		current_delete_name: ''
	},

	methods: {
		togglePageViewer: function togglePageViewer(is_show) {
			var page_id = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

			if (is_show) this.show_modal = true;else this.show_modal = false;

			setTimeout(function () {
				this.page_selected = page_id;
			}.bind(this), 600);
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
