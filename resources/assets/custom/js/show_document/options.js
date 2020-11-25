const components = [
	'header-data-view',
	'info-divider',
	'page-item',
	'pages-viewer',
	'delete-modal'
];

let options = {
	data: {
		show_modal: false,
		page_selected: null,
		current_form_action: '',
		current_delete_name: ''
	},

	methods: {
		togglePageViewer(is_show, page_id = null){
			if(is_show)
				this.show_modal = true;

			else
				this.show_modal = false;

			setTimeout(function(){
				this.page_selected = page_id;
			}.bind(this), 600);
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