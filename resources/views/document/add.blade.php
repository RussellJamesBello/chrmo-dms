<?php /*
	As you can see in this file, a $checker variable is available even though
	I did not bind something in the Service Container, registered a View Composer,
	or passed it from the controller. It's because I registered it in App\Http\Middleware\RegisterFieldChecker
	and I shared it to all views, same as how it is done in Illuminate\View\Middleware\ShareErrorsFromSession
	where the "automagical" $errors variable is registered.
*/ ?>

@extends('layouts.authenticated')

@section('custom_css')
	<link rel="stylesheet" href="{{ mix('css/add_document/styles.css') }}">
@endsection

@section('sub_content')

<form class="ui text_center form" :class="form_state">

	<div class="ui large icon success message">
		<i class="check circle outline icon"></i>
		<i class="close icon" @click="form_state = ''"></i>

		<div class="content">
			<div class="header">
				Document Successfully added!
			</div>
			<p>You may now add another document.</p>
		</div>
	</div>

	<br>
	<info-divider>Employee</info-divider>

	<div class="fields">
		<div class="one wide field"></div>

		<checkbox-field
			:data-content="new_employee_error"
			data-position="top center"
			class="two"
			kind="toggle"
			label="New Employee"
			name="new_employee"
			v-model="new_employee"
			@change="resetNameOfficeFields">
		</checkbox-field>

		<div class="one wide field"></div>

		<search-field
			:data-content="emp_id_error"
			data-position="top center"
			class="eight"
			:class="{error: emp_id_error}"
			label="Select Employee"
			name="select_employee"
			v-model="search_name"
			:dynamic-attribs="{disabled: new_employee}">
		</search-field>

		<div class="one wide field"></div>

		<select-field
			:data-content="office_error"
			data-position="top center"
			class="three"
			:class="{error: office_error}"
			label="Office" 
			name="office" 
			:options="{{ $offices_and_divisions_json }}"
			v-model="office"
			:default-is-disabled="true"
			disabled-text="-- Select Office --"
			ref="office"
			:dynamic-attribs="{disabled: !new_employee}">
		</select-field>
	</div>

	<br>

	<div class="fields">
		<input-field
			:data-content="first_name_error"
			data-position="top center"
			class="five{!! $checker->check('first_name') !!} <?php //code in the left is kept for reference for future use ?>
			:class="{error: first_name_error}"
			label="First Name"
			type="text"
			name="first_name"
			v-model="first_name"
			ref="first_name"
			:dynamic-attribs="{maxlength: 40, readonly: !new_employee}">
		</input-field>

		<input-field
			:data-content="middle_name_error"
			data-position="top center"
			class="four"
			:class="{error: middle_name_error}"
			label="Middle Name"
			type="text"
			name="middle_name"
			v-model="middle_name"
			ref="middle_name"
			:dynamic-attribs="{maxlength: 30, readonly: !new_employee}">
		</input-field>

		<input-field
			:data-content="last_name_error"
			data-position="top center"
			class="four"
			:class="{error: last_name_error}"
			label="Last Name"
			type="text"
			name="last_name"
			v-model="last_name"
			ref="last_name"
			:dynamic-attribs="{maxlength: 30, readonly: !new_employee}">
		</input-field>

		<select-field
		:data-content="suffix_error"
		data-position="top center"
		class="three"
		:class="{error: suffix_error}"
		label="Suffix" 
		name="suffix" 
		:options="{{ $suffixes_json }}" 
		v-model="suffix"
		disabled-text="-- Select Suffix --"
		ref="suffix"
		:dynamic-attribs="{disabled: !new_employee}">
		</select-field>
	</div>

	<br><br>

	<info-divider>Document</info-divider>

	<!--///////commented it out to reduce user experience complexity. To use custom folder directory for uploads to the server, uncomment this
	<left-labeled-input-field
		:data-content="folder_directory_error"
		data-position="top center"
		class="sixteen"
		:class="{error: folder_directory_error}"
		label="Folder Directory"
		:left-label="completeLabel"
		type="text"
		name="folder_directory"
		v-model="folder_directory"
		ref="folder_directory">
	</left-labeled-input-field>
	-->

	<div class="fields">
		<input-field
			:data-content="document_name_error"
			data-position="top center"
			class="eight"
			:class="{error: document_name_error}"
			label="Document Name"
			type="text"
			name="document_name"
			v-model="document_name"
			ref="document_name">
		</input-field>

		<tagging-dropdown-field
			:data-content="tags_error"
			data-position="top center"
			class="eight"
			:class="{error: tags_error}"
			label="Tags (Keywords)"
			name="tags"
			v-model="tags"
			default-text="Type a tag and press Enter to add another tag."
			ref="tags">
		</tagging-dropdown-field>
	</div>

	<div class="fields">
		<div class="six wide field"></div>

		{{-- This component's value prop is not bound to the root prop uploads because of the how the FileList API works. Setting of the uploads's value happens in setUploadValue() --}}
		<input-field
			v-if="uploads.length < 800"
			:data-content="uploads_error"
			data-position="top center"
			class="four"
			:class="{error: uploads_error}"
			:label="uploads.length > 0 ? 'Upload More Pages' : 'Upload Pages'"
			type="file"
			name="uploads[]"
			@input="setUploadValue"
			:dynamic-attribs="{multiple: '', accept: '.png, .jpg, .jpeg', style: 'color: white'}"
			ref="uploads">
		</input-field>
	</div>

	<br>
	<div v-if="uploads.length">
		<info-divider>Pages</info-divider>

		<button class="ui left floated red button" type="button" @click="uploads.splice(0, uploads.length)">
			Remove All
		</button>

		<div class="ui right floated tiny statistic">
			<div class="value">
				<i class="file alternate outline icon"></i> 
				@{{ uploads.length }}
			</div>

			<div class="label">
				Pages (Max. of 800)
			</div>
		</div>

		<div class="ui divided items">
			<edit-page-item
				v-for="(upload, index) in uploads"
				:error-message="upload.error"
				:file="upload.page"
				:page-number="index + 1"
				:order="getIfFirstOrLast(index)"
				:key="upload.page.name + index + upload.page.size + upload.page.lastModified"
				@remove="uploads.splice($event, 1)"
				@move="movePageListItem"
				@mouseover="triggerPopup">
			</edit-page-item>
		</div>
	</div>

	<br>
	<animated-button
		type="button"
		class="fluid basic blue fade"
		visible-icon="large save"
		hidden-text="SUBMIT &amp; UPLOAD"
		@click="sendFormThroughAjax">
	</animated-button>

</form>

@endsection

@section('vue_options')
	<script src="{{ mix('/js/add_document/reactive_data.js') }}"></script>
	<script src="{{ mix('/js/add_document/options.js') }}"></script>
@endsection

@section('sub_custom_js')
	<script src="{{ mix('/js/add_document/logic.js') }}"></script>
@endsection