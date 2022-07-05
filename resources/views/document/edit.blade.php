@extends('layouts.authenticated')

@section('sub_content')

<div style="overflow: auto;">
	<a href="{{ url("employees/{$document->employee->employee_id}") }}" class="ui grey right floated mini button">Back</a>
	<a href="{{ url('documents/' . $document->document_id) }}" class="ui green right floated mini button">Info</a>
</div>

<form class="ui text_center form segment" :class="form_state">
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
	
	<!--///////commented it out to reduce user experience complexity. To use custom folder directory for uploads to the server, uncomment this
	<left-labeled-input-field
		:data-content="folder_directory_error"
		data-position="top center"
		class="sixteen"
		:class="{error: folder_directory_error}"
		label="Folder Directory"
		left-label="{{ $left_label }}"
		type="text"
		name="folder_directory"
		v-model="folder_directory"
		data-old="{{ $document->custom_folder_path }}"
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
			data-old="{{ $document->name }}"
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
			data-old="{{ $document->keywords }}"
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

		<button class="ui left floated red button" type="button" @click="removeUploads">
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

		<div class="ui divided items" v-if="show_existing_uploads">
			<edit-page-item
				v-for="(upload, index) in uploads"
				:error-message="upload.error"
				:file="upload.page"
				:page-number="index + 1"
				:order="getIfFirstOrLast(index)"
				:key="upload.page.name + index + upload.page.size + upload.page.lastModified"
				@remove="removePageListItem($event)"
				@move="movePageListItem"
				@mouseover="triggerPopup">
			</edit-page-item>
		</div>
	</div>

	<br>
	<animated-button
		type="button"
		class="fluid basic blue fade"
		visible-icon="large edit"
		hidden-text="Edit Document"
		@click="sendFormThroughAjax">
	</animated-button>
</form>

@endsection

@section('vue_options')

<script>
	var pages = {!! $document_contents->toJson() !!};
	var page_url = '{{ $page_url }}';
</script>
<script src="{{ mix('/js/edit_document/options.js') }}"></script>

@endsection

@section('sub_custom_js')
	<script src="{{ mix('/js/edit_document/logic.js') }}"></script>
@endsection