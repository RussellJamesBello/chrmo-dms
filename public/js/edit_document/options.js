var _data;

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

var components = ['info-divider', 'left-labeled-input-field', 'input-field', 'tagging-dropdown-field', 'edit-page-item', 'animated-button'];

var options = {
	data: (_data = {
		folder_directory: '',
		document_name: '',
		tags: '',
		uploads: [],

		folder_directory_error: '',
		document_name_error: '',
		tags_error: '',
		uploads_error: ''

	}, _defineProperty(_data, 'uploads', []), _defineProperty(_data, 'uploads_altered', false), _defineProperty(_data, 'show_existing_uploads', false), _defineProperty(_data, 'form_state', ''), _data),

	methods: {
		getIfFirstOrLast: function getIfFirstOrLast(index) {
			index += 1;

			if (this.uploads.length == 1) return 'none';else if (index == 1) return 'first';else if (index == this.uploads.length) return 'last';else return '';
		},
		removePageListItem: function removePageListItem(event) {
			this.uploads.splice(event, 1);
			this.uploads_altered = true;
		},
		movePageListItem: function movePageListItem(direction, page_number) {
			var temp = void 0;

			if (this.uploads_altered == false) this.uploads_altered = true;

			if (direction == 'down') {
				temp = this.uploads[page_number + 1];
				this.$set(this.uploads, page_number + 1, this.uploads[page_number]);
				this.$set(this.uploads, page_number, temp);
			} else if (direction == 'up') {
				temp = this.uploads[page_number - 1];
				this.$set(this.uploads, page_number - 1, this.uploads[page_number]);
				this.$set(this.uploads, page_number, temp);
			}
		},
		removeUploads: function removeUploads() {
			this.uploads.splice(0, this.uploads.length);

			if (this.uploads_altered == false) this.uploads_altered = true;
		},
		setUploadValue: function setUploadValue(value, event) {
			var length = event.target.files.length;
			var uploads_length = this.uploads.length;

			this.uploads_altered = true;

			if (length + uploads_length > 800) {
				window.alert('You may only upload 800 pages at a time.');
				return;
			}

			for (var i = 0; i < length; i++) {
				var type = event.target.files[i].type;

				if (!(type == 'image/jpeg' || type == 'image/png')) continue;

				//iteration variable is added to the length of uploads to enable adding more pages rather than
				//replacing the existing element in the current iteration.
				//Due to the functionality that enables the user to move a page up or down, it's better to store
				//per page error inside uploads along with the File object than to store it as a separate variable
				//just like the errors for the other form fields. If it is stored as a separate variable, then it will
				//not react to data changes from uploads. For example, page 1 has errors, if it is moved down, then the
				//page swapped with page 1 will have page 1's error. Not good. When per page error is stored inside uploads,
				//then if uploads is altered, the page's error is moved along with its page.
				this.$set(this.uploads, i + uploads_length, { 'page': event.target.files[i], 'error': '' });
			}
		},
		sendFormThroughAjax: function sendFormThroughAjax() {
			this.form_state = 'loading';

			var form_data = new FormData();

			form_data.append('_method', 'PUT');
			form_data.append('folder_directory', this.folder_directory);
			form_data.append('document_name', this.document_name);
			form_data.append('tags', this.tags);

			this.uploads.forEach(function (image, index) {
				form_data.append('uploads[]', image.page);
			});

			if (this.uploads_altered == true) form_data.append('uploads_altered', 1);else form_data.append('uploads_altered', 0);

			axios({
				method: 'post',
				url: window.location.href,
				data: form_data
			}).then(function (response) {
				this.uploads_altered = false;
				//this.folder_directory = '';
				//this.document_name = '';
				//this.tags = '';
				//this.uploads = [];

				this.folder_directory_error = '';
				this.document_name_error = '';
				this.tags_error = '';
				this.uploads_error = '';

				//To clear out the dropdown's DOM and since setting this.tags to '' does not
				//totally remove the tags, clear the dropdown through jQuery.
				//$('.ui.dropdown').dropdown('clear');

				window.scrollTo(0, 0);

				this.form_state = 'success';
			}.bind(this)).catch(function (error) {
				var validation_errors = error.response.data.errors;

				this.folder_directory_error = validation_errors.folder_directory ? validation_errors.folder_directory[0] : '';
				this.document_name_error = validation_errors.document_name ? validation_errors.document_name[0] : '';
				this.tags_error = validation_errors.tags ? validation_errors.tags[0] : '';
				this.uploads_error = validation_errors.uploads ? validation_errors.uploads[0] : '';

				var length = this.uploads.length;

				for (var i = 0; i < length; i++) {
					this.uploads[i].error = validation_errors['uploads.' + i] ? validation_errors['uploads.' + i][0] : '';
				}this.form_state = 'error';
			}.bind(this));
		},
		triggerPopup: function triggerPopup(event, id) {
			//since Semantic UI is dependent on jQuery and PageListItem component (Vue component using Semantic's Popup) is not letting
			//jQuery automatically update its state when hovered on, we need to manually trigger the popup here.
			$('#' + id).popup('show');
		}
	},

	mounted: function mounted() {
		var _this = this;

		/*commented it out to reduce user experience complexity. To use custom folder directory for uploads to the server, uncomment this
  this.folder_directory = this.$refs.folder_directory.dataOld;*/
		this.document_name = this.$refs.document_name.dataOld;
		this.tags = this.$refs.tags.dataOld;

		var _loop = function _loop(i) {
			fetch(page_url + pages[i]).then(function (res) {
				return res.blob();
			}).then(function (blob) {
				blob.lastModified = new Date().getMilliseconds();

				if (blob.type == 'image/jpeg') blob.name = i + 1 + '.jpeg';else if (blob.type == 'image/png') blob.name = i + 1 + '.png';

				_this.$set(_this.uploads, i, { 'page': blob, 'error': '' });
			});
		};

		for (var i = 0; i < pages.length; i++) {
			_loop(i);
		}

		var timespan = 0;

		if (pages.length <= 15) timespan = 2500;else if (pages.length <= 25) timespan = 4000;else if (pages.length <= 35) timespan = 5500;else timespan = 7000;

		setTimeout(function () {
			this.show_existing_uploads = true;
		}.bind(this), timespan);
	}
};
