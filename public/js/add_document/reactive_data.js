//this originally came from ./options.js but placed in a separate file because ./logic.js needs to use this variable's properties
var reactive_data = {
		//form field models
		new_employee: false,
		search_name: '',
		emp_id: '', //variable to use when new_employee is false
		office: '',
		first_name: '',
		middle_name: '',
		last_name: '',
		suffix: '',
		folder_directory: '',
		document_name: '',
		tags: '',
		uploads: [],

		form_state: '',
		//error variables (error per page is stored in uploads)
		new_employee_error: '',
		emp_id_error: '',
		office_error: '',
		first_name_error: '',
		middle_name_error: '',
		last_name_error: '',
		suffix_error: '',
		folder_directory_error: '',
		document_name_error: '',
		tags_error: '',
		uploads_error: ''
};
