const components = [
	'input-field', 
	'select-field',
	'left-labeled-input-field',
	'tagging-dropdown-field',
	'search-field',
	'edit-page-item',
	'animated-button',
	'info-divider',
	'checkbox-field'
];

let options = {
	data: reactive_data,

	methods: {
		getIfFirstOrLast(index){
			index += 1;

			if(this.uploads.length == 1)
				return 'none';

			else if(index == 1)
				return 'first';

			else if(index == this.uploads.length)
				return 'last';

			else
				return '';
		},

		movePageListItem(direction, page_number){
			let temp;

			if(direction == 'down')
			{
				temp = this.uploads[page_number + 1];
				this.$set(this.uploads, page_number + 1, this.uploads[page_number]);
				this.$set(this.uploads, page_number, temp);
			}

			else if(direction == 'up')
			{
				temp = this.uploads[page_number - 1];
				this.$set(this.uploads, page_number - 1, this.uploads[page_number]);
				this.$set(this.uploads, page_number, temp);
			}
		},

		setUploadValue(value, event){
			let length = event.target.files.length;
			let uploads_length = this.uploads.length;

			if(length + uploads_length > 800)
			{
				window.alert('You may only upload 800 pages at a time.');
				return;
			}

			for(let i = 0; i < length; i++)
			{
				let type = event.target.files[i].type;

				if(!(type == 'image/jpeg' || type == 'image/png'))
					continue;
				
				//iteration variable is added to the length of uploads to enable adding more pages rather than
				//replacing the existing element in the current iteration.
				//Due to the functionality that enables the user to move a page up or down, it's better to store
				//per page error inside uploads along with the File object than to store it as a separate variable
				//just like the errors for the other form fields. If it is stored as a separate variable, then it will
				//not react to data changes from uploads. For example, page 1 has errors, if it is moved down, then the
				//page swapped with page 1 will have page 1's error. Not good. When per page error is stored inside uploads,
				//then if uploads is altered, the page's error is moved along with its page.
				this.$set(this.uploads, i + uploads_length, {'page': event.target.files[i], 'error': ''});
			}
		},

		resetNameOfficeFields(){
			this.emp_id = '';
			this.search_name = '';
			this.office = '';
			this.first_name = '';
			this.middle_name = '';
			this.last_name = '';
			this.suffix = '';
		},

		sendFormThroughAjax(){
			this.form_state = 'loading';

			var form_data = new FormData();

			if(this.new_employee)
				form_data.append('new_employee', this.new_employee);
			else
				form_data.append('emp_id', this.emp_id);
			
			form_data.append('office', this.office);
			form_data.append('first_name', this.first_name);
			form_data.append('middle_name', this.middle_name);
			form_data.append('last_name', this.last_name);
			form_data.append('suffix', this.suffix);
			form_data.append('folder_directory', this.folder_directory);
			form_data.append('document_name', this.document_name);
			form_data.append('tags', this.tags);

			this.uploads.forEach(function(image, index){
				form_data.append('uploads[]', image.page);
			});
			
			axios({
				method: 'post',
				url: '/add-document',
				data: form_data
			})
			.then(function(response){
				this.new_employee = false;
				this.search_name = '';
				this.emp_id = '';
				this.office = '';
				this.first_name = '';
				this.middle_name = '';
				this.last_name = '';
				this.suffix = '';
				this.folder_directory = '';
				this.document_name = '';
				this.tags = '';
				this.uploads = [];

				this.new_employee_error = '';
				this.emp_id_error = '';
				this.office_error = '';
				this.first_name_error = '';
				this.middle_name_error = '';
				this.last_name_error = '';
				this.suffix_error = '';
				this.folder_directory_error = '';
				this.document_name_error = '';
				this.tags_error = '';
				this.uploads_error = '';

				//To clear out the dropdown's DOM and since setting this.tags to '' does not
				//totally remove the tags, clear the dropdown through jQuery.
				$('.ui.dropdown').dropdown('clear');

				window.scrollTo(0, 0);

				this.form_state = 'success';
			}.bind(this))
			.catch(function (error) {
				let validation_errors = error.response.data.errors;

				this.new_employee_error = validation_errors.new_employee ? validation_errors.new_employee[0] : '';
				this.emp_id_error = validation_errors.emp_id ? validation_errors.emp_id[0] : '';
				this.office_error = validation_errors.office ? validation_errors.office[0] : '';
				this.first_name_error = validation_errors.first_name ? validation_errors.first_name[0] : '';
				this.middle_name_error = validation_errors.middle_name ? validation_errors.middle_name[0] : '';
				this.last_name_error = validation_errors.last_name ? validation_errors.last_name[0] : '';
				this.suffix_error = validation_errors.suffix ? validation_errors.suffix[0] : '';
				this.folder_directory_error = validation_errors.folder_directory ? validation_errors.folder_directory[0] : '';
				this.document_name_error = validation_errors.document_name ? validation_errors.document_name[0] : '';
				this.tags_error = validation_errors.tags ? validation_errors.tags[0] : '';
				this.uploads_error = validation_errors.uploads ? validation_errors.uploads[0] : '';

				let length = this.uploads.length;

				for(let i = 0; i < length; i++)
					this.uploads[i].error = validation_errors['uploads.' + i] ? validation_errors['uploads.' + i][0] : '';

				this.form_state = 'error';
			}.bind(this));
		},

		triggerPopup(event, id){
			//since Semantic UI is dependent on jQuery and PageListItem component (Vue component using Semantic's Popup) is not letting
			//jQuery automatically update its state when hovered on, we need to manually trigger the popup here.
			$('#' + id).popup('show');
		}
	},

	computed: {
		completeLabel(){
			//concatinate all name parts and remove all space
			let name = (this.first_name + ' ' + this.middle_name + ' ' + this.last_name + ' ' + this.suffix.replace('.', '')).replace(/ +/g, "");

			return this.office + '/' + name + '/';
		}
	},

	mounted(){
		/*
			It is possible to declare all of this in a script tag in the view file that referenced this file but
			there are problems with that approach. The problems are:
			1. Laravel's blade template syntax outputs HTML entity name/number. In other words, it escapes the outputted data.
			This is not a problem if blade outputs the data inside anything other than script tag. If blade outputs data inside a
			script tag, the script tag cannot interpret the HTML entity name/number because it "speaks" JavaScript, not HTML;
			2. No caching. If I declare the codes below as an internal script using a script tag, caching is not utilized.
			3. Messy code. There is no separation of concerns. While using internal scripts still functions the way it is expected
			to be, HTML and JavaScript is mixed. It is better to declare it here and use Vue's $refs to get the values of the
			variables below from the compponents' dataOld property.
		*/
		/*
		commented out because AJAX will be used instead of traditional form submission

		this.office = this.$refs.office.dataOld;
		this.first_name = this.$refs.first_name.dataOld;
		this.middle_name = this.$refs.middle_name.dataOld;
		this.last_name = this.$refs.last_name.dataOld;
		this.suffix = this.$refs.suffix.dataOld;
		this.folder_directory = this.$refs.folder_directory.dataOld;
		this.document_name = this.$refs.document_name.dataOld;
		this.tags = this.$refs.tags.dataOld;
		this.uploads = this.$refs.uploads.dataOld;
		*/
	}
};