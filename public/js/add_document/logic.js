var tagging_field = $('.ui.multiple.search.selection.dropdown');

$('.field').popup();
$('.ui.checkbox').checkbox();

tagging_field.dropdown({
	allowAdditions: true,
	ignoreCase: true,

	//this function is required because options in menu that came from ajax call, if removed, is not totally removed
	//internally. So when a value is removed, manually remove it. A bit of processing takes place here because
	//the string passed to this function is in all lower-cased.
	onLabelRemove: function onLabelRemove(value) {
		//1. get the index. get the current value of the field, convert it to lower-case to match the case of value, and look for the index of its occurence
		var index = tagging_field.dropdown('get value').toLocaleLowerCase().indexOf(value);
		//2. extract the value that has the correct letter casing to be removed using the index and the length of value
		var exact_value = tagging_field.dropdown('get value').substr(index, value.length);
		//3. manually remove it
		tagging_field.dropdown('remove selected', exact_value);
	}
});

//for tags field's dynamic dropdown selection menu. the element input.search is where the user will type the next tag to add
$('.ui.multiple.search.selection.dropdown>input.search').keyup(function (event) {
	var tag = $(this).val();

	//enter
	if (event.keyCode == 13) {
		tagging_field.dropdown('set selected', tag);
		$(this).val('');
	}

	//up arrow or down arrow
	else if (event.keyCode == 38 || event.keyCode == 40) return;

		//request through ajax after 100ms, save the result in the dropdown, and show it
		else if (tag.length >= 2) setTimeout(function () {
				axios.get('tags', {
					params: {
						tag: tag
					}
				}).then(function (response) {
					if (response.data.results.length != 0) {
						tagging_field.dropdown('setup menu', { values: response.data.results });
						tagging_field.dropdown('show');
					}
				});
			}, 100);
});

$('.ui.search').search({
	apiSettings: {
		url: 'employee-search?keyword={query}'
	},
	transition: 'swing down',

	duration: 1000,

	maxResults: 15,

	searchDelay: 250,

	minCharacters: 5,

	fields: {
		title: 'whole_name',
		description: 'office'
	},

	onSelect: function onSelect(result, response) {
		reactive_data.search_name = result.whole_name;
		reactive_data.emp_id = result.emp_id;
		reactive_data.office = result.emp_office;
		reactive_data.first_name = result.emp_first_name;
		reactive_data.middle_name = result.emp_middle_name;
		reactive_data.last_name = result.emp_last_name;
		reactive_data.suffix = result.emp_suffix;
	}
});

//to prevent traditional form submission when user hit Enter because the Tags field uses Enter to add a tag
//https://stackoverflow.com/questions/895171/prevent-users-from-submitting-a-form-by-hitting-enter
$(document).ready(function () {
	$(window).keydown(function (event) {
		if (event.keyCode == 13) {
			event.preventDefault();
			return false;
		}
	});
});
