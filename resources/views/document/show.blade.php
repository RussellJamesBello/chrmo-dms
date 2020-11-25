@extends('layouts.authenticated')

@section('sub_content')

<pages-viewer
	modal-id="picture_viewer"
	:modal-show="show_modal"
	:clicked-page="page_selected"
	page-url="{{ $page_url }}"
	:pages="{{ $pages->toJson() }}"
	@closed="togglePageViewer(false)">
</pages-viewer>

<delete-modal
	:form-action="current_form_action"
	id="remove_modal"
	modal-title="Remove Document"
	:delete-name="current_delete_name"
	@close="reinitializeValues()">
	@csrf
	@method('DELETE')
</delete-modal>

<a href="{{ url()->previous() }}" class="ui grey right floated mini button">Back</a>
<a href="{{ url("documents/$document->document_id/edit") }}" class="ui yellow right floated mini button">Edit</a>
@role('administrator')
	<a @click="changeCurrentRemove('{{ url("documents/$document->document_id") }}', '{{ $document->name }}')" href="#" class="ui red right floated mini button">
		Delete
	</a>
@endrole

<br>

<info-divider>Document</info-divider>

<div class="ui stackable centered grid">
	<header-data-view class="nine wide">
		Name:
		<template slot="value">
			{{ $document->name }}
		</template>
	</header-data-view>

	<header-data-view class="six wide">
		Tags:
		<template slot="value">
			{{ $document->keywords }}
		</template>
	</header-data-view>

	<header-data-view class="fifteen wide">
		Stored at:
		<template slot="value">
			{{ getEmployeeFolder($employee, $document->custom_folder_path, $document->name, null, false) }}
		</template>
	</header-data-view>
</div>

<br>

<info-divider>Pages</info-divider>

<div class="ui stackable centered grid">
	<div class="fifteen wide column">
		<div class="ui divided items">
			@foreach($document->document_contents as $content)
				<page-item
					:page-number="{{ $content->page_number }}"
					src="{{ $page_url . $pages[$loop->index]['id'] }}"
					file-name="{{ $content->file_name }}"
					page-id="{{ $pages[$loop->index]['id'] }}"
					@clicked-page="togglePageViewer(true, $event)">
				</page-item>
			@endforeach
		</div>
	</div>
</div>

@endsection

@section('vue_options')

<script src="{{ mix('/js/show_document/options.js') }}"></script>

@endsection